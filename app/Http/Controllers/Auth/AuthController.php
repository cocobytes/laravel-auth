<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\JWT;
use App\Models\Usuarios;
use App\Models\UsuariosAccesoToken;
use App\Models\VerificacionCorreos;

class AuthController extends Controller
{
    protected $TIME_VERIFY_EMAIL_SECONDS = 120; // por defecto 2min
    protected $TIME_SIGNIN_SECONDS = 259200; // por defecto 3dias
    protected $APP_SECRET_KEY;

    public function __construct()
    {
        $this->APP_SECRET_KEY = env('APP_SECRET_KEY');
    }

    public function signup(AuthRequest $req) {
        $data = $req->validated();

        DB::beginTransaction();
        try {
            // Creacion del usuario
            $user = Usuarios::create([
                'nombre' => $data['nombre'],
                'correo_electronico' => $data['correo_electronico'],
                'contrasena' => Hash::make($data['contrasena'])
            ]);

            // Crear JWT de acceso para verificar el correo
            $jwt = JWT::encode([
                'correo_electronico' => $user->correo_electronico,
                'exp' => JWT::createExpire($this->TIME_VERIFY_EMAIL_SECONDS)
            ], $this->APP_SECRET_KEY);

            // Guardamos el JWT de correo
            VerificacionCorreos::create([
                'id' => hash('sha256', $jwt),
                'usuario_uuid' => $user->uuid,
                'correo_electronico' => $user->correo_electronico
            ]);

            // Enviamos el JWT al correo electronico
            Mail::send('emails.verifyEmail',['user' => $user, 'jwt' => $jwt], function($msg) use($user) {
                $msg->to($user->correo_electronico, $user->nombre)
                    ->subject('Verificación de correo electronico');
            });

            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Usuario creado correctamente',
                'errors' => null,
                'data' => null
            ],201);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Hubo un error en el servidor',
                'errors' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }

    public function signin(AuthRequest $req) {
        $data = $req->validated();

        DB::beginTransaction();
        try {
            $isUser = Usuarios::where('correo_electronico','=', $data['correo_electronico'])->first();

            if(!is_null($isUser) && Hash::check($data['contrasena'], $isUser->contrasena)){
                if($isUser->correo_verificado == 1){
                    // Crear JWT de acceso para verificar el correo
                    $jwt = JWT::encode([
                        'correo_electronico' => $isUser->ccorreo_electronicoo,
                        'nombre' => $isUser->nombre,
                        'id' => $isUser->uuid,
                        'exp' => JWT::createExpire($this->TIME_SIGNIN_SECONDS)
                    ], $this->APP_SECRET_KEY);

                    // Guardamos el el acceso de JWT
                    UsuariosAccesoToken::create([
                        'id' => hash('sha256', $jwt),
                        'usuario_uuid' => $isUser->uuid,
                        'scoopes' => "['invitado']"
                    ]);

                    DB::commit();

                    return response()->json([
                        'status' => 200,
                        'message' => 'El usuario a iniciado sesión correctamente.',
                        'errors' => null,
                        'data' => $jwt
                    ],200);
                }else{
                    return response()->json([
                        'status' => 400,
                        'message' => 'El correo electronico no se encuentra verificado, Por favor confirmar el correo electronico.',
                        'errors' => null,
                        'data' => null
                    ],400);
                }
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'Las credenciales no coinciden, Por favor vuelva a intentar.',
                    'errors' => null,
                    'data' => null
                ],400);
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Hubo un error en el servidor',
                'errors' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }
}

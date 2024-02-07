<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\PasswordRequest;
use App\Models\RestablecerContrasena;
use App\Services\JWT;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    protected $TIME_VERIFY_EMAIL_SECONDS = 120; // por defecto 2min
    protected $APP_SECRET_KEY;

    public function __construct()
    {
        $this->APP_SECRET_KEY = env('APP_SECRET_KEY');
    }

    public function forgotPassword(PasswordRequest $req) {
        $data = $req->validated();

        DB::beginTransaction();
        try {
            $user = Usuarios::where('correo_electronico','=', $data['correo_electronico'])->first();

            if(!is_null($user)){
                // Crear JWT de acceso para verificar el correo
                $jwt = JWT::encode([
                    'correo_electronico' => $user->correo_electronico,
                    'exp' => JWT::createExpire($this->TIME_VERIFY_EMAIL_SECONDS)
                ], $this->APP_SECRET_KEY);

                // Guardamos el JWT de correo
                RestablecerContrasena::create([
                    'id' => hash('sha256', $jwt),
                    'usuario_uuid' => $user->uuid,
                    'correo_electronico' => $user->correo_electronico
                ]);

                // Enviamos el JWT al correo electronico
                Mail::send('emails.resetPassword',['user' => $user, 'jwt' => $jwt], function($msg) use($user) {
                    $msg->to($user->correo_electronico, $user->nombre)
                        ->subject('Restablecer contraseña');
                });

                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'El correo para restablecer contraseña a sido enviado correctamente',
                    'errors' => null,
                    'data' => null
                ],200);
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'El correo electronico no se encuentra disponible',
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

    public function confirmForgotPassword(PasswordRequest $req) {
        $data = $req->validated();

        DB::beginTransaction();

        try {
            // Trear el payload del JWT
            $payload = JWT::decode($data['jwt'], $this->APP_SECRET_KEY);

            // HASH Para crear el identificador unico de la bd
            $identifier = hash('sha256', $data['jwt']);

            // Trear el usuario con el JWT
            $isIdentifier = RestablecerContrasena::where('id','=', $identifier)
                        ->where('correo_electronico','=',$payload->correo_electronico)
                        ->first();

            // Si existe ese JWT del usuario
            if(!is_null($isIdentifier)){
                // Treaer el usuario para verificar el correo electronico y que sea el unico
                $isUser = Usuarios::where('uuid','=', $isIdentifier->usuario_uuid)->first();

                // Sl usuario es el originario del JWT actualizar los campos
                if(!is_null($isUser) && $isIdentifier->correo_electronico === $isUser->correo_electronico){
                    // Verificar si el token no a expirado
                    if(!JWT::expire($data['jwt'], $this->APP_SECRET_KEY)){
                        Usuarios::where('uuid','=', $isIdentifier->usuario_uuid)
                                ->update([ 'contrasena' => Hash::make($data['contrasena']) ]);

                            RestablecerContrasena::where('id','=', $identifier)
                                ->where('correo_electronico','=',$payload->correo_electronico)
                                ->where('usuario_uuid','=',$isUser->uuid)
                                ->update([ 'revocado' => true ]);

                        DB::commit();

                        return response()->json([
                            'status' => 200,
                            'message' => 'Usuario a recuperado la contraseña correctamente',
                            'errors' => null,
                            'data' => null
                        ],200);
                    }else{

                        RestablecerContrasena::where('id','=', $identifier)
                                ->where('correo_electronico','=',$payload->correo_electronico)
                                ->where('usuario_uuid','=',$isUser->uuid)
                                ->update([ 'revocado' => true ]);

                        DB::commit();

                        return response()->json([
                            'status' => 401,
                            'message' => 'El token del usuario es expirado',
                            'errors' => null,
                            'data' => null
                        ],401);
                    }
                }else{
                    return response()->json([
                        'status' => 401,
                        'message' => 'El token del usuario es invalido',
                        'errors' => null,
                        'data' => null
                    ],401);
                }
            }else{
                return response()->json([
                    'status' => 401,
                    'message' => 'El token del usuario es invalido',
                    'errors' => null,
                    'data' => null
                ],401);
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

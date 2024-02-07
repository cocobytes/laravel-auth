<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $method = $this->route()->getActionMethod();

        switch ($method) {
            case 'signup':
                return [
                    'nombre' => 'required|string|max:100',
                    'correo_electronico' => 'required|string|max:50|unique:usuarios,correo_electronico|email',
                    'contrasena' => 'required|string|max:100|min:4|confirmed:contrasena_confirmation',
                    'contrasena_confirmation' => 'required|string|max:100|min:4'
                ];

            case 'signin':
                return [
                    'correo_electronico' => 'required|string|max:50|email',
                    'contrasena' => 'required|string|max:100|min:4',
                ];

            case 'verifyEmail':
                return [
                    'jwt' => 'required|string'
                ];

            case 'resendVerifyEmail':
                return [
                    'correo_electronico' => 'required|string'
                ];
            default:
                break;
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de validación',
            'errors' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}

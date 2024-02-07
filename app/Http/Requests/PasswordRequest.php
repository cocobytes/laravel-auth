<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;


class PasswordRequest extends FormRequest
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
            case 'forgotPassword':
                return [
                    'correo_electronico' => 'required|string|email'
                ];

            case 'confirmForgotPassword':
                return [
                    'jwt' => 'required|string',
                    'contrasena' => 'required|string|confirmed::contrasena_confirmation',
                    'contrasena_confirmation' => 'required|string'
                ];

            case 'resetPassword':
                return [
                    'contrasena' => 'required|string|confirmed::contrasena_confirmation',
                    'contrasena_confirmation' => 'required|string'
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

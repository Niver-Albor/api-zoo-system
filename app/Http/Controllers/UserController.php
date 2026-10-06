<?php

namespace App\Http\Controllers;

use App\Business\Services\EncryptorService;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ApiResponse;

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password))
            {
                return $this->errorResponse('Credenciales invalidas', Response::HTTP_UNAUTHORIZED);
            }
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return $this->successResponse(
            [
                'user'          => $user->toArray,
                'access_token'  => $token,
                'token_type' => 'Bearer'
            ],
            'Inicio de Sesión exitoso!',
            Response::HTTP_OK
        );
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create');
    }

    public function update(FormRequest $request, User $user)
    {
        $this->authorize('update', $user);
    }
}

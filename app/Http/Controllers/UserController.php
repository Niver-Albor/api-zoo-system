<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ApiResponse;

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (!Auth::attempt($credentials))
            {
                return $this->errorResponse('Credenciales invalidas', Response::HTTP_UNAUTHORIZED);
            }
        
        $user = Auth::user();

        // Regenera la Cookie de sesión.
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
        // Crea el token de Sanctum y lo guarda
        $deviceName = $request->input('device_name', $request->userAgent() ?? 'unknown_device');
        $token = $user->createToken($deviceName)->plainTextToken;
        
        return $this->successResponse(
            [
                'user'          => $user,
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

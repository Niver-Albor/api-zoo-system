<?php

namespace App\Http\Controllers;

use App\Business\Entities\UserEntity;
use App\Business\Services\CreateUserService;
use App\Business\Services\UpdateUserService;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CreateUserService $createUserService,
        protected UpdateUserService $updateUserService
    ) {}

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

    public function logout(Request $request)
    {
        // Revisa si la petición tiene un token de autenticación y lo elimina.
        if ($request->user()->currentAccessToken() instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $request->user()->currentAccessToken()->delete();
        }
        // Si la petición es de un cliente web (cookies), elimina la sesión.
        if ($request->hasSession()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->successResponse(
            data: null,
            message: 'Sesión cerrada exitosamente'
        );
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $responseUser = $this->createUserService->createUser($request->validated());

        if ($responseUser)
            {
                return $this->successResponse(
                    new UserResource($responseUser), 
                    'Usuario registrado correctamente', 
                    Response::HTTP_CREATED
                    );
            }
        
        return $this->errorResponse();
    }

    public function who(Request $request, string $token)
    {
        $user = User::where('uuid', $token)->first();

        if ($user)
            {
                return $this->successResponse($user->toArray());
            }
        
        return $this->successResponse($request->user(), 'Datos del usuario actual');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $responseUser = $this->updateUserService->updateUser($user->id, $request->validated());

        if ($responseUser)
            {
                return $this->successResponse(new UserResource($responseUser), 'Usuario actualizado existosamente');
            }
        
        return $this->errorResponse();
    }
}

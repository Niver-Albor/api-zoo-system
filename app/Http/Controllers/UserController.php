<?php

namespace App\Http\Controllers;

use App\Business\DTOs\SaveUserData;
use App\Business\Services\CreateUserService;
use App\Business\Services\DeleteUserService;
use App\Business\Services\IndexUserService;
use App\Business\Services\UpdateUserService;
use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\UserResource;
use Illuminate\Foundation\Http\FormRequest;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CreateUserService $createUserService,
        protected UpdateUserService $updateUserService,
        protected DeleteUserService $deleteUserService,
        protected IndexUserService $indexUserService
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

        $dto = SaveUserData::fromRequest($request);

        $responseUser = $this->createUserService->createUser($dto);

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

    public function show(FormRequest $request, User $user)
    {
        $this->authorize('view', $user);

        $user->load('roles');

        if ($user)
            {
                return $this->successResponse(new UserResource($user));
            }
        
        return $this->successResponse(new UserResource($request->user()), 'Datos del usuario actual');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $dto = SaveUserData::fromRequest($request);

        $responseUser = $this->updateUserService->updateUser($user, $dto);

        if ($responseUser)
            {
                return $this->successResponse(new UserResource($responseUser), 'Usuario actualizado existosamente');
            }
        
        return $this->errorResponse();
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $deleted = $this->deleteUserService->deleteUser($user);

        if ($deleted)
            {
                return $this->successResponse(null, 'Usuario eliminado correctamente');
            }
        
        return $this->errorResponse('No se ha eliminado el usuario');
    }

    public function index(IndexUserRequest $request)
    {
        $filters = $request->validated();

        $animals = $this->indexUserService->indexUser($filters);

        return UserResource::collection($animals); // No utilizo el Trait acá porque después en la respuesta la anidación es asquerosa.
    }
}

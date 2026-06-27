<?php

namespace App\Infrastructure\Http\Controllers\Api;

use App\Application\Responses\ApiResponseTrait;
use App\Application\User\DTOs\RegisterDTO;
use App\Application\User\DTOs\LoginDTO;
use App\Application\User\DTOs\RecoverPasswordDTO;
use App\Application\User\UseCases\RegisterUser;
use App\Application\User\UseCases\AuthenticateUser;
use App\Application\User\UseCases\RecoverPassword;
use App\Infrastructure\Http\Controllers\Controller;
use App\Infrastructure\User\Persistence\Models\UserModel;
use App\Domain\User\ValueObjects\Role;
use App\Domain\User\Exceptions\InvalidCredentialsException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;
class UserController extends Controller
{
    use ApiResponseTrait;
    public function register(Request $request, RegisterUser $registerUser): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|in:admin,editor,user',
        ]);

        $role = isset($validated['role']) ? Role::from($validated['role']) : Role::USER;

        $dto = new RegisterDTO(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            role: Role::USER
        );

        $user = $registerUser->execute($dto);

        $userModel = UserModel::find($user->getId());
        $token = $userModel->createToken('api_token')->plainTextToken;

        return $this->successResponse([
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => (string) $user->getEmail(),
                'roles' => array_map(fn($r) => $r->value, $user->getRoles()),
            ],
            'token' => $token,
        ], 'User registered successfully', 201);

    }

    public function login(Request $request, AuthenticateUser $authenticateUser): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $dto = new LoginDTO($validated['email'], $validated['password']);
            $user = $authenticateUser->execute($dto);

            $userModel = UserModel::find($user->getId());
            $token = $userModel->createToken('api_token')->plainTextToken;

            return $this->successResponse([
                'user' => [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => (string) $user->getEmail(),
                    'roles' => array_map(fn($r) => $r->value, $user->getRoles()),
                ],
                'token' => $token,
            ], 'Login successful');
        } catch (InvalidCredentialsException $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    public function recoverPassword(Request $request, RecoverPassword $recoverPassword): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $dto = new RecoverPasswordDTO($validated['email']);
        $recoverPassword->execute($dto);

        return $this->successResponse(
            null,
            'If your email is registered in our system, you will receive a password recovery instructions shortly.'
        );
    }
}
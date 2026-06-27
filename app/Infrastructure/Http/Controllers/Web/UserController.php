<?php

namespace App\Infrastructure\Http\Controllers\Web;

use App\Application\User\DTOs\RegisterDTO;
use App\Application\User\DTOs\RecoverPasswordDTO;
use App\Application\User\UseCases\RegisterUser;
use App\Application\User\UseCases\RecoverPassword;
use App\Infrastructure\Http\Controllers\Controller;
use App\Domain\User\ValueObjects\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request, RegisterUser $registerUser): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $dto = new RegisterDTO(
                name: $validated['name'],
                email: $validated['email'],
                password: $validated['password'],
                role: Role::USER
            );

            $user = $registerUser->execute($dto);

            Auth::loginUsingId($user->getId());

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => $e->getMessage(),
            ])->onlyInput('email');
        }
    }

    public function recoverPassword(Request $request, RecoverPassword $recoverPassword): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $dto = new RecoverPasswordDTO($validated['email']);
        $recoverPassword->execute($dto);

        return back()->with('status', 'If your email is registered, recovery instructions have been sent.');
    }
}
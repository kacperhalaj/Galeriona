<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        // Walidacja e-maila
        $request->validate([
            'email' => 'required|email'
        ]);

        // Znalezienie użytkownika
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Nie znaleziono użytkownika o takim adresie e-mail.']);
        }

        // Wygenerowanie tokenu resetującego
        $token = Password::getRepository()->create($user);

        // Zwrócenie go do widoku w sesji
        return back()->with([
            'status' => 'Token resetu hasła został wygenerowany.',
            'reset_token' => $token
        ]);
    }
}

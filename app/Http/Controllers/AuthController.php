<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SellerDescription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Google2FA;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    /**
     * Obsługa próby logowania.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->filled('remember'); // Zapamiętaj stan "remember me"

        // Spróbuj uwierzytelnić użytkownika
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user(); // Pobierz uwierzytelnionego użytkownika

            // Sprawdź, czy 2FA jest włączone dla tego użytkownika i czy rola to 'user' lub 'seller'
            if ($user->google2fa_enabled && in_array($user->role, ['user', 'seller'])) {
                // Zapisz niezbędne informacje w sesji do weryfikacji 2FA
                $request->session()->put('2fa_user_id', $user->id);
                $request->session()->put('2fa_remember', $remember); // Zapisz stan "remember me"

                Auth::logout(); // Tymczasowo wyloguj użytkownika
                $request->session()->save(); // Upewnij się, że zmiany w sesji są zapisane

                return redirect()->route('totp.login.form'); // Przekieruj do formularza TOTP
            }

            // Jeśli 2FA nie jest włączone lub nie dotyczy, kontynuuj normalne logowanie
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Podane dane są nieprawidłowe.',
        ])->onlyInput('email');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function registerPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'house_number' => 'required|string|max:20',
            'apartment_number' => 'nullable|string|max:20',
            'role' => 'required|in:user,seller',
            'setup_totp' => 'nullable|boolean',
            'seller_description' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('role') === 'seller' && (empty($value) || strlen($value) > 255)) {
                        $fail('Opis sprzedawcy jest wymagany i nie może przekraczać 255 znaków.');
                    }
                }
            ],
            'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                $response = Http::withoutVerifying()->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
                if (!$response->json('success')) {
                    $fail('Weryfikacja reCAPTCHA nie powiodła się. Spróbuj ponownie.');
                }
            }],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $setupTotp = isset($validated['setup_totp']) && $validated['setup_totp'] && in_array($validated['role'], ['user', 'seller']);
        $google2fa_secret = null;

        if ($setupTotp) {
            $google2fa_secret = Google2FA::generateSecretKey();
        }

        $user = User::create([
            'username' => $validated['username'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'google2fa_secret' => $google2fa_secret,
            'google2fa_enabled' => false,
        ]);

        $user->addresses()->create([
            'city' => $validated['city'],
            'postal_code' => $validated['postal_code'],
            'street' => $validated['street'],
            'house_number' => $validated['house_number'],
            'apartment_number' => $validated['apartment_number'] ?? null,
        ]);

        // Zapisz opis sprzedawcy, jeśli rola to seller
        if ($user->role === 'seller') {
            SellerDescription::create([
                'user_id' => $user->id,
                'short_description' => $request->input('seller_description'),
            ]);
        }

        Auth::login($user);

        if ($setupTotp) {
            $request->session()->put('google2fa_secret', $google2fa_secret);
            return redirect()->route('totp.setup');
        }

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    // Wyświetlenie strony konfiguracji TOTP
    public function showTotpSetup(Request $request)
    {
        $user = Auth::user();

        if ($user->google2fa_enabled) {
            return redirect()->route('home')->with('error', 'Uwierzytelnianie dwuskładnikowe jest już włączone.');
        }


        $google2fa_secret = $request->session()->get('google2fa_secret');

        if (!$google2fa_secret && !$user->google2fa_secret) {
            $google2fa_secret = Google2FA::generateSecretKey(); 
            $user->google2fa_secret = $google2fa_secret;
            $user->save();
            $request->session()->put('google2fa_secret', $google2fa_secret);
        } elseif ($user->google2fa_secret && !$google2fa_secret) {
            $google2fa_secret = $user->google2fa_secret;
            $request->session()->put('google2fa_secret', $google2fa_secret);
        }

        if (!$google2fa_secret) {
             return redirect()->route('client.panel')->with('error', 'Nie można zainicjować konfiguracji TOTP. Brak sekretu.');
        }


        $otpAuthUrl = Google2FA::getQRCodeUrl(
            config('app.name'),
            $user->email,
            $google2fa_secret
        );

        return view('auth.totp_setup', [
            'otpAuthUrl' => $otpAuthUrl,
            'google2fa_secret' => $google2fa_secret
        ]);
    }

    // Weryfikuj i włącz TOTP
    public function verifyTotpSetup(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $user = Auth::user();
        $google2fa_secret = $request->session()->get('google2fa_secret', $user->google2fa_secret);

        if (!$google2fa_secret) {
            return redirect()->route('totp.setup')->with('error', 'Sesja wygasła lub brak sekretu TOTP. Spróbuj ponownie.');
        }

        $valid = Google2FA::verifyKey($google2fa_secret, $request->input('one_time_password'));

        if ($valid) {
            $user->google2fa_secret = $google2fa_secret;
            $user->google2fa_enabled = true;
            $user->save();
            $request->session()->forget('google2fa_secret');
            return redirect()->route('home')->with('success', 'Uwierzytelnianie dwuskładnikowe zostało pomyślnie włączone!');
        } else {
            return redirect()->route('totp.setup')->with('error', 'Nieprawidłowy kod TOTP. Spróbuj ponownie.');
        }
    }

    // Włącz TOTP z profilu użytkownika
    public function enableTotp(Request $request)
    {
        $user = Auth::user();
        if ($user->google2fa_enabled) {
            return redirect()->back()->with('error', 'Uwierzytelnianie dwuskładnikowe jest już włączone.');
        }


        $google2fa_secret = Google2FA::generateSecretKey();
        $user->google2fa_secret = $google2fa_secret;
        $user->google2fa_enabled = false;
        $user->save();

        $request->session()->put('google2fa_secret', $google2fa_secret);
        return redirect()->route('totp.setup');
    }

    // Wyłącz TOTP z profilu użytkownika
    public function disableTotp(Request $request)
    {
        $user = Auth::user();
        $user->google2fa_enabled = false;
        $user->save();
        return redirect()->back()->with('success', 'Uwierzytelnianie dwuskładnikowe zostało wyłączone.');
    }

    // Wyświetlenie formularza logowania TOTP
    public function showTotpLoginForm(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.totp_login');
    }

    // Weryfikacja kodu TOTP podczas logowania
    public function verifyTotpLogin(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Sesja wygasła. Zaloguj się ponownie.');
        }

        $user = User::find($userId);
        if (!$user || !$user->google2fa_enabled || !$user->google2fa_secret) {

            $request->session()->forget(['2fa_user_id', '2fa_remember']);
            return redirect()->route('login')->with('error', 'Konfiguracja 2FA nie jest kompletna lub jest wyłączona dla tego użytkownika.');
        }

        $valid = Google2FA::verifyKey($user->google2fa_secret, $request->input('one_time_password')); 

        if ($valid) {
            Auth::login($user, $request->session()->get('2fa_remember', false)); // Zaloguj użytkownika

            $request->session()->forget(['2fa_user_id', '2fa_remember']);
            $request->session()->regenerate(); // Zregeneruj ID sesji

            return redirect()->route('home')->with('success', 'Zalogowano pomyślnie!'); 
        } else {
            return redirect()->route('totp.login.form')->with('error', 'Nieprawidłowy kod TOTP. Spróbuj ponownie.');
        }
    }
}

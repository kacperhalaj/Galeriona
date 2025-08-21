<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja – Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .brand-logo {
            color: #667eea;
            transition: color 0.3s ease;
        }

        .brand-logo:hover {
            color: #764ba2;
            text-decoration: none;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .text-brand {
            color: #667eea !important;
        }

        .text-brand:hover {
            color: #764ba2 !important;
        }

        .step-indicator {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="min-vh-100">
    <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100 py-4">
        <div class="card register-card shadow-lg border-0 rounded-4 p-4" style="width: 100%; max-width: 600px;">

            <!-- logo -->
            <div class="text-center mb-4">
                <a href="/" class="brand-logo text-decoration-none fs-2 fw-bold">
                    <i class="fas fa-palette me-2"></i>Galeriona
                </a>
            </div>

            <h2 class="text-center fw-bold text-dark mb-2">Utwórz konto</h2>
            @if(request('role') === 'seller')
                <div class="alert alert-info text-center mt-2 mb-4">
                    <i class="fas fa-store me-2"></i>Kilka kroków dzieli Cię od zostania sprzedawcą!
                </div>
            @endif

            <!-- Kroki postępu -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="step-indicator text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                <small class="fw-bold">1</small>
                            </div>
                            <small class="text-brand fw-semibold">Dane osobowe</small>
                        </div>
                        <div class="flex-grow-1 mx-3">
                            <hr class="border-2 border-secondary">
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                <small class="fw-bold">2</small>
                            </div>
                            <small class="text-secondary">Adres</small>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf


                <input type="hidden" name="role" value="{{ request('role', 'user') }}">

                {{-- Step 1: Dane osobowe --}}
                <div id="step1">
                    <h5 class="text-primary mb-3"><i class="fas fa-user-circle me-2"></i>Dane osobowe i logowania</h5>
                    {{-- Nazwa uzytkownika --}}
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold text-secondary">
                            <i class="fas fa-at me-2"></i>Nazwa użytkownika
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-at text-brand"></i>
                            </span>
                            <input type="text" name="username" id="username"
                                   class="form-control border-start-0 rounded-end @error('username') is-invalid @enderror"
                                   placeholder="Wprowadź nazwę użytkownika" value="{{ old('username') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Imię --}}
                    <div class="mb-3">
                        <label for="first_name" class="form-label fw-semibold text-secondary">
                            <i class="fas fa-user me-2"></i>Imię
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-user text-brand"></i>
                            </span>
                            <input type="text" name="first_name" id="first_name"
                                   class="form-control border-start-0 rounded-end"
                                   placeholder="Wprowadź swoje imię" value="{{ old('first_name') }}" required>
                        </div>
                    </div>

                    {{-- Nazwisko --}}
                    <div class="mb-3">
                        <label for="last_name" class="form-label fw-semibold text-secondary">
                            <i class="fas fa-user me-2"></i>Nazwisko
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-user text-brand"></i>
                            </span>
                            <input type="text" name="last_name" id="last_name"
                                   class="form-control border-start-0 rounded-end"
                                   placeholder="Wprowadź swoje nazwisko" value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold text-secondary">
                            <i class="fas fa-envelope me-2"></i>Adres e-mail
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-envelope text-brand"></i>
                            </span>
                            <input type="email" name="email" id="email"
                                   class="form-control border-start-0 rounded-end @error('email') is-invalid @enderror"
                                   placeholder="Wprowadź swój adres e-mail" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>

                    {{-- Hasło --}}
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold text-secondary">
                            <i class="fas fa-lock me-2"></i>Hasło
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-brand"></i>
                            </span>
                            <input type="password" name="password" id="password"
                                   class="form-control border-start-0 rounded-end"
                                   placeholder="Wprowadź swoje hasło" required>
                        </div>
                    </div>

                    {{-- Potwierdzenie hasła --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold text-secondary">
                            <i class="fas fa-redo-alt me-2"></i>Potwierdź hasło
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-redo-alt text-brand"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control border-start-0 rounded-end"
                                   placeholder="Potwierdź swoje hasło" required>
                        </div>
                    </div>

                    {{-- TOTP Checkbox --}}
                    @if(in_array(request('role', 'user'), ['user', 'seller']))
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="setup_totp" id="setup_totp" value="1" {{ old('setup_totp') ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary" for="setup_totp">
                            Skonfiguruj uwierzytelnianie dwuskładnikowe (TOTP) teraz
                        </label>
                    </div>
                    @endif


                    <button type="button" onclick="nextStep()" class="btn btn-gradient text-white fw-semibold w-100 py-2 mb-3">
                        Dalej <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>

                <!-- Sekcja adresu -->
                <div id="step2" class="step-section d-none">
                    <h5 class="text-brand fw-semibold mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>Adres zamieszkania
                    </h5>

                    <div class="row g-3">
                        <!-- Miasto -->
                        <div class="col-md-6">
                            <label for="city" class="form-label fw-semibold text-secondary">
                                <i class="fas fa-city me-2"></i>Miasto *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-city text-brand"></i>
                                </span>
                                <input type="text" name="city" id="city"
                                       class="form-control border-start-0 rounded-end"
                                       placeholder="Nazwa miasta" required>
                            </div>
                        </div>

                        <!-- Kod pocztowy -->
                        <div class="col-md-6">
                            <label for="postal_code" class="form-label fw-semibold text-secondary">
                                <i class="fas fa-mail-bulk me-2"></i>Kod pocztowy *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-mail-bulk text-brand"></i>
                                </span>
                                <input type="text" name="postal_code" id="postal_code"
                                       class="form-control border-start-0 rounded-end"
                                       placeholder="00-000" pattern="[0-9]{2}-[0-9]{3}" required>
                            </div>
                        </div>

                        <!-- Ulica -->
                        <div class="col-12">
                            <label for="street" class="form-label fw-semibold text-secondary">
                                <i class="fas fa-road me-2"></i>Ulica *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-road text-brand"></i>
                                </span>
                                <input type="text" name="street" id="street"
                                       class="form-control border-start-0 rounded-end"
                                       placeholder="Nazwa ulicy" required>
                            </div>
                        </div>

                        <!-- Numer domu -->
                        <div class="col-md-6">
                            <label for="house_number" class="form-label fw-semibold text-secondary">
                                <i class="fas fa-home me-2"></i>Numer domu *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-home text-brand"></i>
                                </span>
                                <input type="text" name="house_number" id="house_number"
                                       class="form-control border-start-0 rounded-end"
                                       placeholder="123" required>
                            </div>
                        </div>

                        <!-- Numer mieszkania -->
                        <div class="col-md-6">
                            <label for="apartment_number" class="form-label fw-semibold text-secondary">
                                <i class="fas fa-door-open me-2"></i>Numer mieszkania
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-door-open text-brand"></i>
                                </span>
                                <input type="text" name="apartment_number" id="apartment_number"
                                       class="form-control border-start-0 rounded-end"
                                       placeholder="12 (opcjonalnie)">
                            </div>
                        </div>
                    </div>
                    @if(request('role') === 'seller')
    <div class="mb-3">
        <label for="seller_description" class="form-label fw-semibold text-secondary">
            <i class="fas fa-info-circle me-2"></i>Opis sprzedawcy
        </label>
        <textarea name="seller_description" id="seller_description"
                  class="form-control border-start-0 rounded-end @error('seller_description') is-invalid @enderror"
                  placeholder="Opisz siebie i swoją twórczość" rows="3" required>{{ old('seller_description') }}</textarea>
        @error('seller_description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
@endif
                    <!-- reCAPTCHA -->
                    <div class="col-12 mt-4 mb-3">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>

                    @error('g-recaptcha-response')
                        <div class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</div>
                    @enderror

                    </div>

                    <!-- Przyciski -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary fw-semibold px-4" onclick="prevStep()">
                            <i class="fas fa-arrow-left me-2"></i>Wstecz
                        </button>
                        <button type="submit" class="btn btn-gradient text-white fw-semibold px-4">
                            <i class="fas fa-user-plus me-2"></i>Utwórz konto
                        </button>
                    </div>
                </div>

                @php
                    $role = request('role', 'user');
                @endphp
                <input type="hidden" name="role" value="{{ $role }}">

            </form>

            <!-- Login link -->
            <div class="text-center mt-4">
                <p class="mb-0 text-secondary">
                    Masz już konto?
                    <a href="/login" class="text-brand text-decoration-none fw-semibold">
                        <i class="fas fa-sign-in-alt me-1"></i>Zaloguj się
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function nextStep() {
            // Walidacja pól w kroku 1
            const step1Fields = document.querySelectorAll('#step1 input[required]');
            let isValid = true;

            step1Fields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Sprwadzenie, czy hasła są zgodne
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;

            if (password !== confirmPassword) {
                document.getElementById('password_confirmation').classList.add('is-invalid');
                isValid = false;
            }

            if (isValid) {
                document.getElementById('step1').classList.add('d-none');
                document.getElementById('step2').classList.remove('d-none');

                // Aktualizacja wskaźników postępu
                document.querySelector('.step-indicator').classList.remove('step-indicator');
                document.querySelector('.step-indicator').classList.add('bg-success');
                document.querySelectorAll('.d-flex.align-items-center')[1].querySelector('div').classList.remove('bg-light', 'text-secondary');
                document.querySelectorAll('.d-flex.align-items-center')[1].querySelector('div').classList.add('step-indicator', 'text-white');
                document.querySelectorAll('.d-flex.align-items-center')[1].querySelector('small').classList.remove('text-secondary');
                document.querySelectorAll('.d-flex.align-items-center')[1].querySelector('small').classList.add('text-brand', 'fw-semibold');
            }
        }

        function prevStep() {
            document.getElementById('step2').classList.add('d-none');
            document.getElementById('step1').classList.remove('d-none');

            // Resrtowanie wskaźników postępu
            document.querySelector('.bg-success').classList.remove('bg-success');
            document.querySelector('.bg-success').classList.add('step-indicator');
            document.querySelectorAll('.d-flex.align-items-center')[1].querySelector('div').classList.remove('step-indicator', 'text-white');
            document.querySelectorAll('.d-flex.align-items-center')[1].querySelector('div').classList.add('bg-light', 'text-secondary');
            document.querySelectorAll('.d-flex.align-items-center')[1].querySelector('small').classList.remove('text-brand', 'fw-semibold');
            document.querySelectorAll('.d-flex.align-items-center')[1].querySelector('small').classList.add('text-secondary');
        }

        // Walidacja pól formularza
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            if (password !== confirmPassword && confirmPassword.length > 0) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        // Kod pocztowy - formatowanie
        document.getElementById('postal_code').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '-' + value.substring(2, 5);
            }
            this.value = value;
        });

        // Walidacja reCAPTCHA przed wysłaniem formularza
        const registrationForm = document.querySelector('form[method="POST"]');
        if (registrationForm) {
            registrationForm.addEventListener('submit', function(event) {
                const recaptchaResponse = grecaptcha.getResponse();
                const step2Section = document.getElementById('step2');
                // Sprawdzamy, czy drugi krok jest aktywny
                if (!step2Section.classList.contains('d-none')) {
                    if (recaptchaResponse.length === 0) {
                        event.preventDefault(); // Zatrzymaj wysyłanie formularza
                        const recaptchaError = document.getElementById('recaptcha-error');
                        if(recaptchaError) recaptchaError.classList.remove('d-none');
                    } else {
                        const recaptchaError = document.getElementById('recaptcha-error');
                        if(recaptchaError) recaptchaError.classList.add('d-none');
                    }
                }
            });
        }
    </script>
</body>
</html>

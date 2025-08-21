<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfiguracja Uwierzytelniania Dwuskładnikowego - Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .setup-card {
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
        .qr-code-container {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            display: block;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .secret-key-display {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            font-family: monospace;
            word-break: break-all;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body class="min-vh-100">
    <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100 py-4">
        <div class="card setup-card shadow-lg border-0 rounded-4 p-4 p-md-5" style="width: 100%; max-width: 550px;">

            <div class="text-center mb-4">
                <a href="/" class="brand-logo text-decoration-none fs-2 fw-bold">
                    <i class="fas fa-palette me-2"></i>Galeriona
                </a>
            </div>

            <h2 class="text-center fw-bold text-dark mb-3">Konfiguracja 2FA</h2>
            <p class="text-center text-secondary mb-4">
                Zeskanuj poniższy kod QR za pomocą aplikacji uwierzytelniającej (np. Google Authenticator, Authy)
                lub ręcznie wprowadź klucz.
            </p>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="text-center mb-4 qr-code-container">
                <div id="qrcode"></div> 
            </div>

            <div class="mb-4">
                <p class="text-center text-secondary mb-2">Lub wprowadź ten klucz ręcznie:</p>
                <div class="secret-key-display text-center">
                    <code>{{ $google2fa_secret }}</code>
                </div>
            </div>

            <form method="POST" action="{{ route('totp.verify') }}">
                @csrf
                <div class="mb-3">
                    <label for="one_time_password" class="form-label fw-semibold text-secondary">
                        <i class="fas fa-key me-2"></i>Jednorazowy kod
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-key text-brand"></i>
                        </span>
                        <input type="text" name="one_time_password" id="one_time_password"
                               class="form-control border-start-0 rounded-end @error('one_time_password') is-invalid @enderror"
                               placeholder="Wprowadź kod z aplikacji" required autofocus
                               inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code">
                        @error('one_time_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-gradient text-white fw-semibold w-100 py-2 mb-3">
                    <i class="fas fa-check-circle me-2"></i>Weryfikuj i Włącz 2FA
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="text-brand text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Wróć do strony głównej (bez włączania 2FA)
                </a>
            </div>
             <div class="text-center mt-2">
                <small class="text-muted">Jeśli nie chcesz teraz konfigurować 2FA, możesz to zrobić później w ustawieniach swojego profilu.</small>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        // Generowanie kodu QR po stronie klienta
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $otpAuthUrl }}",
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>

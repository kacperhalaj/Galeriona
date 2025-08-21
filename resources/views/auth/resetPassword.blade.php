<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetowanie hasła – Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .login-card {
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
    </style>
</head>
<body class="min-vh-100">
    <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100 py-4">
        <div class="card login-card shadow-lg border-0 rounded-4 p-4" style="width: 100%; max-width: 450px;">

            <!-- Nazwa serwisu -->
            <div class="text-center mb-4">
                <a href="/" class="brand-logo text-decoration-none fs-2 fw-bold">
                    <i class="fas fa-palette me-2"></i>Galeriona
                </a>
            </div>

            <h2 class="text-center fw-bold text-dark mb-4">Przypomnij hasło</h2>

            <!-- Komunikat o sukcesie -->
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Wyświetlanie błędów walidacji -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Formularz -->
            <form method="POST" action="{{ route('token.email') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold text-secondary">
                        <i class="fas fa-envelope me-2"></i>Adres e-mail
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-envelope text-brand"></i>
                        </span>
                        <input type="email" name="email" id="email"
                               class="form-control border-start-0 rounded-end"
                               placeholder="Wprowadź swój e-mail" required autofocus>
                    </div>
                </div>

                <!-- Przycisk -->
                <button type="submit" class="btn btn-gradient text-white fw-semibold w-100 py-3">
                    <i class="fas fa-envelope me-2"></i>Generuj token resetu
                </button>
            </form>
        </div>
    </div>
    @if (session('reset_token'))
    <script>
        console.log("Reset token: {{ session('reset_token') }}");
    </script>
@endif
</body>
</html>

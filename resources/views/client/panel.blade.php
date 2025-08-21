<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Klienta - Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .chart-fixed {
            width: 100% !important;
            max-width: 700px;
            height: 340px !important;
            max-height: 340px;
            display: block;
            margin: 0 auto;
            background: #fff;
        }
    </style>
</head>

<body class="bg-light">

    @include('components.header')
    @include('client.components.header')

    <!-- Główna sekcja -->
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="mb-4 text-primary">
                            <i class="fas fa-user-cog me-2"></i>
                            Twoje działania
                        </h4>
                        <div class="row">
                            <div class="col-md-6">

                                <a href="{{ route('client.manage.update') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-user-edit me-2"></i>
                                    Edytuj moje dane
                                </a>
                                <a href="{{ route('client.purchases.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-shopping-bag me-2"></i>
                                    Moje zakupy
                                </a>
                                <a href="{{ route('client.followers.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-heart me-2"></i>
                                    Obserwowani artyści
                                </a>
                                <a href="{{ route('client.wallet.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-wallet me-2"></i>
                                    Mój portfel
                                </a>
                                <a href="{{ route('client.donations.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-hand-holding-heart me-2"></i>
                                    Wesprzyj sprzedawcę
                                </a>
                                <a href="{{ route('client.addresses.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Zarządzanie adresami
                                </a>
                                <a href="{{ route('client.statistics.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Statystyki
                                </a>


                            </div>
                                <div class="col-md-6">
                                {{-- Sekcja Zarządzania TOTP --}}
                                <div class="card shadow-sm border-0 mb-4">
                                    <div class="card-body">

                                        <h5 class="mb-3 text-primary"><i class="fas fa-shield-alt me-2"></i>Zarządzanie TOTP</h5>
                                        @if (Auth::user() && Auth::user()->google2fa_enabled)
                                            <p class="text-success"><i class="fas fa-check-circle me-1"></i>Uwierzytelnianie dwuskładnikowe (TOTP) jest aktywne.</p>

                                            <form action="{{ route('totp.disable') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger w-100">
                                                    <i class="fas fa-ban me-2"></i>Wyłącz TOTP
                                                </button>
                                            </form>
                                        @else
                                            <p class="text-muted"><i class="fas fa-times-circle me-1"></i>Uwierzytelnianie dwuskładnikowe (TOTP) jest nieaktywne.</p>

                                            <form action="{{ route('totp.enable') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="fas fa-user-shield me-2"></i>Włącz TOTP
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                {{-- Sekcja Poziomu Klienta --}}
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <h5 class="mb-3 text-primary">
                                            <i class="fas fa-star me-2"></i>Twój poziom klienta
                                        </h5>
                                        <p class="mb-2">
                                            <strong>Poziom:</strong> {{ Auth::user()->loyalty_level }}
                                        </p>
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ Auth::user()->loyalty_progress }}%;"
                                                aria-valuenow="{{ Auth::user()->loyalty_progress }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ Auth::user()->loyalty_progress }}%
                                            </div>
                                        </div>
                                        <small class="text-muted">Zrealizowane zamówienia: {{ Auth::user()->total_orders }}</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @yield('panel_content')
                        @yield('content')


                    </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

</body>
</html>

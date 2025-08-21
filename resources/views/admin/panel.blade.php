<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel Admina - Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    @include('components.header')
    @include('admin.components.header')

    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="mb-4 text-primary">
                            <i class="fas fa-cogs me-2"></i>
                            Zarządzanie systemem
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('admin.manage.users.index') }}"
                                    class="btn btn-outline-secondary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-users me-2"></i>
                                    Zarządzanie użytkownikami
                                </a>
                                <a href="{{ route('admin.addresses.index') }}"
                                    class="btn btn-outline-secondary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Zarządzanie adresami
                                </a>
                                <a href="{{ route('admin.manage.sellers.index') }}"
                                    class="btn btn-outline-secondary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-store me-2"></i>
                                    Zarządzanie sprzedawcami
                                </a>
                                <a href="{{ route('admin.artworks.index') }}"
                                    class="btn btn-outline-secondary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Zarządzanie dziełami
                                </a>
                                <a href="{{ route('admin.sales.index') }}"
                                    class="btn btn-outline-secondary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Zarządzanie zamówieniami
                                </a>
                                <a href="{{ route('admin.categories.index') }}"
                                    class="btn btn-outline-secondary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-th me-2"></i>
                                    Zarządzanie kategoriami
                                </a>
                                <a href="{{ route('admin.statistics.index') }}"
                                    class="btn btn-outline-secondary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Raporty i statystyki
                                </a>
                                <a href="{{ route('admin.profile.edit') }}"
                                    class="btn btn-outline-secondary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-user-cog me-2"></i>
                                    Edytuj swój profil
                                </a>
                            </div>
                        </div>

                        @yield('content')

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('currentDate').textContent = new Date().toLocaleDateString('pl-PL', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    </script>

</body>

</html>

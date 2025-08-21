<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Sprzedawcy - Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    @include('components.header')
    @include('seller.components.header')

    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="mb-4 text-primary">
                            <i class="fas fa-chart-line me-2"></i>
                            Twoje działania
                        </h4>
                        <div class="row">
                            <div class="col-md-6">

                                <a href="{{ route('seller.profile.edit') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-user-cog me-2"></i>
                                    Edytuj profil
                                </a>
                                
                                <a href="{{ route ('seller.artworks.create') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Dodaj nowe dzieło
                                </a>
                                <a href= "{{ route('seller.artworks.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-paint-brush me-2"></i>
                                    Zarządzanie swoimi dziełami
                                </a>
                                <a href="{{ route('seller.sales.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Historia sprzedaży
                                </a>
                                <a href="{{ route('seller.finances.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-dollar-sign me-2"></i>
                                    Moje finanse
                                </a>
                                <a href="#"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-star me-2"></i>
                                    Najlepsi fani
                                </a>
                                <a href="{{ route('seller.followers.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-users me-2"></i>
                                    Obserwujący
                                </a>
                                <a href="{{ route('seller.statistics.index') }}"
                                    class="btn btn-outline-primary w-100 mb-3 text-start d-flex align-items-center">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Statystyki sprzedaży
                                </a>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                @yield('content')
                            </div>
                        </div>
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

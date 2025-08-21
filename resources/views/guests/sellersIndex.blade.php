<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeriona - Przeglądaj Profile Sprzedawców</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 400px;
            display: flex;
            align-items: center;
            color: white;
        }

        .seller-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 100%;
        }

        .seller-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }

        .seller-avatar {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 3px solid #f8f9fa;
        }

        .seller-cover {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .rating-stars {
            color: #ffc107;
        }

        .seller-stats {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .filter-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .btn-filter {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }

        .btn-filter:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
        }

        .specialty-badge {
            font-size: 0.8rem;
            margin: 2px;
        }

        .search-results-header {
            border-bottom: 2px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    @include('components.header')
    @include('guests.components.herosection')

    <div class="container my-5">
        <!-- Nagłówek wyników -->
        <div class="search-results-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3>
                        @if(isset($sellers) && $sellers->count() > 0)
                            Znaleziono {{ $sellers->total() }} sprzedawców
                        @else
                            Wszyscy Sprzedawcy
                        @endif
                    </h3>
                </div>
                <div class="col-md-4 text-end">
                    <small class="text-muted">
                        {{ $sellers->firstItem() ?? 0 }}-{{ $sellers->lastItem() ?? 0 }} z {{ $sellers->total() ?? 0 }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Lista sprzedawców -->
        @if(isset($sellers) && $sellers->count() > 0)
            <div class="row">
                @foreach($sellers as $seller)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card seller-card p-3">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <strong class="fs-5">{{ $seller->username }}</strong>
                                </div>
                                @if($seller->sellerDescription && $seller->sellerDescription->short_description)
                                    <p class="card-text text-muted small mb-3">
                                        {{ Str::limit($seller->sellerDescription->short_description, 100) }}
                                    </p>
                                @endif
                                <div class="d-grid gap-2">
                                    <a href="{{ route('seller.profile.public', $seller->id) }}" class="btn btn-filter">
                                        <i class="fas fa-eye me-2"></i>Zobacz profil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginacja -->
            <div class="d-flex justify-content-center mt-5">
                {{ $sellers->links() }}
            </div>
        @else
            <!-- Brak wyników -->
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Nie znaleziono sprzedawców</h4>
            </div>
        @endif
    </div>

    @include('components.statystyki-platformy')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.search) {
                const resultsHeader = document.querySelector('.search-results-header');
                if (resultsHeader) {
                    window.scrollTo({
                        top: resultsHeader.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            }
        });
    </script>
</body>
</html>

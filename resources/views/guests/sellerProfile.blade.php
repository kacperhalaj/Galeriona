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
        }

        .artwork-card:hover {
            transform: translateY(-3px);
        }

        .card-img-top {
            height: 250px;
            object-fit: cover;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
    </style>
</head>

<body class="bg-light">

@include('components.header')
@include('guests.components.herosection')
    <div class="container my-5">
        <div class="bg-white rounded-4 shadow-lg p-4 p-md-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-primary mb-3">{{ $seller->username }}</h2>
                @if($seller->sellerDescription && $seller->sellerDescription->short_description)
                    <p class="lead text-muted">{{ $seller->sellerDescription->short_description }}</p>
                @endif
            </div>

            <div class="bg-light rounded-3 p-4">
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-primary">
                        <i class="fas fa-palette me-2"></i>Dzieła artysty
                    </h4>
                </div>

                <div class="row g-4">
                    @forelse($seller->artworks as $artwork)
                        <div class="col-lg-4 col-md-6">
                            <div class="card artwork-card h-100 shadow-sm border-0 transition-all">
                                @if($artwork->image_path)
                                    <img src="{{ asset($artwork->image_path) }}" class="card-img-top" alt="{{ $artwork->title }}">
                                @else
                                    <div class="card-img-top bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold text-primary">{{ $artwork->title }}</h5>
                                    <p class="card-text text-muted flex-grow-1">{{ $artwork->description }}</p>

                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="fw-bold fs-5 text-success">
                                            {{ number_format($artwork->price, 2) }} zł
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-palette display-1 text-muted opacity-50 mb-3"></i>
                                <h4 class="text-muted">Brak dzieł</h4>
                                <p class="text-muted">Ten artysta nie dodał jeszcze żadnych dzieł.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Płynne pojawianie się kart
            const cards = document.querySelectorAll('.artwork-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>

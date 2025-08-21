<section class="hero-section text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Odkryj niepowtarzalne dzieła sztuki</h1>
                <p class="lead mb-4">Największa platforma sprzedaży autentycznych dzieł sztuki. Od klasyki po nowoczesność.</p>
                <div class="d-flex gap-3 mb-4">
                    <button class="btn btn-light btn-lg">
                        <i class="fas fa-search me-2"></i>Przeglądaj kolekcje
                    </button>
                    <a href="{{ route('register', ['role' => 'seller']) }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-store me-2"></i>Zostań sprzedawcą
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="{{ asset('images/cat1Carousel.jpg') }}" class="d-block w-100 same-size-img" alt="Dzieło sztuki">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('images/painting1Carousel.jpg') }}" class="d-block w-100 same-size-img" alt="Dzieło sztuki">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('images/sculpture1Carousel.jpg') }}" class="d-block w-100 same-size-img" alt="Dzieło sztuki">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

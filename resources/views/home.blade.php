<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeriona - Odkryj niepowtarzalne dzieła sztuki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 500px;
        }

        .same-size-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 0.375rem;
        }
    </style>
</head>

<body>
    @include('components.header')

    @include('components.hero-section')

    <div id="followed-offers-section" class="mb-5" style="display:none;"></div>

    <div id="wyszukiwarka-section" class="mt-5">
        @include('components.wyszukiwarka', ['artworks' => $artworks, 'cartArtworks' => $cartArtworks ?? []])
    </div>

    @include('components.statystyki-platformy')

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('przegladaj-kolekcje-btn');
    const headerBtn = document.getElementById('header-search-btn');
    const wyszukiwarkaSection = document.getElementById('wyszukiwarka-section');

    function scrollToWyszukiwarka(e) {
        if (e) e.preventDefault();
        if (wyszukiwarkaSection) {
            wyszukiwarkaSection.style.display = 'block';
            const headerHeight = document.querySelector('nav').offsetHeight;
            const targetPosition = wyszukiwarkaSection.offsetTop - headerHeight;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
            }
        }


        if (window.location.hash === '#wyszukiwarka-section') {
            scrollToWyszukiwarka();
        }

        if (btn) {
            btn.addEventListener('click', scrollToWyszukiwarka);
        }

        if (headerBtn) {
            headerBtn.addEventListener('click', scrollToWyszukiwarka);
        }
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const showFollowedBtn = document.getElementById('show-followed-offers');
        const followedSection = document.getElementById('followed-offers-section');
        const wyszukiwarkaSection = document.getElementById('wyszukiwarka-section');
        const przegladajBtn = document.getElementById('header-search-btn');

        function loadFollowedOffers() {
            fetch('{{ route('client.followers.followed_offers') }}')
                .then(response => response.text())
                .then(html => {
                    followedSection.innerHTML = html;
                    followedSection.style.display = 'block';
                    wyszukiwarkaSection.style.display = 'none';
                });
        }

        if (showFollowedBtn && followedSection && wyszukiwarkaSection) {
            showFollowedBtn.addEventListener('click', function(e) {
                e.preventDefault();
                loadFollowedOffers();
            });
        }
        // Dodaj obsługę powrotu do wyszukiwarki
        if (przegladajBtn && followedSection && wyszukiwarkaSection) {
            przegladajBtn.addEventListener('click', function(e) {
                wyszukiwarkaSection.style.display = 'block';
                followedSection.style.display = 'none';
            });
        }
    });
    </script>
</body>
</html>

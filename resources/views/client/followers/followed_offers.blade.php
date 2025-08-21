<div class="container my-5">
    <h2 class="mb-4 text-center">
        <i class="fas fa-heart text-danger me-2"></i>Oferty zaobserwowanych sprzedawców
    </h2>

    @if($artworks->count())
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Zdjęcie</th>
                        <th>Nazwa</th>
                        <th>Autor</th>
                        <th>Cena</th>
                        <th>Kategoria</th>
                        <th>Właściciel</th>
                        <th style="width:135px; text-align:center;">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($artworks as $artwork)
                        <tr>
                            <td>
                                <img src="{{ asset($artwork->image_path) }}"
                                    alt="{{ $artwork->title }}"
                                    style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td>{{ $artwork->title }}</td>
                            <td>{{ $artwork->artist }}</td>
                            <td>
                                @if($artwork->is_priceless)
                                    <span class="text-info">Bezcenne</span>
                                @elseif(!is_null($artwork->price) && $artwork->price !== '')
                                    {{ $artwork->price }} zł
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $artwork->category->name ?? 'Brak' }}</td>
                            <td>{{ $artwork->user->username ?? '-' }}</td>
                            <td style="text-align:center;">
                                @php
                                    $inCart = isset($cartArtworks) && in_array($artwork->id, $cartArtworks);
                                @endphp
                                @if ($inCart)
                                    <form method="POST" action="{{ route('client.cart.remove', $artwork->id) }}"
                                        class="cart-action-form d-inline" data-artwork-id="{{ $artwork->id }}"
                                        data-action-type="remove">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-danger btn-sm d-inline-flex align-items-center justify-content-center"
                                            style="min-width:120px;">
                                            <i class="fas fa-times me-1"></i> Usuń z koszyka
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('client.cart.add', $artwork->id) }}"
                                        class="cart-action-form d-inline" data-artwork-id="{{ $artwork->id }}"
                                        data-action-type="add">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-success btn-sm d-inline-flex align-items-center justify-content-center"
                                            style="min-width:120px;">
                                            <i class="fas fa-cart-plus me-1"></i> Dodaj do koszyka
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(method_exists($artworks, 'links'))
            <div class="pagination-container">
                {{ $artworks->withQueryString()->links() }}
            </div>
        @endif
    @else
        <p class="text-center">Brak dzieł od zaobserwowanych sprzedawców.</p>
    @endif

    @if(!empty($specialOffers))
        <div class="mt-5">
            <h3 class="mb-3 text-center text-warning">
                <i class="fas fa-star me-2"></i>Oferty specjalne od sprzedawców
            </h3>
            <div class="row justify-content-center">
                @foreach($specialOffers as $sellerId => $artwork)
                    <div class="col-md-5 col-lg-3 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="{{ asset($artwork->image_path) }}" class="card-img-top" alt="{{ $artwork->title }}" style="object-fit:cover; height:140px;">
                            <div class="card-body p-3">
                                <h5 class="card-title" style="font-size:1.1rem;">{{ $artwork->title }}</h5>
                                <p class="card-text mb-1" style="font-size:0.95rem;"><b>Autor:</b> {{ $artwork->artist }}</p>
                                <p class="card-text mb-1" style="font-size:0.95rem;"><b>Sprzedawca:</b> {{ $artwork->user->username ?? '-' }}</p>
                                <p class="card-text mb-1" style="font-size:0.95rem;"><b>Kategoria:</b> {{ $artwork->category->name ?? 'Brak' }}</p>
                                <span class="badge bg-info mb-2">Bezcenne</span>
                                <form method="POST" action="{{ route('client.cart.add', $artwork->id) }}" class="mt-2">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-success btn-sm w-100 d-inline-flex align-items-center justify-content-center"
                                        style="font-size:0.95rem;">
                                        <i class="fas fa-cart-plus me-1"></i> Dodaj do koszyka
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Toasty dla feedbacku -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toast-success" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="polite"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Operacja zakończona sukcesem!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    <div id="toast-error" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="polite"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toast-error-msg">
                Błąd podczas operacji!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const showFollowedBtn = document.getElementById('show-followed-artworks');
    const followedSection = document.getElementById('followed-artworks-section');
    const wyszukiwarkaSection = document.getElementById('wyszukiwarka-section');

    if (showFollowedBtn && followedSection && wyszukiwarkaSection) {
        showFollowedBtn.addEventListener('click', function(e) {
            e.preventDefault();
            followedSection.style.display = 'block';
            wyszukiwarkaSection.scrollIntoView({ behavior: 'smooth' });
        });
    }
});
</script>

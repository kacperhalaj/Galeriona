<div class="container my-5">
    <h2 class="mb-4 text-center">Przeglądaj dzieła</h2>
    <form method="GET" action="{{ route('kolekcje.index') }}" class="row g-3 mb-4" id="search-form">
        <div class="col-md-3">
            <input type="text" name="nazwa" class="form-control" placeholder="Nazwa przedmiotu" value="{{ request('nazwa') }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="autor" class="form-control" placeholder="Autor" value="{{ request('autor') }}">
        </div>
        <div class="col-md-2">
            <input type="number" name="cena_od" class="form-control" placeholder="Cena od" min="0" value="{{ request('cena_od') }}">
        </div>
        <div class="col-md-2">
            <input type="number" name="cena_do" class="form-control" placeholder="Cena do" min="0" value="{{ request('cena_do') }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="kategoria" class="form-control" placeholder="Kategoria" value="{{ request('category') }}">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Szukaj</button>
            <button type="button" class="btn btn-secondary w-100 mt-2" id="clear-button">Wyczyść</button>
        </div>
    </form>

    @if(isset($artworks) && $artworks->count())
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Zdjęcie</th>
                        <th>Nazwa</th>
                        <th>Autor</th>
                        <th>Cena</th>
                        <th>Kategoria</th>
                        @auth
                            @if (auth()->user()->role === 'user')
                                <th style="width:135px; text-align:center;">Akcje</th>
                            @endif
                            @if (auth()->user()->role === 'user')

                            @endif
                        @endauth

                        @auth
                            @if (auth()->user()->role === 'user' || auth()->user()->role === 'admin' || auth()->user()->role === 'seller')
                                <th>Właściciel</th>
                            @endif
                            @if (auth()->user()->role === 'user')

                            @endif
                        @endauth

                    </tr>
                </thead>
                <tbody>
                    @foreach($artworks as $artwork)
                        <tr>
                            {{-- Zdjęcie dzieła --}}
                            <td>
                                <img src="{{ asset($artwork->image_path) }}"
                                alt="{{ $artwork->title }}"
                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                            </td>

                            {{-- Nazwa dzieła --}}
                            <td>{{ $artwork->title }}</td>

                            {{-- Autor dzieła --}}
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

                            {{-- Kategoria dzieła --}}
                            <td>{{ $artwork->category->name ?? 'Brak' }}</td>


                            @auth
                                @if (auth()->user()->role === 'user')
                                    <td style="text-align:center;">
                                        @php
                                            Log::alert($cartArtworks ?? 'Brak danych o dziełach w koszyku');
                                            $inCart = isset($cartArtworks) && in_array($artwork->id, $cartArtworks);
                                            Log::alert($inCart ? 'Dzieło jest w koszyku' : 'Dzieło nie jest w koszyku');
                                        @endphp
                                        @if ($inCart)
                                            <!-- Przycisk USUŃ z koszyka -->
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
                                            <!-- Przycisk DODAJ do koszyka -->
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
                                @endif
                            @endauth
                            <td>
                                {{ $artwork->user->username ?? '-' }}
                                @auth
                                    @if(auth()->user()->id !== $artwork->user->id && auth()->user()->role === 'user')
                                        @if(auth()->user()->followedSellers->contains($artwork->user->id))
                                            <form action="{{ route('seller.unfollow', $artwork->user->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm">Przestań obserwować</button>
                                            </form>
                                        @else
                                            <form action="{{ route('seller.follow', $artwork->user->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button class="btn btn-outline-primary btn-sm">Obserwuj</button>
                                            </form>
                                        @endif
                                    @endif
                                @endauth
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-container">
            {{ $artworks->withQueryString()->links() }}
        </div>
    @else
        <p>Brak wyników spełniających kryteria.</p>
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

        let toastSuccess = new bootstrap.Toast(document.getElementById('toast-success'));
        let toastError = new bootstrap.Toast(document.getElementById('toast-error'));
        let toastErrorMsg = document.getElementById('toast-error-msg');

        function renderActionCell(artworkId, inCart) {
            if (inCart) {
                return `<form method="POST" action="/client/cart/remove/${artworkId}" class="cart-action-form d-inline" data-artwork-id="${artworkId}" data-action-type="remove">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center justify-content-center" style="min-width:120px;">
                            <i class="fas fa-times me-1"></i> Usuń z koszyka
                        </button>
                    </form>`;
            } else {
                return `<form method="POST" action="/client/cart/add/${artworkId}" class="cart-action-form d-inline" data-artwork-id="${artworkId}" data-action-type="add">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-success btn-sm d-inline-flex align-items-center justify-content-center" style="min-width:120px;">
                            <i class="fas fa-cart-plus me-1"></i> Dodaj do koszyka
                        </button>
                    </form>`;
            }
        }

        function handleCartAction(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const artworkId = this.dataset.artworkId;
                const actionType = this.dataset.actionType;
                const formData = new FormData(this);

                let method = actionType === 'remove' ? 'DELETE' : 'POST';
                let actionUrl = this.action;

                fetch(actionUrl, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': formData.get('_token'),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: method === 'POST' ? formData : null
                    })
                    .then(async response => {
                        if (response.ok) {
                            const cell = this.parentElement;
                            if (actionType === 'add') {
                                cell.innerHTML = renderActionCell(artworkId, true);
                                handleCartAction(cell.querySelector('form'));
                                toastSuccess.show();
                            } else {
                                cell.innerHTML = renderActionCell(artworkId, false);
                                handleCartAction(cell.querySelector('form'));
                                toastSuccess.show();
                            }
                        } else {
                            let msg = 'Błąd!';
                            try {
                                let data = await response.json();
                                if (data.error) msg = data.error;
                            } catch {}
                            toastErrorMsg.textContent = msg;
                            toastError.show();
                        }
                    });
            });
        }

        // Funkcja do aktualizacji tabeli
        function updateTable(url) {
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('.table-responsive');
                    const pagination = doc.querySelector('.pagination-container');

                    if (newTable) {
                        document.querySelector('.table-responsive').innerHTML = newTable.innerHTML;
                    }
                    if (pagination) {
                        document.querySelector('.pagination-container').innerHTML = pagination.innerHTML;
                    }
                    // Ponownie przypisz handlery po aktualizacji tabeli
                    document.querySelectorAll('.cart-action-form').forEach(handleCartAction);
                });
        }

        // Przypnij do wszystkich przycisków na starcie
        document.querySelectorAll('.cart-action-form').forEach(handleCartAction);

        // Obsługa formularza wyszukiwania
        const form = document.getElementById('search-form');
        const clearButton = document.getElementById('clear-button');
        const inputs = form.querySelectorAll('input');

        // Obsługa wyszukiwania
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const searchParams = new URLSearchParams(formData);
            updateTable(`${form.action}?${searchParams.toString()}`);
        });

        // Obsługa czyszczenia
        clearButton.addEventListener('click', function() {
            inputs.forEach(input => {
                input.value = '';
            });
            updateTable(form.action);
        });
    });
</script>

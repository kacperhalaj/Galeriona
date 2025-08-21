<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koszyk - Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .cart-img-preview {
            width: 64px;
            height: 48px;
            object-fit: cover;
            border-radius: 6px;
        }

        .cart-remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            font-size: 1.1rem;
            padding: 0;
            cursor: pointer;
        }

        .cart-remove-btn:hover {
            color: #a71d2a;
        }
    </style>
</head>

<body class="bg-light">
    @include('components.header')

    <div class="container my-5">
        <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Koszyk</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($items->isEmpty())
            <div class="alert alert-info">Koszyk jest pusty.</div>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-left me-1"></i> Przeglądaj dzieła
            </a>
        @else
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Dzieło</th>
                        <th>Zdjęcie</th>
                        <th>Cena</th>
                        <th>Zniżka</th>
                        <th>Ilość</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->artwork->title }}</td>
                            <td>
                                @if ($item->artwork->image_path)
                                    <img src="{{ asset('storage/artworksImage/' . $item->artwork->image_path) }}"
                                        alt="{{ $item->artwork->title }}" class="cart-img-preview">
                                @else
                                    <img src="https://via.placeholder.com/64x48?text=Brak" alt="Brak zdjęcia"
                                        class="cart-img-preview">
                                @endif
                            </td>
                            <td>{{ number_format($item->artwork->price, 2, ',', ' ') }} zł</td>
                            <td>
                                @if (isset($discounts[$item->id]))
                                    -{{ number_format($discounts[$item->id], 2, ',', ' ') }} zł
                                @else
                                    –
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                <form action="{{ route('client.cart.remove', $item->artwork_id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="cart-remove-btn" title="Usuń">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Promocje --}}
            @if (!empty($promotions))
                <div class="alert alert-success">
                    <ul class="mb-0">
                        @foreach ($promotions as $promo)
                            <li>{{ $promo }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Podsumowanie --}}
            @php
                $sum = 0;
                $totalDiscount = 0;
                foreach ($items as $item) {
                    $price = $item->artwork->price;
                    $discount = $discounts[$item->id] ?? 0;
                    $sum += $price;
                    $totalDiscount += $discount;
                }
                $finalPrice = $sum - $totalDiscount;
            @endphp

            <div class="mt-4">
                <div class="row">
                    <div class="col-md-4 offset-md-8">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Suma do zapłaty (przed rabatem):
                                <span>{{ number_format($sum, 2, ',', ' ') }} zł</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Rabat:
                                <span class="text-success">-{{ number_format($totalDiscount, 2, ',', ' ') }} zł</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                Cena po obniżce:
                                <span>{{ number_format($finalPrice, 2, ',', ' ') }} zł</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-3 text-end">
                <a href="{{ route('client.cart.checkout.form') }}" class="btn btn-success btn-lg">Kupuję</a>
            </div>

            <form action="{{ route('client.cart.clear') }}" method="POST" class="d-inline mt-3 text-end">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-secondary">Opróżnij koszyk</button> 
            </form>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

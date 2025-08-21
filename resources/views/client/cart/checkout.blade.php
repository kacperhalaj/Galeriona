<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podsumowanie zamówienia - Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    @include('components.header')

    <div class="container my-5">
        {{-- Wyświetlanie komunikatów sesji --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2 class="mb-4"><i class="fas fa-bag-shopping me-2"></i>Podsumowanie zamówienia</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Dzieło</th>
                    <th>Cena</th>
                    <th>Zniżka</th>
                    <th>Ilość</th>
                    <th>Suma</th>
                </tr>
            </thead>
            <tbody>

                @php
                    $sum = 0;
                    $totalDiscount = 0;
                    $totalVolume = 0; // Inicjalizacja całkowitej objętości
                @endphp


                @foreach ($items as $item)
                    @php
                        $price = $item->artwork->price;
                        $discount = $discounts[$item->id] ?? 0;
                        $finalPrice = $price - $discount;
                        $sum += $price * $item->quantity;
                        $totalDiscount += $discount * $item->quantity;



                        $itemHeight = $item->artwork->height ?? 1; // cm
                        $itemWidth = $item->artwork->width ?? 1;   // cm
                        $itemDepth = $item->artwork->depth ?? 1;   // cm
                        $totalVolume += ($itemHeight * $itemWidth * $itemDepth) * $item->quantity;                        
                    @endphp
                    <tr>
                        <td>{{ $item->artwork->title }}</td>
                        <td>
                            @if ($discount)
                                <span class="text-decoration-line-through text-secondary">
                                    {{ number_format($price, 2, ',', ' ') }} zł
                                </span><br>
                                <span class="fw-bold text-success">{{ number_format($finalPrice, 2, ',', ' ') }} zł</span>
                            @else
                                {{ number_format($price, 2, ',', ' ') }} zł
                            @endif
                        </td>
                        <td>
                            {{ $discount ? '-'.number_format($discount, 2, ',', ' ').' zł' : '–' }}
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($finalPrice * $item->quantity, 2, ',', ' ') }} zł</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-end fw-bold">Suma przed rabatem:</td>
                    <td>{{ number_format($sum, 2, ',', ' ') }} zł</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end text-success fw-bold">Łączny rabat:</td>
                    <td class="text-success">-{{ number_format($totalDiscount, 2, ',', ' ') }} zł</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end" style="font-size: 0.95em;">
                        Rabat stałego klienta
                        @if(!empty($quantityDiscountPercent))
                            <span class="fw-normal">({{ $quantityDiscountPercent }}%)</span>
                        @endif
                    </td>
                    <td style="font-size: 0.95em;">
                        @if(!empty($quantityDiscountValue) && $quantityDiscountValue > 0)
                            -{{ number_format($quantityDiscountValue, 2, ',', ' ') }} zł
                        @else
                            brak rabatu stałego klienta
                        @endif
                    </td>
                </tr>
                <tr>
                    <p>Zalogowany użytkownik: {{ Auth::check() ? Auth::user()->username : 'Brak' }}</p>
                    <td colspan="4" class="text-end fw-bold">Do zapłaty:</td>
                    <td class="fw-bold">
                        {{ number_format($sum - $totalDiscount - ($quantityDiscountValue ?? 0), 2, ',', ' ') }} zł
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="card my-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-ruler-combined me-2"></i>Szczegóły wymiarów przedmiotów</h5>
                @if($totalVolume > 0)
                    <ul class="list-group list-group-flush">
                        @foreach ($items as $item)
                            @php
                                $itemHeight = $item->artwork->height ?? 0;
                                $itemWidth = $item->artwork->width ?? 0;
                                $itemDepth = $item->artwork->depth ?? 0;
                                $itemSingleVolume = $itemHeight * $itemWidth * $itemDepth;
                            @endphp
                            <li class="list-group-item">
                                <strong>{{ $item->artwork->title }}</strong> (Ilość: {{ $item->quantity }})
                                @if($itemHeight > 0 && $itemWidth > 0 && $itemDepth > 0)
                                    <br><small class="text-muted">Wymiary sztuki: {{ $itemHeight }}cm x {{ $itemWidth }}cm x {{ $itemDepth }}cm</small>
                                    <br><small class="text-muted">Objętość sztuki: {{ number_format($itemSingleVolume, 0, ',', ' ') }} cm³</small>
                                    @if($item->quantity > 1)
                                    <br><small class="text-muted">Łączna objętość dla {{ $item->quantity }} szt.: {{ number_format($itemSingleVolume * $item->quantity, 0, ',', ' ') }} cm³</small>
                                    @endif
                                @else
                                    <br><small class="text-muted">Wymiary nieokreślone.</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <p class="card-text mt-3 fw-bold">
                        Łączna szacowana objętość wszystkich przedmiotów: {{ number_format($totalVolume, 0, ',', ' ') }} cm³
                    </p>
                @else
                    <p class="card-text">Wymiary przedmiotów w koszyku nie zostały określone lub ich objętość wynosi zero.</p>
                @endif
            </div>
        </div>

            @php
                // Definicje paczek (klucz => [nazwa, maksymalna objętość w cm³, przykładowe wymiary])
                $packagesData = [
                    'S' => ['name' => 'S', 'volume' => 6000, 'dims' => 'np. 30x20x10 cm'],
                    'M' => ['name' => 'M', 'volume' => 30000, 'dims' => 'np. 50x30x20 cm'],
                    'XL' => ['name' => 'XL', 'volume' => 84000, 'dims' => 'np. 70x40x30 cm'],
                ];

                $suggestedPackageKey = null; // Klucz sugerowanej paczki (S, M, XL)
                $isCustomShippingRequired = false;
                $packageInfoMessage = ''; // Wiadomość informacyjna

                if ($totalVolume <= 0) {
                    $packageInfoMessage = 'Nie można zasugerować rozmiaru paczki, ponieważ objętość produktów nie została określona lub wynosi zero.';
                } else {
                    foreach ($packagesData as $key => $package) {
                        if ($totalVolume <= $package['volume']) {
                            $suggestedPackageKey = $key;
                            break;
                        }
                    }
                    if ($suggestedPackageKey === null) { // Objętość większa niż największa paczka
                        $isCustomShippingRequired = true;
                        $packageInfoMessage = 'Całkowita objętość Twoich produktów (' . number_format($totalVolume, 0, ',', ' ') . ' cm³) przekracza pojemność naszej największej standardowej paczki (XL: ' . $packagesData['XL']['dims'] . '). W związku z tym może być wymagana specjalna wycena i organizacja wysyłki.';
                    }
                }
            @endphp

            <div class="card mt-3 mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-box-open me-2"></i>Sugestia dotycząca opakowania</h5>

                    @if($packageInfoMessage)
                        <div class="alert {{ $isCustomShippingRequired ? 'alert-warning' : 'alert-info' }}">{{ $packageInfoMessage }}</div>
                    @endif

                    @if($totalVolume > 0 && !$isCustomShippingRequired && $suggestedPackageKey)
                        <p class="card-text">Na podstawie łącznej objętości przedmiotów, poniżej znajdziesz listę dostępnych paczek wraz z sugerowaną opcją dla Twojego zamówienia:</p>
                    @elseif($totalVolume > 0 && $isCustomShippingRequired)
                        <p class="card-text">Poniżej lista standardowych rozmiarów paczek. Twoje zamówienie ze względu na gabaryty wymaga jednak indywidualnej obsługi.</p>
                    @elseif($totalVolume <= 0 && !$packageInfoMessage)
                        <p class="card-text">Dostępne standardowe rozmiary paczek w naszej ofercie:</p>
                    @endif
                    
                    {{-- Zawsze pokazuj listę paczek, jeśli są zdefiniowane --}}
                    @if(!empty($packagesData))
                        <ul class="list-group list-group-flush mb-3">
                            @foreach ($packagesData as $key => $package)
                                <li class="list-group-item d-flex justify-content-between align-items-center
                                    {{ $key === $suggestedPackageKey && !$isCustomShippingRequired ? 'list-group-item-success fw-bold' : '' }}">
                                    Paczka {{ $package['name'] }} <span class="text-muted small">({{ $package['dims'] }})</span>
                                    @if ($key === $suggestedPackageKey && !$isCustomShippingRequired)
                                        <span class="badge bg-primary rounded-pill">Sugerowana</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    
                    <p class="card-text mt-2 fst-italic"><small>Uwaga: Jest to jedynie automatyczna sugestia oparta na sumarycznej objętości. Rzeczywisty sposób pakowania, kształt przedmiotów oraz dostępność opakowań mogą wpłynąć na ostateczny wybór i koszt wysyłki.</small></p>
                </div>
            </div>


        @if (!empty($promotions))
            <div class="alert alert-success">
                <ul class="mb-0">
                    @foreach ($promotions as $promo)
                        <li>{{ $promo }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert {{ Auth::user()->discount > 0 ? 'alert-success' : 'alert-secondary' }}" style="font-size: 0.95em;">
            @if(Auth::user()->discount > 0)
                Twój stały rabat: {{ Auth::user()->discount }}%
            @else
                Brak stałego rabatu klienta
            @endif
        </div>

        <form action="{{ route('client.cart.checkout') }}" method="POST" class="mt-3">
            @csrf
            <div class="mb-3">
                <label for="address_id" class="form-label">Wybierz adres dostawy:</label>
                <select class="form-select" id="address_id" name="address_id" required>
                    <option value="">-- Wybierz adres --</option>
                    @foreach ($addresses as $address)
                        <option value="{{ $address->id }}">
                            {{ $address->street }}, {{ $address->postal_code }} {{ $address->city }}
                        </option>
                    @endforeach
                </select>
            </div>

        
            <button type="submit" class="btn btn-success btn-lg">Potwierdź zakup</button>
            <a href="{{ route('client.cart.index') }}" class="btn btn-secondary ms-2">Anuluj</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statystyki sprzedaży - Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    @include('components.header')
    @include('seller.components.header')

    <div class="container py-4">
        <a href="{{ route('seller.panel') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left me-2"></i>Powrót do panelu sprzedawcy
        </a>
        <h2><i class="fas fa-chart-bar me-2"></i>Statystyki mojej sprzedaży</h2>
        <div class="row my-4">
            <div class="col-md-3">
                <div class="alert alert-success">
                    <b>Łączny przychód:</b><br>
                    {{ number_format($totalEarned, 2, ',', ' ') }} zł
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-info">
                    <b>Liczba sprzedanych dzieł:</b><br>
                    {{ $totalSales }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-primary">
                    <b>Średnia wartość sprzedaży:</b><br>
                    {{ $totalSales > 0 ? number_format($totalEarned / $totalSales, 2, ',', ' ') : 0 }} zł
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-warning">
                    <b>Najdroższa sprzedaż:</b><br>
                    {{ $maxSale ? number_format($maxSale->price, 2, ',', ' ') . ' zł (' . ($maxSale->artwork->title ?? '-') . ')' : '-' }}
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-md-6">
                <div class="alert alert-secondary">
                    <b>Najczęściej sprzedawane dzieła:</b><br>
                    <ul class="mb-0">
                        @forelse($topArtworks as $title => $count)
                            <li>{{ $title }} <span class="text-muted">({{ $count }})</span></li>
                        @empty
                            <li class="text-muted">Brak danych</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-dark">
                    <b>Najlepsi klienci:</b><br>
                    <ul class="mb-0">
                        @forelse($topBuyers as $buyer => $count)
                            <li>{{ $buyer }} <span class="text-muted">({{ $count }})</span></li>
                        @empty
                            <li class="text-muted">Brak danych</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-md-3">
                <div class="alert alert-danger">
                    <b>Najbardziej hojny darczyńca:</b><br>
                    @if($topDonor)
                        {{ $topDonor->client->username }}<br>
                        <span class="text-muted">{{ number_format($topDonor->total, 2, ',', ' ') }} zł</span>
                    @else
                        Brak darczyńców
                    @endif
                </div>
            </div>
        </div>

        <hr>

        <div class="row gy-4">
            <div class="col-lg-6">
                <h5 class="mb-3">Sprzedaż miesiąc po miesiącu</h5>
                @if (empty($monthlyStats['months']))
                    <p class="text-muted">Brak danych do wyświetlenia wykresu.</p>
                @else
                    <canvas id="salesChart"></canvas>
                @endif
            </div>
            <div class="col-lg-6">
                <h5 class="mb-3">Struktura przychodu z dzieł</h5>
                @if (empty($artworkPie))
                    <p class="text-muted">Brak danych do wyświetlenia wykresu.</p>
                @else
                    <div class="d-flex justify-content-center">
                        <canvas id="artworkPieChart" style="max-width:280px;max-height:280px;"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <footer class="mt-5 py-4 bg-transparent"></footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sprzedaż miesiąc po miesiącu
        @if (!empty($monthlyStats['months']))
            new Chart(document.getElementById('salesChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyStats['months']) !!},
                    datasets: [{
                            label: 'Przychód (zł)',
                            data: {!! json_encode($monthlyStats['totals']) !!},
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Liczba sprzedaży',
                            data: {!! json_encode($monthlyStats['counts']) !!},
                            backgroundColor: 'rgba(255, 205, 86, 0.4)',
                            type: 'line',
                            borderColor: 'rgba(255, 205, 86, 1)',
                            fill: false,
                            yAxisID: 'y2'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y1: {
                            beginAtZero: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Przychód (zł)'
                            }
                        },
                        y2: {
                            beginAtZero: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: 'Liczba'
                            }
                        }
                    }
                }
            });
        @endif

        // Struktura przychodu z dzieł (Pie)
        @if (!empty($artworkPie))
            new Chart(document.getElementById('artworkPieChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: {!! json_encode(array_keys($artworkPie)) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($artworkPie)) !!},
                        backgroundColor: [
                            '#0074D9', '#FF4136', '#2ECC40', '#FF851B', '#B10DC9', '#FFDC00', '#001f3f'
                        ]
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        @endif
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statystyki systemu - Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    @include('components.header')
    @include('admin.components.header')

    <div class="container py-4">
        <a href="{{ route('admin.panel') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left me-2"></i>Powrót do panelu admina
        </a>
        <h2><i class="fas fa-chart-bar me-2"></i>Statystyki systemu</h2>
        <div class="row my-4">
            <div class="col-md-3">
                <div class="alert alert-success">
                    <b>Wszyscy użytkownicy:</b><br>
                    {{ $usersCount }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-info">
                    <b>Sprzedawcy:</b><br>
                    {{ $sellersCount }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-primary">
                    <b>Klienci:</b><br>
                    {{ $clientsCount }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-secondary">
                    <b>Liczba dzieł sztuki:</b><br>
                    {{ $artworksCount }}
                </div>
            </div>
        </div>
        <div class="row my-4">
            <div class="col-md-4">
                <div class="alert alert-dark">
                    <b>Liczba transakcji:</b><br>
                    {{ $totalSales }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-warning">
                    <b>Łączny obrót:</b><br>
                    {{ number_format($totalEarned, 2, ',', ' ') }} zł
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-danger">
                    <b>Najdroższa sprzedaż:</b><br>
                    {{ $maxSale ? number_format($maxSale->price, 2, ',', ' ') . ' zł (' . ($maxSale->artwork->title ?? '-') . ')' : '-' }}
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-md-4">
                <div class="alert alert-success">
                    <b>Najaktywniejsi sprzedawcy:</b><br>
                    <ul class="mb-0">
                        @forelse($topSellers as $seller => $count)
                            <li>{{ $seller }} <span class="text-muted">({{ $count }})</span></li>
                        @empty
                            <li class="text-muted">Brak danych</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-info">
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
            <div class="col-md-4">
                <div class="alert alert-secondary">
                    <b>Najpopularniejsze dzieła:</b><br>
                    <ul class="mb-0">
                        @forelse($topArtworks as $title => $count)
                            <li>{{ $title }} <span class="text-muted">({{ $count }})</span></li>
                        @empty
                            <li class="text-muted">Brak danych</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <hr>

        <div class="row gy-4">
            <div class="col-lg-6">
                <h5 class="mb-3">Obrót miesiąc po miesiącu</h5>
                @if (empty($monthlyStats['months']))
                    <p class="text-muted">Brak danych do wyświetlenia wykresu.</p>
                @else
                    <canvas id="salesChart"></canvas>
                @endif
            </div>
            <div class="col-lg-6">
                <h5 class="mb-3">Średnia wartość transakcji w miesiącu</h5>
                @if (empty($monthlyStats['months']))
                    <p class="text-muted">Brak danych do wyświetlenia wykresu.</p>
                @else
                    <canvas id="avgSaleChart"></canvas>
                @endif
            </div>
        </div>
    </div>

    <footer class="mt-5 py-4 bg-transparent"></footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Obrót miesiąc po miesiącu
        @if (!empty($monthlyStats['months']))
            new Chart(document.getElementById('salesChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyStats['months']) !!},
                    datasets: [{
                            label: 'Obrót (zł)',
                            data: {!! json_encode($monthlyStats['totals']) !!},
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Liczba transakcji',
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
                                text: 'Obrót (zł)'
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

        // Średnia wartość transakcji w miesiącu
        @if (!empty($monthlyStats['months']))
            new Chart(document.getElementById('avgSaleChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyStats['months']) !!},
                    datasets: [{
                        label: 'Średnia wartość transakcji (zł)',
                        data: {!! json_encode($monthlyStats['avgs']) !!},
                        backgroundColor: 'rgba(40, 167, 69, 0.4)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        fill: true,
                        tension: 0.2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        @endif
    </script>
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

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statystyki - Galeriona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    @include('components.header')
    @include('client.components.header')

    <div class="container py-4">
        <a href="{{ route('client.panel') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left me-2"></i>Powrót do panelu
        </a>
        <h2><i class="fas fa-chart-bar me-2"></i>Statystyki moich transakcji</h2>
        <div class="row my-4">
            <div class="col-md-3">
                <div class="alert alert-success">
                    <b>Łączna wartość zakupów:</b><br>
                    {{ number_format($totalSpent, 2, ',', ' ') }} zł
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-info">
                    <b>Liczba zakupionych dzieł:</b><br>
                    {{ $totalPurchases }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-primary">
                    <b>Średnia wartość zakupu:</b><br>
                    {{ $totalPurchases > 0 ? number_format($totalSpent / $totalPurchases, 2, ',', ' ') : 0 }} zł
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-warning">
                    <b>Najdroższy zakup:</b><br>
                    {{ $maxPurchase ? number_format($maxPurchase->price, 2, ',', ' ') . ' zł (' . ($maxPurchase->artwork->title ?? '-') . ')' : '-' }}
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-md-6">
                <div class="alert alert-secondary">
                    <b>Najpopularniejsi artyści:</b><br>
                    <ul class="mb-0">
                        @forelse($topArtists as $artist => $count)
                            <li>{{ $artist }} <span class="text-muted">({{ $count }})</span></li>
                        @empty
                            <li class="text-muted">Brak danych</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-dark">
                    <b>Najczęściej kupowani sprzedawcy:</b><br>
                    <ul class="mb-0">
                        @forelse($topSellers as $seller => $count)
                            <li>{{ $seller }} <span class="text-muted">({{ $count }})</span></li>
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
                <h5 class="mb-3">Zakupy miesiąc po miesiącu</h5>
                @if (empty($monthlyStats['months']))
                    <p class="text-muted">Brak danych do wyświetlenia wykresu.</p>
                @else
                    <canvas id="purchasesChart"></canvas>
                @endif
            </div>
            <div class="col-lg-6">
                <h5 class="mb-3">Struktura wydatków na artystów</h5>
                @if (empty($artistPie))
                    <p class="text-muted">Brak danych do wyświetlenia wykresu.</p>
                @else
                    <div class="d-flex justify-content-center">
                        <canvas id="artistPieChart" style="max-width:280px;max-height:280px;"></canvas>
                    </div>
                @endif
            </div>
            <div class="col-lg-6">
                <h5 class="mb-3">Zakupy u sprzedawców</h5>
                @if (empty($topSellers))
                    <p class="text-muted">Brak danych do wyświetlenia wykresu.</p>
                @else
                    <canvas id="sellerBarChart"></canvas>
                @endif
            </div>
            <div class="col-lg-6">
                <h5 class="mb-3">Średnia cena zakupu w danym miesiącu</h5>
                @if (empty($monthlyStats['months']))
                    <p class="text-muted">Brak danych do wyświetlenia wykresu.</p>
                @else
                    <canvas id="avgPurchaseChart"></canvas>
                @endif
            </div>
        </div>
    </div>

    <footer class="mt-5 py-4 bg-transparent"></footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Zakupy miesiąc po miesiącu
        @if (!empty($monthlyStats['months']))
            new Chart(document.getElementById('purchasesChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyStats['months']) !!},
                    datasets: [{
                            label: 'Wartość zakupów (zł)',
                            data: {!! json_encode($monthlyStats['totals']) !!},
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Liczba zakupów',
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
                                text: 'Wartość (zł)'
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

        // Struktura wydatków na artystów (Pie)
        @if (!empty($artistPie))
            new Chart(document.getElementById('artistPieChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: {!! json_encode(array_keys($artistPie)) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($artistPie)) !!},
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

        // Zakupy u sprzedawców (Bar horizontal)
        @if (!empty($topSellers))
            new Chart(document.getElementById('sellerBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($topSellers)) !!},
                    datasets: [{
                        label: 'Liczba zakupów',
                        data: {!! json_encode(array_values($topSellers)) !!},
                        backgroundColor: 'rgba(153, 102, 255, 0.6)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    indexAxis: 'y'
                }
            });
        @endif

        // Średnia cena zakupu w danym miesiącu
        @if (!empty($monthlyStats['months']))
            new Chart(document.getElementById('avgPurchaseChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyStats['months']) !!},
                    datasets: [{
                        label: 'Średnia cena zakupu (zł)',
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
</body>

</html>

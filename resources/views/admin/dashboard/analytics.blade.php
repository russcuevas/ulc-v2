<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ULC - System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
    <style>
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.85);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        .loader-container {
            display: flex;
            gap: 10px;
        }

        .dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #ff6b35;
            animation: bounce 1.2s infinite ease-in-out;
        }

        .dot1 {
            animation-delay: 0s;
        }

        .dot2 {
            animation-delay: 0.2s;
        }

        .dot3 {
            animation-delay: 0.4s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        .loader-text {
            font-size: 1rem;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div id="loadingOverlay" style="display:none;">
        <div class="loader-container">
            <div class="dot dot1"></div>
            <div class="dot dot2"></div>
            <div class="dot dot3"></div>
        </div>
        <p class="loader-text">Filtering data, please wait...</p>
    </div>
    <div class="container mt-4">
        <a class="nav-link text-primary" href="{{ route('admin.dashboard.page') }}"><i
                class="fa-solid fa-arrow-left"></i>
            BACK TO DASHBOARD</a>
        <h3 class="mb-4">Summary for {{ $location }}</h3>

        @if (request('from_month') && request('to_month'))
            <div class="alert alert-primary mb-3">
                Showing data from
                <strong>
                    {{ \Carbon\Carbon::parse(request('from_month'))->format('F Y') }}
                    –
                    {{ \Carbon\Carbon::parse(request('to_month'))->format('F Y') }}
                </strong>
            </div>
        @endif

        {{-- Date Range Filter --}}
        <form id="filterForm" method="GET"
            action="{{ route('admin.dashboard.analytics', ['location' => $location]) }}" class="row g-3 mb-4">

            <div class="col-sm-6 col-md-4 col-lg-4">
                <label class="form-label">From Month</label>
                <input type="month" name="from_month" class="form-control"
                    value="{{ request('from_month', $from ? $from->format('Y-m') : '') }}">
            </div>

            <div class="col-sm-6 col-md-4 col-lg-4">
                <label class="form-label">To Month</label>
                <input type="month" name="to_month" class="form-control"
                    value="{{ request('to_month', $to ? $to->format('Y-m') : '') }}">
            </div>

            <div class="col-sm-6 col-md-2 col-lg-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Apply</button>
            </div>

            <div class="col-sm-6 col-md-2 col-lg-2 d-flex align-items-end">
                <a href="{{ route('admin.dashboard.analytics', ['location' => $location]) }}"
                    class="btn btn-secondary w-100">Reset</a>
            </div>

        </form>


        {{-- Summary Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-2 shadow-lg h-100 card-left-orange">
                    <div class="card-body">
                        <span>Clients</span>
                        <h2>{{ $clients }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-2 shadow-lg h-100 card-left-orange">
                    <div class="card-body">
                        <span>Loans</span>
                        <h2>₱{{ number_format($loans, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-2 shadow-lg h-100 card-left-orange">
                    <div class="card-body">
                        <span>Collected</span>
                        <h2>₱{{ number_format($collected, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card border-2 shadow-lg p-4 mb-5">
                    <h6>LOANS/COLLECTED BREAKDOWN</h6>
                    <canvas id="lineChart" height="255px"></canvas>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-2 shadow-lg p-4 mb-5">
                    <h6>LOAN STATUS BREAKDOWN</h6>
                    <canvas id="loanStatusChart"></canvas>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-2 shadow-lg p-4 mb-5">
                    <h6>PAYMENT TYPE BREAKDOWN</h6>
                    <canvas id="paymentStatusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Area Name</th>
                        <th>Collector</th>
                        <th>Total Loans</th>
                        <th>Total Clients</th>
                        <th>Total Collected</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $area)
                        <tr>
                            <td>{{ $area['area'] }}</td>
                            <td>{{ $area['collector'] ?? 'N/A' }}</td>
                            <td>₱{{ number_format($area['total_loans'], 2) }}</td>
                            <td>{{ number_format($area['total_clients']) }}</td>
                            <td>₱{{ number_format($area['total_collected'], 2) }}</td>
                        </tr>
                    @endforeach
                    @if (count($data) === 0)
                        <tr>
                            <td colspan="6" class="text-center">No data found for this range</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <h6 style="text-transform: uppercase; text-align: right; margin-bottom: 10px; color: red;">ASSIGNED SECRETARY:
            {{ $secretary }}
        </h6>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                        label: 'Loans',
                        data: {!! json_encode($loansData) !!},
                        borderColor: 'rgb(255 107 53)',
                        backgroundColor: 'rgb(255 107 53)',
                        tension: 0.3,
                        fill: true,
                    },
                    {
                        label: 'Collected',
                        data: {!! json_encode($collectedData) !!},
                        borderColor: 'rgb(120, 120, 120)',
                        backgroundColor: 'rgb(120, 120, 120)',
                        tension: 0.3,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount in peso'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });


        const loanStatusCtx = document.getElementById('loanStatusChart').getContext('2d');
        new Chart(loanStatusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($loanStatusLabels) !!},
                datasets: [{
                    data: {!! json_encode($loanStatusData) !!},
                    backgroundColor: ['#ff6b35', '#6c757d', '#ffc107', '#0d6efd', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
        new Chart(paymentStatusCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($paymentStatusLabels) !!},
                datasets: [{
                    data: {!! json_encode($paymentStatusData) !!},
                    backgroundColor: ['#0d6efd', '#dc3545', '#ffc107', '#198754', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#filterForm').on('submit', function() {
                $('#loadingOverlay').fadeIn();
            });
        });
    </script>
</body>

</html>

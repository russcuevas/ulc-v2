<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ULC - System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
    @include('secretary.manila.components.navbar')
    <div id="loadingOverlay" style="display:none;">
        <div class="loader-container">
            <div class="dot dot1"></div>
            <div class="dot dot2"></div>
            <div class="dot dot3"></div>
        </div>
        <p class="loader-text">Filtering data, please wait...</p>
    </div>

    <div class="main-content">
        <div class="row g-4 mb-4">
            <h4>Manila Area</h4>

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
            <form id="filterForm" method="GET" action="" class="row g-4">

                <div class="col-md-4">
                    <label class="form-label">From Month</label>
                    <input type="month" name="from_month" class="form-control" value="{{ request('from_month') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">To Month</label>
                    <input type="month" name="to_month" class="form-control" value="{{ request('to_month') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Apply</button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <a href="dashboard" class="btn btn-secondary w-100">Reset</a>
                </div>


            </form>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-2 shadow-lg h-100 card-left-orange">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="text-body-secondary fw-medium">Total Loans</span>
                            <div class="icon-box bg-danger-subtle text-danger">
                                <i class="fa-solid fa-coins"></i>
                            </div>
                        </div>
                        <h2 class="h3 fw-bold mb-1">
                            ₱{{ number_format($totalLoans, 2) }}
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-2 shadow-lg h-100 card-left-orange">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="text-body-secondary fw-medium">Total Clients</span>
                            <div class="icon-box bg-primary-subtle text-primary">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                        <h2 class="h3 fw-bold mb-1">
                            {{ number_format($totalClients) }}
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-2 shadow-lg h-100 card-left-orange">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="text-body-secondary fw-medium">Total Collected</span>
                            <div class="icon-box bg-info-subtle text-info">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </div>
                        </div>
                        <h2 class="h3 fw-bold mb-1">
                            ₱{{ number_format($totalCollected, 2) }}
                        </h2>
                    </div>
                </div>
            </div>
            <a style="text-align: right; text-decoration: none;"
                href="{{ route('secretary.manila.analytics.page') }}">View summary details</a>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card border-2 shadow-lg h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Loan Analytics [Monthly Collections]</h5>
                            </div>
                        </div>
                        <span class="text-success small">
                            Total selected range: ₱<span id="totalYearCollections"></span>
                        </span>


                        <canvas id="portfolioChart" style="width:100%; max-height:300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card border-2 shadow-lg h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Recent Activity</h5>

                        <div class="d-flex gap-3 mb-4">
                            <div class="activity-dot  flex-shrink-0"></div>
                            <div>
                                <p class="mb-1 small"></p>
                                <small class="text-body-secondary">

                                    <span style="color: #ff6b35"></span>
                                </small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/admin/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($labels);
        const collectionsData = @json($collectionsData);

        // FIXED total calculation + formatting
        const totalYear = collectionsData
            .map(v => Number(v))
            .reduce((a, b) => a + b, 0);

        document.getElementById('totalYearCollections').innerText =
            totalYear.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        const ctx = document.getElementById('portfolioChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Collections',
                    data: collectionsData.map(v => Number(v)),
                    backgroundColor: 'rgb(255, 107, 53)',
                    borderColor: 'black',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString('en-PH');
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    </script>

    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            timeOut: 4000,
            positionClass: "toast-top-right"
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @elseif (session('error'))
            toastr.error("{{ session('error') }}");
        @elseif ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
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

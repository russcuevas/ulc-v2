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

        .activity-dot {
            margin-left: 10px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 6px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        /* Pulse animation */
        .pulse {
            animation: pulse-dot 1.5s infinite;
        }

        @keyframes pulse-dot {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.5);
                opacity: 0.5;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }


        .activity-item {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item p {
            margin: 0;
            font-weight: 500;
        }

        .activity-item small {
            color: #6c757d;
        }

        /* Scrollbar styling for modern browsers */
        #recent-activity-container::-webkit-scrollbar {
            width: 6px;
        }

        #recent-activity-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        #recent-activity-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        #recent-activity-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body>
    @include('secretary.fc.components.navbar')
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
            <h4>Financial Counselor</h4>

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
            <a style="text-align: right; text-decoration: none;" href="{{ route('secretary.fc.analytics.page') }}">View
                summary details</a>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-7">
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

            <div class="col-12 col-lg-5">
                <div class="card border-2 shadow-lg h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">
                                Recent Activity
                            </h5>
                            <a href="{{ route('secretary.fc.notification.page') }}"
                                class="small text-decoration-none">View all activity</a>
                        </div>

                        <!-- Scrollable container -->
                        <div id="recent-activity-container" style="overflow-y: auto; max-height: 400px;">
                            <div class="text-center text-muted small py-3">Loading...</div>
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

    <script>
        function getColorByActivityType(colorType) {
            switch (colorType) {
                case 'success':
                    return '#28a745';
                case 'info':
                    return '#17a2b8';
                case 'warning':
                    return '#ffc107';
                case 'danger':
                    return '#dc3545';
                default:
                    return '#6c757d';
            }
        }


        function fetchSecretaryNotifications() {
            fetch("{{ route('secretary.fc.fetch_notifications') }}")
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recent-activity-container');
                    container.innerHTML = '';

                    if (data.notifications.length === 0) {
                        container.innerHTML = '<p class="text-muted small">No recent activity.</p>';
                        return;
                    }

                    data.notifications.forEach(n => {
                        const dotColor = getColorByActivityType(n.color);
                        const pulseClass = n.is_read_secretary == 0 ? 'pulse' : '';

                        const html = `
                        <div class="activity-item d-flex gap-2 mb-3 position-relative">
                            <div class="activity-dot ${pulseClass}" style="background-color: ${dotColor};"></div>
                            <div class="flex-grow-1 position-relative">
                                <p class="small mb-1">${n.type}</p>
                                <small>${n.description}</small>
                                <div class="time-badge" style="position: absolute; bottom: 0; right: 0; font-size: 0.7rem; color: #6c757d;">
                                    ${n.time}
                                </div>
                            </div>
                        </div>
                        `;

                        container.insertAdjacentHTML('beforeend', html);
                    });
                })
                .catch(err => console.error(err));
        }

        // Fetch on page load
        document.addEventListener('DOMContentLoaded', fetchSecretaryNotifications);

        // Refresh every 15 seconds
        setInterval(fetchSecretaryNotifications, 15000);
    </script>

</body>

</html>

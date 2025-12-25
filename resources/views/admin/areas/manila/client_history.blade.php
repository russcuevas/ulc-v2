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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    @include('admin.components.navbar')

    <div class="main-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.page') }}"
                            class="text-decoration-none"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i
                            class="fas fa-file-invoice-dollar me-1"></i>
                        History</li>
                </ol>
            </nav>

            <div class="row">

                {{-- CLIENT INFORMATION --}}
                <div class="col-lg-3">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header text-white" style="background-color: #ff6b35">
                            <i class="fas fa-user"></i> Client Information
                        </div>
                        <div class="card-body" style="font-size: 12px !important;">
                            <p><strong>Full Name:</strong> {{ $client->fullname }}</p>
                            <p><strong>Phone:</strong> {{ $client->phone }}</p>
                            <p><strong>Gender:</strong> {{ $client->gender }}</p>
                            <p><strong>Address:</strong> {{ $client->address }}</p>
                            <p><strong>Date Registered:</strong><br>
                                {{ \Carbon\Carbon::parse($client->created_at)->format('F d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- LOAN HISTORY --}}
                <div class="col-lg-9">
                    <div class="card shadow-sm">
                        <div class="card-header text-white d-flex justify-content-between align-items-center"
                            style="background-color: #ff6b35">
                            <span>
                                <i class="fas fa-file-invoice-dollar"></i> Loan History
                            </span>
                            <span class="badge bg-primary">
                                Total Loans: {{ count($loans) }}
                            </span>
                        </div>

                        <div class="card-body table-responsive">
                            <table id="loanHistory" class="table table-bordered table-striped js-basic-example">
                                <thead class="table-light" style="font-size: 11px;">
                                    <tr>
                                        <th>PN #</th>
                                        <th>Release #</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Mode</th>
                                        <th>Amount</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 11px;">
                                    @foreach ($loans as $loan)
                                        <tr>
                                            <td>{{ $loan->pn_number }}</td>
                                            <td>{{ $loan->release_number }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($loan->loan_from)->format('M d, Y') }}
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($loan->loan_to)->format('M d, Y') }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge 
                                                {{ $loan->loan_status === 'new' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ ucfirst($loan->loan_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                ₱{{ number_format($loan->loan_amount, 2) }}
                                            </td>
                                            <td>
                                                ₱{{ number_format($loan->balance, 2) }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge 
                                                {{ $loan->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($loan->payment_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a style="text-decoration: none" href="">Check payment
                                                    history</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('assets/admin/js/script.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#loanHistory').DataTable({
                responsive: true,
                pageLength: 10,
                orderCellsTop: true,
                fixedHeader: true,
            });
        });
    </script>

    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {

                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false)
            })
        })();
    </script>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            timeOut: 4000,
            positionClass: "toast-top-right"
        };

        // TOASTR NOTIFICATIONS
    </script>


</body>

</html>

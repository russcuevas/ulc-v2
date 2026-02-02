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
    @include('collector.fc.components.navbar')

    <div class="main-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('collector.fc.dashboard.page') }}"
                            class="text-decoration-none"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-coins me-1"></i>
                        Collections</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm border-1">
                        <div class="d-flex justify-content-between align-items-start m-4">
                            <!-- LEFT: Title -->
                            <h5 class="card-title mb-0">
                                Payments Summary -
                                <span style="color: #ff6b35">
                                    Financial Counselor [{{ $area->area_name }}]
                                </span>
                            </h5>
                        </div>

                        <div class="card-body table-responsive">
                            <table id="loanHistory" class="table table-hover table-striped js-basic-example dataTable"
                                style="border: 2px solid rgba(0, 0, 0, 0.175) !important;;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference #</th>
                                        <th>Collections Date</th>
                                        <th>Total Collectibles</th>
                                        <th>Total Collections</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>


                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>
                                                <span
                                                    style="color: green; font-weight: 900;">{{ $payment->reference_number }}</span>
                                                <br>
                                                <span style="text-align: left; font-size: 10px;"
                                                    class="badge bg-danger">
                                                    {{ \Carbon\Carbon::parse($payment->created_at)->format('F j, Y - h:i A') }}
                                                    <br>
                                                    by: {{ $payment->created_by }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('F d, Y') }}
                                            </td>
                                            <td>₱{{ number_format($payment->daily, 2) }}</td>
                                            <td>₱{{ number_format($payment->collection, 2) }}</td>
                                            <td>
                                                <a href="{{ route('collector.fc.collections.payments', $payment->reference_number) }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    Collections <i class="fas fa-eye"></i>
                                                </a>
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
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>

</body>

</html>

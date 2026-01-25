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
    @include('secretary.valenzuela.components.navbar')

    <div class="main-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('secretary.valenzuela.dashboard.page') }}"
                            class="text-decoration-none"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('secretary.valenzuela.area.page') }}"
                            class="text-decoration-none"><i class="fas fa-location-dot me-1"></i> Valenzuela</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-coins me-1"></i>
                        Payments</li>
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
                                    Valenzuela [{{ $area->area_name }}]
                                </span>
                            </h5>

                            <!-- RIGHT: Buttons -->
                            <div class="d-flex flex-column align-items-end">
                                <form action="{{ route('secretary.area.valenzuela.payments.request', $area->id) }}"
                                    method="POST">
                                    @csrf
                                    <button type="button" id="openDatePicker"
                                        class="btn btn-outline-primary btn-sm d-flex align-items-center mb-2">
                                        <i class="fas fa-plus-circle"></i>&nbsp;Create Payments
                                    </button>
                                </form>

                                <a href="javascript:void(0)" id="printSummaryCollections"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-print me-1"></i> PRINT SUMMARY
                                </a>

                            </div>
                        </div>

                        <div class="card-body table-responsive">
                            <table id="loanHistory" class="table table-hover table-striped js-basic-example dataTable"
                                style="border: 2px solid rgba(0, 0, 0, 0.175) !important;;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference #</th>
                                        <th>Collector</th>
                                        <th>Due Date</th>
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
                                            <td>{{ $payment->collected_by }}</td>
                                            <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('F d, Y') }}
                                            </td>
                                            <td>₱{{ number_format($payment->daily, 2) }}</td>
                                            <td>₱{{ number_format($payment->collection, 2) }}</td>
                                            <td>
                                                <a href="{{ route('secretary.area.valenzuela.payments.clients', $payment->reference_number) }}"
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

    <script>
        document.getElementById('openDatePicker').addEventListener('click', function() {
            Swal.fire({
                title: 'Choose date and Collector',
                html: `
                        <div class="text-start">
                            <div class="row g-2">
                                <!-- Due Date -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="fa fa-calendar-day me-1 text-muted"></i>DUE DATE
                                    </label>
                                    <input
                                        id="dueDateInput"
                                        type="date"
                                        class="form-control"
                                        placeholder="Choose date"
                                    >
                                </div>

                                <!-- Collector -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="fa fa-user me-1 text-muted"></i>COLLECTOR
                                    </label>
                                    <select id="collectorSelect" class="form-select">
                                        <option value="">Select collector</option>
                                        @foreach ($collectors as $collector)
                                            <option value="{{ $collector->id }}">
                                                {{ $collector->fullname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    `,
                didOpen: () => {
                    flatpickr("#dueDateInput", {
                        dateFormat: "Y-m-d",
                        minDate: new Date() // disable past dates
                    });
                },
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const date = document.getElementById('dueDateInput').value;
                    const collector = document.getElementById('collectorSelect').value;

                    if (!date) {
                        Swal.showValidationMessage('Please select a date');
                    }
                    if (!collector) {
                        Swal.showValidationMessage('Please select a collector');
                    }

                    return {
                        date,
                        collector
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('secretary.area.valenzuela.payments.request', $area->id) }}";

                    // CSRF Token
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    // Due Date
                    const dateInput = document.createElement('input');
                    dateInput.type = 'hidden';
                    dateInput.name = 'due_date';
                    dateInput.value = result.value.date;
                    form.appendChild(dateInput);

                    // Collector
                    const collectorInput = document.createElement('input');
                    collectorInput.type = 'hidden';
                    collectorInput.name = 'collector';
                    collectorInput.value = result.value.collector;
                    form.appendChild(collectorInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
    <script>
        document.getElementById('printSummaryCollections').addEventListener('click', function() {
            Swal.fire({
                title: '<i class="fas fa-print me-1"> </i>Print Summary Collection',
                html: `
            <div class="row g-2 text-start">
                <div class="col-12">
                <label class="form-label fw-semibold">
                    <i class="fa fa-calendar-day me-1 text-muted"></i>FROM DATE
                </label>   
                <input type="date" id="fromDate" class="form-control">
                </div>
                <div class="col-12">
                <label class="form-label fw-semibold">
                    <i class="fa fa-calendar-day me-1 text-muted"></i>TO DATE
                </label>
                <input type="date" id="toDate" class="form-control">
                </div>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Print',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const from = document.getElementById('fromDate').value;
                    const to = document.getElementById('toDate').value;

                    if (!from || !to) {
                        Swal.showValidationMessage('Both dates are required');
                        return false;
                    }

                    if (from > to) {
                        Swal.showValidationMessage('From date cannot be later than To date');
                        return false;
                    }

                    return {
                        from,
                        to
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action =
                        "{{ route('secretary.area.valenzuela.payments.print.summary.collections', $area->id) }}";
                    form.target = '_blank';

                    form.innerHTML = `
            <input type="hidden" name="from_date" value="${result.value.from}">
            <input type="hidden" name="to_date" value="${result.value.to}">
        `;

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>


</body>

</html>

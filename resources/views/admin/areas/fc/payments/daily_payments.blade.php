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
</head>

<body>
    @include('admin.components.navbar')
    <div class="main-content">
        <div class="container-fluid">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.page') }}"
                            class="text-decoration-none"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item">
                        <a href="{{ url()->previous() }}" class="text-decoration-none">
                            <i class="fa fa-coins me-1"></i> Payments
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-coins me-1"></i>
                        Collections</li>
                </ol>
            </nav>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm border-1">

                        <div class="d-flex justify-content-between align-items-start m-4">
                            <div>
                                <h5 class="card-title mb-1">
                                    #{{ $referenceNumber }}
                                </h5>

                                <span class="badge bg-primary text-uppercase">
                                    {{ $payments->first()->collected_by ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="text-end">
                                <span class="text-muted fw-semibold">
                                    {{ \Carbon\Carbon::parse($payments->first()->due_date)->format('M d, Y') }}
                                </span> <br>

                                <span class="badge bg-primary text-uppercase">
                                    TOTAL CLIENTS: [{{ $payments->unique('fullname')->count() }}]
                                </span>
                            </div>
                        </div>
                        <div class="m-4 text-end" style="margin-top: 0px !important;">
                            <a href="{{ route('admin.area.fc.payments.print', $referenceNumber) }}" target="_blank"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-print me-1"></i> PRINT SUMMARY
                            </a>
                        </div>


                        <div class="card-body p-4">
                            @php
                                $totalCollectibles = $payments->sum('daily');
                                $totalCollected = $payments->sum(fn($p) => $p->collection ?? 0);
                                $clientsPaid = $payments
                                    ->filter(fn($p) => $p->collection > 0 && $p->type != 'NO PAYMENT')
                                    ->count();
                                $clientsNotPaid = $payments->filter(fn($p) => $p->type == 'NO PAYMENT')->count();
                            @endphp


                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="card shadow-sm h-100 card-left-orange">
                                        <div class="card-body">
                                            <small class="text-muted">TOTAL COLLECTIBLES</small>
                                            <h4 class="fw-bold text-danger mb-0">
                                                ₱{{ number_format($totalCollectibles, 2) }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card shadow-sm h-100 card-left-orange">
                                        <div class="card-body">
                                            <small class="text-muted">TOTAL COLLECTED</small>
                                            <h4 class="fw-bold text-success mb-0">
                                                ₱{{ number_format($totalCollected, 2) }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card shadow-sm h-100 card-left-orange">
                                        <div class="card-body">
                                            <small class="text-muted"># OF PAID</small>
                                            <h4 class="fw-bold text-success mb-0">
                                                {{ $clientsPaid }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card shadow-sm h-100 card-left-orange">
                                        <div class="card-body">
                                            <small class="text-muted"># OF NO PAYMENT</small>
                                            <h4 class="fw-bold text-danger mb-0">
                                                {{ $clientsNotPaid }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $totalCollectedAmount = $payments
                                        ->where('is_collected', 1)
                                        ->sum(fn($p) => $p->collection ?? 0);
                                    $totalPendingAmount = $payments
                                        ->where('is_collected', 0)
                                        ->sum(fn($p) => $p->collection ?? 0);
                                @endphp

                                <div class="d-flex flex-column align-items-end gap-2 mb-3 mt-5">

                                    <div class="d-flex gap-2 flex-wrap">

                                        {{-- Collect All --}}
                                        <form method="POST"
                                            action="{{ route('admin.fc.collect.all', $referenceNumber) }}"
                                            class="d-inline collect-all-form" data-amount="{{ $totalPendingAmount }}">
                                            @csrf
                                            <input type="hidden" name="type" value="CASH">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-coins me-1"></i> COLLECT PAYMENT
                                            </button>
                                        </form>

                                        {{-- Remind Payment --}}
                                        <form method="POST"
                                            action="{{ route('admin.fc.payments.remind.by.reference', $referenceNumber) }}"
                                            class="d-inline remind-all-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-bell me-1"></i> REMIND PAYMENT
                                            </button>
                                        </form>

                                        {{-- No Payment --}}
                                        <form method="POST"
                                            action="{{ route('admin.fc.no-payment.all', $referenceNumber) }}"
                                            class="d-inline no-payment-all-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-times-circle me-1"></i> NO PAYMENT
                                            </button>
                                        </form>

                                    </div>




                                    {{-- Totals below buttons --}}
                                    <table class="table table-bordered table-sm w-auto text-end mb-3">
                                        <thead>
                                            <tr>
                                                <th class="text-muted small">TOTAL COLLECTED</th>
                                                <th class="text-muted small">FOR COLLECT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold text-success fs-6">
                                                    ₱{{ number_format($totalCollectedAmount, 2) }}
                                                </td>
                                                <td class="fw-bold text-info fs-6">
                                                    ₱{{ number_format($totalPendingAmount, 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>


                            </div>



                            <div class="table-responsive">
                                <table class="table table-hover table-striped js-basic-example dataTable"
                                    style="border: 2px solid rgba(0, 0, 0, 0.175) !important;;">
                                    <thead style="font-size: 12px">
                                        <tr>
                                            <th>Client Name</th>
                                            <th>Loan Amount</th>
                                            <th>Old balance</th>
                                            <th>New Balance</th>
                                            <th>Daily</th>
                                            <th>Collection</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 12px;">
                                        @foreach ($payments as $payment)
                                            <tr>
                                                <td>{{ $payment->fullname }}</td>
                                                <td>₱{{ number_format($payment->loan_amount, 2) }}</td>
                                                <td>₱{{ number_format($payment->old_balance, 2) }}</td>

                                                <td>₱{{ number_format($payment->balance, 2) }}</td>
                                                <td>₱{{ number_format($payment->daily, 2) }}</td>
                                                <td>
                                                    @if (is_numeric($payment->collection) && $payment->collection > 0)
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span>₱{{ number_format($payment->collection, 2) }}</span>

                                                            <button
                                                                class="btn btn-sm btn-outline-secondary edit-collection-btn"
                                                                data-id="{{ $payment->id }}"
                                                                data-amount="{{ $payment->collection }}">
                                                                <i class="fas fa-pen"></i>
                                                            </button>

                                                        </div>
                                                    @elseif ($payment->type === 'NO PAYMENT')
                                                        <span style="color: red">₱0.00</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td>{{ $payment->type ?? '-' }}</td>
                                                <td>
                                                    @if ($payment->is_collected == 1)
                                                        <span class="badge bg-success">COLLECTED</span>
                                                    @elseif ($payment->collection != 0 && $payment->is_collected == 0)
                                                        <span class="badge bg-info text-white">FOR COLLECT</span>
                                                    @elseif ($payment->type == null && $payment->is_collected == 0)
                                                        <span class="badge bg-warning text-dark">WAIT FOR
                                                            COLLECTOR</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">NO PAYMENT</span>
                                                    @endif

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
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/admin/js/script.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.js-basic-example').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 20, 25, 50],
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




    {{-- EDIT COLLECTION --}}
    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.edit-collection-btn');
            if (!btn) return;

            const paymentId = btn.dataset.id;
            const currentAmount = btn.dataset.amount ?? 0;

            Swal.fire({
                title: 'Edit Collection Amount',
                input: 'number',
                inputLabel: 'Collection',
                inputValue: currentAmount,
                inputAttributes: {
                    min: 0,
                    step: 0.01
                },
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: (value) => {
                    if (value === '' || value < 0) {
                        Swal.showValidationMessage('Please enter a valid amount');
                    }
                    return value;
                }
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(`/admin/areas/fc/${paymentId}/update-collection`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            collection: result.value
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire('Updated!', 'Collection has been updated.', 'success')
                            .then(() => location.reload());
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Something went wrong.', 'error');
                    });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // COLLECT ALL
            document.querySelectorAll('.collect-all-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const amount = parseFloat(this.dataset.amount || 0)
                        .toLocaleString(undefined, {
                            minimumFractionDigits: 2
                        });

                    Swal.fire({
                        title: 'Confirm Collection',
                        text: `Are you sure you want to collect ₱${amount}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, collect',
                        cancelButtonText: 'Cancel'
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // REMIND ALL
            document.querySelectorAll('.remind-all-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Send Reminder?',
                        text: 'Are you sure you want to remind all clients?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, remind',
                        cancelButtonText: 'Cancel'
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // NO PAYMENT ALL
            document.querySelectorAll('.no-payment-all-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Mark No Payment',
                        text: 'Are you sure you want to mark NO PAYMENT for all no payment clients?',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, proceed',
                        cancelButtonText: 'Cancel'
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

        });
    </script>

</body>

</html>

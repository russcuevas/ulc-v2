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
                                </span>

                                <div class="mt-2">
                                    <a href="" class="btn btn-sm btn-primary">
                                        <i class="fas fa-print me-1"></i> PRINT
                                    </a>
                                </div>
                            </div>
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
                            </div>



                            <div class="table-responsive">
                                <table class="table table-hover table-striped js-basic-example dataTable"
                                    style="border: 2px solid rgba(0, 0, 0, 0.175) !important;;">
                                    <thead>
                                        <tr>
                                            <th>Client Name</th>
                                            <th>Loan Amount</th>
                                            <th>Balance</th>
                                            <th>Daily</th>
                                            <th>Collection</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr>
                                                <td>{{ $payment->fullname }}</td>
                                                <td>₱{{ number_format($payment->loan_amount, 2) }}</td>
                                                <td>₱{{ number_format($payment->balance, 2) }}</td>
                                                <td>₱{{ number_format($payment->daily, 2) }}</td>
                                                <td>
                                                    @if (is_numeric($payment->collection))
                                                        ₱{{ number_format($payment->collection, 2) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $payment->type ?? '-' }}</td>
                                                <td>
                                                    @if ($payment->collection > 0 && $payment->type)
                                                        <!-- Paid for this day -->
                                                        <span class="badge bg-success">PAID FOR THIS DAY</span>
                                                    @elseif(($payment->collection == 0 || $payment->collection === null) && $payment->type == 'NO PAYMENT')
                                                        <!-- No payment for this day -->
                                                        <span class="badge bg-danger">NO PAYMENT FOR THIS DAY</span>
                                                    @else
                                                        <!-- Not yet collected, show buttons -->
                                                        <button
                                                            class="btn btn-sm btn-outline-primary mb-1 collect-payment-btn"
                                                            data-client="{{ $payment->fullname }}"
                                                            data-id="{{ $payment->id }}"
                                                            data-balance="{{ $payment->balance }}">
                                                            Collect payment
                                                        </button>

                                                        <button
                                                            class="btn btn-sm btn-outline-primary mb-1 remind-payment-btn"
                                                            data-id="{{ $payment->id }}">
                                                            Remind payment
                                                        </button>

                                                        <button
                                                            class="btn btn-sm btn-outline-primary mb-1 no-payment-btn"
                                                            data-id="{{ $payment->id }}">
                                                            No payment
                                                        </button>
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

    {{-- COLLECT PAYMENT --}}
    <script>
        $(document).on('click', '.collect-payment-btn', function() {
            const clientName = $(this).data('client');
            const paymentId = $(this).data('id'); // ✅ This is your payment ID
            const balance = parseFloat($(this).data('balance'));

            Swal.fire({
                title: 'Collect Payment',
                html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="fa fa-user me-1 text-muted"></i> Client</label>
                    <input type="text" class="form-control" value="${clientName}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="fa fa-money-bill-wave me-1 text-muted"></i> Payment Type</label>
                    <select id="paymentType" class="form-select">
                        <option value="">Select type</option>
                        <option value="CASH">CASH</option>
                        <option value="GCASH">GCASH</option>
                        <option value="CHEQUE">CHEQUE</option>
                        <option value="ADVANCE">ADVANCE</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="fa fa-coins me-1 text-muted"></i> Amount (≤ ₱${balance.toFixed(2)})</label>
                    <input id="paymentAmount" type="number" class="form-control" min="0.01" step="0.01" placeholder="Enter amount">
                </div>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                focusConfirm: false,
                preConfirm: () => {
                    const amount = parseFloat(document.getElementById('paymentAmount').value);
                    const type = document.getElementById('paymentType').value;

                    if (!amount || amount <= 0) {
                        Swal.showValidationMessage('Please enter a valid amount');
                        return false;
                    }

                    if (amount > balance) {
                        Swal.showValidationMessage(
                            `Amount cannot exceed balance: ₱${balance.toFixed(2)}`);
                        return false;
                    }

                    if (!type) {
                        Swal.showValidationMessage('Please select a payment type');
                        return false;
                    }

                    return {
                        amount,
                        type
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/manila/collect-payment/${paymentId}`;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    const inputAmount = document.createElement('input');
                    inputAmount.type = 'hidden';
                    inputAmount.name = 'amount';
                    inputAmount.value = result.value.amount;
                    form.appendChild(inputAmount);

                    const inputType = document.createElement('input');
                    inputType.type = 'hidden';
                    inputType.name = 'type';
                    inputType.value = result.value.type;
                    form.appendChild(inputType);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Remind payment
        $(document).on('click', '.remind-payment-btn', function() {
            Swal.fire({
                title: 'Remind Payment',
                text: 'Would you like to remind the client about this payment?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, remind',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Reminder sent!',
                        text: 'The client has been reminded about the payment.'
                    });
                    // You can trigger an AJAX or email notification here if needed
                }
            });
        });

        // No payment
        $(document).on('click', '.no-payment-btn', function() {
            const paymentId = $(this).data('id');

            Swal.fire({
                title: 'Mark as No Payment',
                text: 'Are you sure you want to mark this payment as "No payment"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, mark it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/manila/no-payment/${paymentId}`;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    const collectionInput = document.createElement('input');
                    collectionInput.type = 'hidden';
                    collectionInput.name = 'amount';
                    collectionInput.value = 0;
                    form.appendChild(collectionInput);

                    const typeInput = document.createElement('input');
                    typeInput.type = 'hidden';
                    typeInput.name = 'type';
                    typeInput.value = 'No payment';
                    form.appendChild(typeInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>


</body>

</html>

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
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard.page') }}" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.client.page') }}" class="text-decoration-none">
                            <i class="fa-solid fa-users me-1"></i> Clients
                        </a>
                    </li>

                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fa-solid fa-user-pen me-1"></i> {{ $client->fullname }}
                    </li>

                </ol>
            </nav>

            <div class="card shadow-sm border-1 p-4">

                <div class="row">
                    <div class="col-12">
                        <div class="callout callout-info">
                            <form action="{{ route('admin.update.client.request', $client->id) }}" method="POST">

                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-3 text-primary">Client Information</h6>
                                    <button type="submit"
                                        class="btn btn-outline-primary btn-sm d-flex align-items-center">
                                        SAVE INFORMATION
                                    </button>
                                    <i class="fas fa-plus-circle d-none"></i>
                                </div>
                                @csrf
                                @method('PUT')

                                <div class="modal-body">
                                    <div class="row">

                                        {{-- LEFT COLUMN --}}
                                        <div class="col-md-6">

                                            {{-- FULL NAME --}}
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Full Name <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="fullname"
                                                    value="{{ $client->fullname }}" required>
                                            </div>

                                            {{-- PHONE --}}
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Phone <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="phone"
                                                    value="{{ $client->phone }}" required pattern="\d{11}"
                                                    minlength="11" maxlength="11">
                                            </div>

                                            {{-- ADDRESS --}}
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Address <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="address"
                                                    value="{{ $client->address }}" required>
                                            </div>

                                        </div>

                                        {{-- RIGHT COLUMN --}}
                                        <div class="col-md-6">

                                            {{-- AREA --}}
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Select Area <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control select2" name="area_id"
                                                    id="areaSelect{{ $client->id }}" required>
                                                    <option value="" disabled>SELECT AREA</option>

                                                    @foreach ($areas as $area)
                                                        <option value="{{ $area->id }}"
                                                            {{ $client->area_id == $area->id ? 'selected' : '' }}>
                                                            {{ $area->location_name }} — {{ $area->areas_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- GENDER --}}
                                            <div class="mb-3">
                                                <label class="form-label d-block">
                                                    Gender <span class="text-danger">*</span>
                                                </label>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        value="Male"
                                                        {{ $client->gender == 'Male' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Male</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        value="Female"
                                                        {{ $client->gender == 'Female' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Female</label>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </form>

                        </div>

                        <hr>

                        <!-- Main content -->
                        <div class="callout callout-info">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="mb-3 text-primary">PN List</h6>
                                <button class="btn btn-outline-primary btn-sm d-flex align-items-center"
                                    data-bs-toggle="modal" data-bs-target="#renewalClientModal">
                                    <i class="fas fa-plus-circle me-2"></i> RENEWAL
                                </button>
                                <i class="fas fa-plus-circle d-none"></i>
                            </div>
                            <!-- ADD MODAL -->
                            @include('admin.client.modal.renewal_modal')
                            <div class="table-responsive">
                                <table
                                    class="table table-hover table-striped js-basic-example dataTable text-wrap w-100"
                                    style="table-layout: fixed; min-width: 1000px; border: 2px solid rgba(0, 0, 0, 0.175);">

                                    <thead class="table-light" style="font-size: 11px !important">
                                        <tr>
                                            <th>Profiling PN</th>
                                            <th>Released PN</th>
                                            <th>Mode</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Loan Amount</th>
                                            <th>Terms</th>
                                            <th>Principal</th>
                                            <th>Balance</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 11px !important">
                                        @foreach ($loans as $loan)
                                            <tr>
                                                <td>{{ $loan->pn_number }}</td>
                                                <td>{{ $loan->release_number }}</td>
                                                <td>{{ ucfirst($loan->loan_status) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($loan->loan_from)->format('M d, Y') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($loan->loan_to)->format('M d, Y') }}</td>
                                                <td>₱{{ number_format($loan->loan_amount, 2) }}</td>
                                                <td>{{ $loan->loan_terms }}</td>
                                                <td>₱{{ number_format($loan->principal, 2) }}</td>
                                                <td>₱{{ number_format($loan->balance, 2) }}</td>

                                                <td>
                                                    <span
                                                        class="badge 
                                                        {{ $loan->payment_status == 'paid' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ strtoupper($loan->payment_status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>


                                </table>
                            </div>
                        </div>
                        <!-- /.invoice -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
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
                lengthMenu: [5, 10, 25, 50],
                order: [
                    [2, 'desc']
                ],
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
</body>

</html>

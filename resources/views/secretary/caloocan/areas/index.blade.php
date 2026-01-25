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
    <style>
        .small-checkbox {
            transform: scale(0.8);
            transform-origin: left center;
        }
    </style>

</head>

<body>
    @include('secretary.caloocan.components.navbar')
    <div class="main-content">
        <div class="container-fluid">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('secretary.caloocan.dashboard.page') }}"
                            class="text-decoration-none"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa-solid fa-map me-1"></i>
                        Areas</li>
                </ol>
            </nav>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm border-1">
                        <div class="d-flex justify-content-between align-items-start m-4">
                            <h5 class="card-title mb-0">
                                Caloocan
                            </h5>

                            <!-- RIGHT: Buttons -->
                            <div class="d-flex flex-column align-items-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-print me-1"></i>PRINT REPORTS
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" style="cursor: pointer" id="printSalesReports">->
                                                SALES</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped js-basic-example dataTable"
                                    style="border: 2px solid rgba(0, 0, 0, 0.175) !important;;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Areas</th>
                                            <th>Number of Clients</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($areas as $area)
                                            <tr>
                                                <td>{{ $area->areas_name }}</td>
                                                <td>{{ $area->clients_count }}</td>
                                                <td>
                                                    <a href="{{ route('secretary.area.caloocan.clients.page', $area->id) }}"
                                                        class="btn btn-sm btn-outline-info">
                                                        View Clients <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('secretary.area.caloocan.payments', $area->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        Payments <i class="fa fa-coins"></i>
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
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/admin/js/script.js') }}"></script>
    <script>
        const AREAS = @json($areas);
    </script>
    <script>
        document.getElementById('printSalesReports').addEventListener('click', function() {

            let areaOptions = `<option value="">-- Select Area --</option>`;
            AREAS.forEach(area => {
                areaOptions += `<option value="${area.id}">${area.areas_name}</option>`;
            });

            Swal.fire({
                title: '<i class="fas fa-print me-1"></i> Print Sales Reports',
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

                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="fa fa-location-dot me-1 text-muted"></i>AREA
                                    </label>                               
                                    <select id="areaId" class="form-select">
                        ${areaOptions}
                    </select>
                </div>

                <div class="col-12 mt-2 d-flex align-items-center">
                    <input type="checkbox" class="form-check-input small-checkbox me-2" id="allAreas">
                    <label class="fw-semibold mb-0" style="font-size:10px;" for="allAreas">
                        [CLICK THIS BOX IF ALL AREAS]
                    </label>
                </div>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Print',
                cancelButtonText: 'Cancel',

                allowOutsideClick: false,
                allowEscapeKey: false,

                preConfirm: () => {
                    const from = document.getElementById('fromDate').value;
                    const to = document.getElementById('toDate').value;
                    const areaId = document.getElementById('areaId').value;
                    const allAreas = document.getElementById('allAreas').checked;

                    if (!from || !to) {
                        Swal.showValidationMessage('Both dates are required');
                        return false;
                    }

                    if (from > to) {
                        Swal.showValidationMessage('From date cannot be later than To date');
                        return false;
                    }

                    if (!allAreas && !areaId) {
                        Swal.showValidationMessage('Select an area or check ALL AREAS');
                        return false;
                    }

                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = "{{ route('secretary.area.caloocan.print.sales') }}";
                    form.target = '_blank';

                    form.innerHTML = `
                <input type="hidden" name="from_date" value="${from}">
                <input type="hidden" name="to_date" value="${to}">
                <input type="hidden" name="area_id" value="${areaId}">
                <input type="hidden" name="all_areas" value="${allAreas ? 1 : 0}">
            `;

                    document.body.appendChild(form);
                    form.submit();

                    return false;
                }
            });
        });
    </script>
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
</body>

</html>

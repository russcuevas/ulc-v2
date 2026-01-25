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
    @include('secretary.valenzuela.components.navbar')
    <div class="main-content">
        <div class="container-fluid">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('secretary.valenzuela.dashboard.page') }}"
                            class="text-decoration-none"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa-solid fa-bell"></i>
                        Notifications</li>
                </ol>
            </nav>
            <div class="row">

                <div class="col-12 col-md-12 col-lg-12 mb-2">
                    <div class="card shadow-sm border-1">
                        <div class="d-flex justify-content-between align-items-center m-4">
                            <h5 class="card-title mb-0">All notifications</h5>
                            <div class="mb-3 text-end">
                                <form action="{{ route('secretary.valenzuela.notifications.mark.all.read') }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        MARK ALL AS READ ✔
                                    </button>
                                </form>
                            </div>
                        </div>


                        <div class="card-body p-4">
                            <div class="table-responsive">



                                <table class="table js-basic-example dataTable"
                                    style="border: 2px solid rgba(0, 0, 0, 0.175) !important;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="display:none;">ReadOrder</th>
                                            <th>Summary</th>
                                            <th>Role</th>
                                            <th>Type</th>
                                            <th>Date-Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($activities as $activity)
                                            <tr
                                                class="{{ $activity->is_read_secretary == 1 ? 'table-secondary text-muted' : 'table-primary' }}">
                                                <td style="display:none;">{{ $activity->is_read_secretary }}</td>
                                                <td>{!! $activity->description !!}</td>
                                                <td>
                                                    <span
                                                        style="text-align: left; font-size: 10px; text-transform: capitalize"
                                                        class="badge bg-primary">
                                                        {{ $activity->role ?? 'Unknown' }}
                                                    </span>
                                                </td>
                                                <td>{{ $activity->type }}</td>
                                                <td class="text-wrap">
                                                    <span style="text-align: left; font-size: 10px;"
                                                        class="badge bg-danger">
                                                        {{ \Carbon\Carbon::parse($activity->created_at)->format('F j, Y - h:i A') }}
                                                        <br>
                                                        by: {{ $activity->fullname ?? 'Unknown' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($activity->is_read_secretary == 1)
                                                        <span class="badge bg-success">Read</span>
                                                    @else
                                                        <span class="badge bg-info">Unread</span>
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
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/admin/js/script.js') }}"></script>
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
            $('.js-basic-example').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                    targets: 0,
                    visible: false,
                    searchable: false
                }]
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

</body>

</html>

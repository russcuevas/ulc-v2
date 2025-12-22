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
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa-solid fa-users me-1"></i>
                        Clients</li>
                </ol>
            </nav>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm border-1">
                        <div class="d-flex justify-content-between align-items-center m-4">
                            <h5 class="card-title mb-0">List of Clients</h5>
                            <button class="btn btn-outline-primary btn-sm d-flex align-items-center"
                                data-bs-toggle="modal" data-bs-target="#addClientModal">
                                <i class="fas fa-plus-circle me-2"></i> ADD CLIENT
                            </button>
                            <i class="fas fa-plus-circle d-none"></i>
                        </div>

                        <!-- ADD MODAL -->
                        @include('admin.client.modal.add_modal')
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped js-basic-example dataTable"
                                    style="min-width: 1000px; border: 2px solid rgba(0, 0, 0, 0.175) !important;;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fullname</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Area</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($clients as $client)
                                            <tr>
                                                <td class="text-wrap">{{ $client->fullname }}</td>
                                                <td class="text-wrap">{{ $client->phone }}</td>
                                                <td class="text-wrap">{{ $client->address }}</td>
                                                <td class="text-wrap">
                                                    {{ $client->location_name }} -
                                                    <span style="color: #ff6b35">[{{ $client->areas_name }}]</span>
                                                </td>
                                                <td class="text-wrap">
                                                    {{ \Carbon\Carbon::parse($client->created_at)->format('F j, Y - h:i A') }}
                                                    <span class="badge rounded-pill bg-success">
                                                        by: {{ $client->created_by }}
                                                    </span>
                                                </td>
                                                <td class="text-nowrap">
                                                    <a href="{{ route('admin.edit.client.page', $client->id) }}"
                                                        class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-pencil"></i>
                                                    </a>


                                                    <form
                                                        action="{{ route('admin.delete.client.request', $client->id) }}"
                                                        method="POST" class="delete-client-form d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger delete-btn"
                                                            data-client-id="{{ $client->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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
    <script>
        $(document).ready(function() {
            $('.delete-btn').click(function(e) {
                e.preventDefault();

                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>

</html>

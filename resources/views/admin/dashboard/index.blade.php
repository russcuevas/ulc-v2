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
</head>

<body>
    @include('admin.components.navbar')

    <div class="main-content">
        <div class="row g-4 mb-4">
            <h4>Dashboard</h4>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-2 shadow-lg h-100 card-left-orange">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="text-body-secondary fw-medium">Total Loans</span>
                            <div class="icon-box bg-danger-subtle text-danger">
                                <i class="fa-solid fa-coins"></i>
                            </div>
                        </div>
                        <h2 class="h3 fw-bold mb-1">₱</h2>
                        <span class="text-success small"><i class="bi bi-arrow-up"></i>
                            +₱ over 2024</span>
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
                        <h2 class="h3 fw-bold mb-1"></h2>
                        <span class="text-success small">+ compared to 2024</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-2 shadow-lg h-100 card-left-orange">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="text-body-secondary fw-medium">Daily Collections</span>
                            <div class="icon-box bg-info-subtle text-info">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </div>
                        </div>
                        <h2 class="h3 fw-bold mb-1">₱</h2>
                        <span class="text-body-secondary small">
                            As of <span id="currentTime"></span>
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4">

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/admin/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

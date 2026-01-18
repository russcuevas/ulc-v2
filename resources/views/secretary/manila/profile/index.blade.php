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

    {{-- NAVBAR --}}
    @include('secretary.manila.components.navbar')

    <div class="main-content">
        <div class="container-fluid">

            {{-- BREADCRUMB --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('secretary.manila.dashboard.page') }}" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fa-solid fa-user me-1"></i> Profile
                    </li>
                </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="row g-4">

                    {{-- LEFT PROFILE CARD --}}
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-1 text-center">
                            <div class="card-body p-4">

                                {{-- AVATAR --}}
                                @php
                                    $initial = strtoupper(substr($secretary->fullname, 0, 1));
                                @endphp

                                <div class="d-flex justify-content-center mb-3">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center"
                                        style="width: 90px; height: 90px; font-size: 36px; font-weight: 600; background-color: #ff6b35">
                                        {{ $initial }}
                                    </div>
                                </div>

                                {{-- NAME --}}
                                <h5 class="mb-1">{{ $secretary->fullname }}</h5>

                                {{-- EMAIL --}}
                                <p class="text-muted small mb-3">{{ $secretary->email }}</p>

                                <hr>

                                {{-- META --}}
                                <div class="text-start small">
                                    <p class="mb-2">
                                        <i class="fa-solid fa-user-shield me-2" style="color: #ff6b35"></i>
                                        Role: <strong>Administrator</strong>
                                    </p>

                                    @php
                                        $isVerified = strtolower(trim($secretary->status)) === 'verified';
                                    @endphp

                                    <p class="mb-2">
                                        <i class="fa-solid fa-circle me-2"
                                            style="color: {{ $isVerified ? '#22c55e' : '#ef4444' }}"></i>
                                        Status:
                                        <span class="fw-semibold">
                                            {{ $isVerified ? 'Verified' : 'Not Verified' }}
                                        </span>
                                    </p>



                                    <p class="mb-0">
                                        <i class="fa-solid fa-clock me-2" style="color: #ff6b35"></i>
                                        Joined:
                                        {{ \Carbon\Carbon::parse($secretary->created_at)->format('F j, Y') }}
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- RIGHT PROFILE FORM --}}
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-1">


                            <div class="card-body p-4">
                                <form action="{{ route('secretary.profile.update') }}" method="POST"
                                    class="needs-validation" novalidate>
                                    @csrf

                                    {{-- PERSONAL INFORMATION --}}
                                    <h6 class="mb-3 text-muted">Personal Information <br>
                                        <span style="font-size: 13px; color: red;">Note: If you want any personal
                                            information changes
                                            please contact admin about this</span>
                                    </h6>
                                    <div class="row">
                                        {{-- FULL NAME --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Full Name <span
                                                    style="color: rgb(126, 30, 30)">*</span></label>
                                            <input
                                                style="text-transform: capitalize; background-color: gray; color: white;"
                                                type="text" name="fullname" class="form-control" required
                                                value="{{ old('fullname', $secretary->fullname) }}" readonly>
                                            <div class="invalid-feedback">Full name is required.</div>
                                        </div>

                                        {{-- PHONE --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone <span
                                                    style="color: rgb(126, 30, 30)">*</span></label>
                                            <input
                                                style="text-transform: capitalize; background-color: gray; color: white;"
                                                type="text" name="phone_number" class="form-control" required
                                                value="{{ old('phone_number', $secretary->phone_number) }}" readonly>
                                            <div class="invalid-feedback">Phone number is required.</div>
                                        </div>
                                    </div>

                                    {{-- GENDER --}}
                                    <div class="mb-3">
                                        <label>Gender</label>
                                        <input style="text-transform: capitalize; background-color: gray; color: white;"
                                            type="text" name="gender" class="form-control" required
                                            value="{{ old('gender', $secretary->gender) }}" readonly>
                                    </div>

                                    <hr>

                                    {{-- ACCOUNT INFORMATION --}}
                                    <h6 class="mb-3 text-muted">Account Information</h6>

                                    <div class="row">
                                        {{-- EMAIL --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email Address <span
                                                    style="color: rgb(126, 30, 30)">*</span></label>
                                            <input style="background-color: gray; color: white;" type="email"
                                                name="email" class="form-control" required
                                                value="{{ old('email', $secretary->email) }}" readonly>
                                            <div class="invalid-feedback">Valid email required.</div>
                                        </div>

                                        {{-- PASSWORD --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" name="password" class="form-control">
                                            <small class="text-muted">Leave blank to keep current password</small>
                                        </div>
                                    </div>

                                    {{-- CONFIRM PASSWORD --}}
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <input type="password" name="password_confirmation" class="form-control">
                                        </div>
                                    </div>

                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-primary px-4" id="saveProfileBtn">
                                            <span class="btn-text">
                                                <i class="fa-solid fa-floppy-disk me-1"></i>
                                                Save Changes
                                            </span>

                                            <span class="btn-spinner d-none">
                                                <span class="spinner-border spinner-border-sm me-1"
                                                    role="status"></span>
                                                Saving...
                                            </span>
                                        </button>


                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/admin/js/script.js') }}"></script>
    {{-- FORM VALIDATION --}}
    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
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

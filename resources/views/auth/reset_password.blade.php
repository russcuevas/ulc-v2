<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ULC-System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('assets/auth/style.css') }}">
</head>

<body class="bg-light d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="card border-0 shadow rounded-4">
                    <div class="card-body p-4 p-md-5">

                        <h3 class="fw-semibold text-left mb-5">
                            Reset Your Password
                            <br>
                            <span class="text-muted" style="font-size: 11px;">
                                Enter the reset code sent to your email and choose a new password.
                            </span>
                        </h3>

                        <form id="resetForm" method="POST" action="{{ route('password.reset') }}"
                            class="needs-validation" novalidate>
                            @csrf

                            <!-- STEP 1 -->
                            <div id="verifyStep" class="{{ session('step') === 'password' ? 'd-none' : '' }}">

                                @php
                                    $emailValue = old('email', $email ?? '');
                                    $hasEmail = !empty($emailValue);
                                @endphp

                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        Email Address <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ $emailValue }}"
                                        class="form-control rounded-3 shadow-sm" placeholder="Enter your email"
                                        @if ($hasEmail) readonly @endif
                                        style="
            background-color: {{ $hasEmail ? '#e9ecef' : '#fff' }};
            color: {{ $hasEmail ? '#495057' : '#212529' }};
        ">
                                </div>


                                <div class="mb-4 position-relative">
                                    <label class="form-label fw-medium">
                                        Reset Code <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="reset_code" id="reset_code"
                                        value="{{ old('reset_code') }}"
                                        class="form-control rounded-3 shadow-sm text-center pe-5"
                                        placeholder="6-digit code" required maxlength="6" autocomplete="off"
                                        inputmode="numeric">

                                </div>

                                <button type="button" id="verifyBtn"
                                    class="btn btn-primary w-100 py-2 rounded-3 fw-medium shadow">
                                    Verify Code
                                </button>
                            </div>

                            <!-- STEP 2 -->
                            <div id="passwordStep" class="{{ session('step') === 'password' ? '' : 'd-none' }}">

                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        New Password <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="password" class="form-control rounded-3 shadow-sm"
                                        placeholder="New password" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-medium">
                                        Confirm Password <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="password_confirmation"
                                        class="form-control rounded-3 shadow-sm" placeholder="Confirm password"
                                        required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-medium shadow">
                                    Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @if (session('step') === 'password')
        <script>
            document.getElementById('email').readOnly = true;
            document.getElementById('reset_code').readOnly = true;
        </script>
    @endif

    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if ($errors->any())
            toastr.error("{{ $errors->first() }}");
        @endif
    </script>


    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            timeOut: 4000,
            positionClass: "toast-top-right"
        };
    </script>
    <script>
        $(document).ready(function() {
            $('#reset_code').on('input', function() {
                let val = $(this).val();
                val = val.replace(/\D/g, '');
                $(this).val(val);

                if (val.length === 6) {
                    $('#reset_code').prop('disabled', true);
                    $('#verifyBtn').prop('disabled', true);
                    let email = $('#email').val();

                    $.ajax({
                        url: "{{ route('password.verify.code') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            email: email,
                            reset_code: val
                        },
                        success: function(res) {
                            toastr.success(res.message);
                            $('#email, #reset_code').prop('readonly', true);
                            $('#verifyStep').addClass('d-none');
                            $('#passwordStep').removeClass('d-none');
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message ?? 'Verification failed.');
                        },
                        complete: function() {
                            $('#reset_code').prop('disabled', false);
                            $('#verifyBtn').prop('disabled', false);
                        }
                    });
                }
            });

            $('#verifyBtn').on('click', function() {
                $('#reset_code').trigger('input');
            });
        });
    </script>


</body>

</html>

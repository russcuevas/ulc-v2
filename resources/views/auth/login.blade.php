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
    <style>
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .loader-content {
            text-align: center;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #ff6b35;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Class to hide the loader */
        .loader-hidden {
            opacity: 0;
            visibility: hidden;
        }
    </style>
</head>

<body class="bg-light">
    <div id="preloader">
        <div class="loader-content">
            <div class="spinner"></div>
            <p>Loading ULC System...</p>
        </div>
    </div>
    <div class="container-fluid p-0 d-flex min-vh-100">
        <div
            class="col-md-4 col-xl-3 bg-ulc-primary text-white p-5 p-lg-5 d-none d-md-flex flex-column justify-content-between shadow-lg">
            <div class="d-flex flex-column" style="margin-top: 5rem;">
                <div class="d-flex align-items-center mb-5">
                    <div class="logo-placeholder me-3"></div>
                    <span class="fs-4 fw-bold text-uppercase">ulc</span>
                </div>

                <div class="mt-4">
                    <h1 class="display-5 fw-bold mb-3">
                        Ultraritz Lending Corporation
                    </h1>
                </div>
            </div>
        </div>

        <div
            class="col-12 col-md-8 col-xl-9 d-flex justify-content-center align-items-center p-3 p-sm-5 position-relative">
            <div class="w-100" style="max-width: 450px;">
                <h2 class="fs-3 fw-semibold mb-5 text-left text-dark">
                    Login to ULC System
                </h2>

                @if (session('error'))
                    <div class="error-message mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="error-message mb-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.login.request') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label fw-medium">Email Address <span
                                style="color: red">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="Email Address" class="form-control rounded-3 shadow-sm" required>

                        <div class="invalid-feedback">Please enter your email address.</div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-medium">Password <span
                                style="color: red">*</span></label>
                        <input type="password" id="password" name="password" placeholder="Password"
                            class="form-control rounded-3 shadow-sm" required>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>

                    <!-- Login Button -->
                    <button type="submit"
                        class="btn btn-primary w-100 d-flex justify-content-center align-items-center py-2 rounded-3 fw-medium shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-log-in me-2">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" x2="3" y1="12" y2="12" />
                        </svg>
                        <span>Login</span>
                    </button>
                </form>

                <!-- Utility Links -->
                <div class="d-flex justify-content-between mt-3 fs-6">
                    <a href="#" class="text-primary text-decoration-none fw-medium"></a>
                    <a href="#" class="text-primary text-decoration-none fw-medium" data-bs-toggle="modal"
                        data-bs-target="#forgotPasswordModal">
                        Forgot password?
                    </a>
                </div>

                <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordLabel"
                    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-dialog">
                        <div class="modal-content rounded-4 shadow">
                            <div class="modal-header border-0">
                                <h5 class="modal-title fw-semibold" id="forgotPasswordLabel">
                                    Forgot Password
                                    <br>
                                    <span class="text-muted mb-4" style="font-size: 12px;">
                                        Enter your email address and we’ll send you a password reset link.
                                    </span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <form method="POST" action="{{ route('password.send.code') }}" class="needs-validation"
                                novalidate>
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="reset-email" class="form-label fw-medium">
                                            Email Address <span style="color:red">*</span>
                                        </label>
                                        <input type="email" id="reset-email" name="email"
                                            class="form-control rounded-3 shadow-sm"
                                            placeholder="Enter your email address" required>
                                        <div class="invalid-feedback">
                                            Please enter a valid email address.
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="btn btn-primary rounded-3 fw-medium d-flex align-items-center justify-content-center"
                                        id="sendResetBtn">
                                        <span class="btn-text">Send Reset Link</span>
                                        <span class="spinner-border spinner-border-sm ms-2 d-none"
                                            role="status"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $('#forgotPasswordModal form').on('submit', function() {
            if (!this.checkValidity()) {
                return;
            }
            let btn = $('#sendResetBtn');
            btn.prop('disabled', true);
            btn.find('.btn-text').text('Sending...');
            btn.find('.spinner-border').removeClass('d-none');
            $('#forgotPasswordModal .btn-close').prop('disabled', true);
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
        @endif
    </script>

    <script>
        window.addEventListener("load", function() {
            const loader = document.getElementById("preloader");
            setTimeout(() => {
                loader.classList.add("loader-hidden");
            }, 500);
        });
    </script>
</body>

</html>

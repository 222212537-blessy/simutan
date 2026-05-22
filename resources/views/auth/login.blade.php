<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login | SIMUTAN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css -->
    <link href="{{ asset('backend/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />

    <style>
        body {
            color: #fff;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .wrapper-page {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: #fff;
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }

        .card-body {
            padding: 2rem;
        }

        .btn-info {
            background-color: #2575fc;
            border-color: #2575fc;
            color: #fff;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-info:hover {
            background-color: #1b4f91;
            border-color: #1b4f91;
        }

        .form-control {
            border-radius: 25px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.12);
        }

        .form-group label {
            font-weight: 500;
            color: #333;
        }

        .form-group a {
            color: #2575fc;
            text-decoration: none;
        }

        .form-group a:hover {
            text-decoration: underline;
        }

        .text-center {
            color: #333;
        }

        h1 {
            color: #333;
        }

        .input-group-text {
            cursor: pointer;
            background-color: transparent;
            border: none;
            padding-left: 0;
        }

        .input-group-text i {
            font-size: 1.2rem;
            color: #333;
        }

        .input-group.position-relative {
            display: flex;
            align-items: center;
        }

        .input-group-text.position-absolute {
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background-color: transparent;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        .input-group-text.position-absolute i {
            font-size: 1.2rem;
            color: #333;
        }

        .btn-group>.btn-group:not(:last-child)>.btn, .btn-group>.btn:not(:last-child):not(.dropdown-toggle), .input-group.has-validation>.dropdown-toggle:nth-last-child(n+4), .input-group.has-validation>:nth-last-child(n+3):not(.dropdown-toggle):not(.dropdown-menu), .input-group:not(.has-validation)>.dropdown-toggle:nth-last-child(n+3), .input-group:not(.has-validation)>:not(:last-child):not(.dropdown-toggle):not(.dropdown-menu){
            border-radius: 25px;
        }

        .spinner-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #eff3f6;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none; /* Hide by default */
        }

        .spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            height: 200px;
            margin-top: -20px;
        }

        .spinner img {
            width: 20rem;
            max-width: 100%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.5; }
            100% { transform: scale(1); opacity: 1; }
        }

        .alert {
            font-size: 0.875rem;
            color: #f44336; /* Red color for errors */
            margin-top: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="wrapper-page">
        <div class="container-fluid p-0">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mt-4">
                        <div class="mb-3">
                            <a href="index.html" class="auth-logo">
                                <img src="{{ asset('backend/assets/images/logo-bps.png') }}" height="60" class="logo-dark mx-auto" alt="">
                                <img src="{{ asset('backend/assets/images/logo-bps.png') }}" height="60" class="logo-light mx-auto" alt="">
                            </a>
                        </div>
                    </div>

                    <h1 class="text-center font-size-34 mb-0"><b>SIMUTAN</b></h1>
                    <h3 class="text-center font-size-34 mt-0">Masuk</h3>

                    <div class="p-3">
                        <form class="form-horizontal mt-3" method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="username">Username</label>
                                <input class="form-control" type="text" id="username" name="username" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <div class="input-group position-relative">
                                    <input class="form-control" id="password" name="password" type="password" required="" placeholder="Password">
                                    <span class="input-group-text position-absolute" id="togglePassword">
                                        <i class="ti ti-eye text-dark"></i>
                                    </span>
                                </div>
                                @if ($errors->any())
                                    <div class="alert">
                                        {{ $errors->first() }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group mb-3 text-end">
                                <button class="btn btn-info w-auto waves-effect waves-light" type="submit" id="loginButton">Masuk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Spinner -->
    <div id="spinner" class="spinner-wrapper">
        <div class="spinner">
            <img src="{{ asset('backend/assets/images/logosimutan_new.png') }}" alt="Logo">
        </div>
    </div>

    <!-- Javascript -->
    <script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>

    <script>
        const form = document.querySelector('#loginForm');
        const spinner = document.querySelector('#spinner');
        const loginButton = document.querySelector('#loginButton');

        form.addEventListener('submit', function() {
            spinner.style.display = 'flex'; // Show spinner when form is submitted
            loginButton.disabled = true; // Disable the login button to prevent multiple submissions
        });
    </script>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('mousedown', function () {
            // Show the password
            password.setAttribute('type', 'text');
            this.querySelector('i').classList.remove('ti-eye');
            this.querySelector('i').classList.add('ti-eye-closed');
        });

        togglePassword.addEventListener('mouseup', function () {
            // Hide the password
            password.setAttribute('type', 'password');
            this.querySelector('i').classList.remove('ti-eye-closed');
            this.querySelector('i').classList.add('ti-eye');
        });

        togglePassword.addEventListener('mouseout', function () {
            password.setAttribute('type', 'password');
            this.querySelector('i').classList.remove('ti-eye-closed');
            this.querySelector('i').classList.add('ti-eye');
        });
    </script>
</body>

</html>

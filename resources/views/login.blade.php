<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin</title>
    <link rel="shortcut icon" href="{{ asset('assets/assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets/compiled/css/app-dark.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/assets/compiled/css/auth.css')}}">
</head>

<body>
    <script src="{{ asset('assets/assets/static/js/initTheme.js') }}"></script>
    <div id="auth">

<div class="row h-100">
    <div class="col-lg-5 col-12">
        <div id="auth-left">
            <h1 class="auth-title">Log in.</h1>

            <form id="login-form" action="{{ route('submitLogin') }}" method="POST">
                @csrf
                <div class="form-group position-relative has-icon-left mb-4">
                    <input type="text" class="form-control form-control-xl" id="username" name="username" placeholder="Username">
                    <div class="form-control-icon">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
                <div class="form-group position-relative has-icon-left mb-4">
                    <input type="password" class="form-control form-control-xl" id="password" name="password" placeholder="Password">
                    <div class="form-control-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <!-- Tambahkan tombol untuk melihat password -->
                    <button type="button" class="btn btn-sm position-absolute end-0 top-0 mt-3 me-3" id="togglePassword">
                        <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                    </button>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5 btn-login">Log in</button>
            </form>
        </div>
    </div>
    <div class="col-lg-7 d-none d-lg-block">
        <div id="auth-right">
        </div>
    </div>
</div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/assets/compiled/js/app.js') }}"></script>
<script src="{{ asset('assets/assets/static/js/auth.js') }}"></script>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var passwordField = document.getElementById('password');
        var passwordIcon = document.getElementById('togglePasswordIcon');

        if (passwordField.type === "password") {
            passwordField.type = "text";
            passwordIcon.classList.remove('bi-eye-slash');
            passwordIcon.classList.add('bi-eye');
        } else {
            passwordField.type = "password";
            passwordIcon.classList.remove('bi-eye');
            passwordIcon.classList.add('bi-eye-slash');
        }
    });

    @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: '{{ $errors->first() }}'
            });
    @endif
</script>

</html>

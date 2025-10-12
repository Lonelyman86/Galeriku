<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk | Galeriku</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
      background-color: #fff;
      font-family: 'Poppins', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .login-box {
      width: 400px;
      border-radius: 12px;
      box-shadow: 0 0 18px rgba(0, 0, 0, 0.08);
      padding: 36px 40px;
      background-color: #fff;
      text-align: center;
    }

    .login-box img.logo {
      width: 45px;
      margin-bottom: 8px;
    }

    h2 {
      font-weight: 700;
      font-size: 22px;
      margin-bottom: 8px;
    }

    .login-box p {
      color: #555;
      font-size: 14px;
      margin-bottom: 24px;
    }

    .form-control {
      border-radius: 24px;
      padding: 10px 16px;
      font-size: 15px;
      background-color: #f7f7f7;
      border: 1px solid #ddd;
    }

    .form-control:focus {
      border-color: #cd2026;
      box-shadow: 0 0 0 0.2rem rgba(205, 32, 38, 0.15);
    }

    .btn-login {
      width: 100%;
      background-color: #e60023;
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 24px;
      font-weight: 600;
      margin-top: 8px;
    }

    .btn-login:hover {
      background-color: #cc001f;
    }

    .forgot {
      font-size: 13px;
      text-decoration: none;
      color: #0066cc;
      display: block;
      margin-top: 8px;
    }

    .forgot:hover {
      text-decoration: underline;
    }

    .divider {
      text-align: center;
      font-size: 13px;
      color: #999;
      margin: 16px 0;
      position: relative;
    }

    .divider::before,
    .divider::after {
      content: "";
      position: absolute;
      top: 50%;
      width: 35%;
      height: 1px;
      background: #ddd;
    }

    .divider::before {
      left: 0;
    }

    .divider::after {
      right: 0;
    }

    .signup {
      font-size: 14px;
      color: #333;
      margin-top: 16px;
    }

    .signup a {
      font-weight: 600;
      text-decoration: none;
      color: #e60023;
    }

    .signup a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>

  <div class="login-box">
    <img src="{{ asset('assets/img/galeriku-icon.png') }}" alt="Logo" class="logo">
    <h2>Selamat datang di Galeriku</h2>
    <p>Masuk untuk melanjutkan</p>

    @if(session()->has('loginError'))
      <div class="alert alert-danger py-2" role="alert">
        {{ session('loginError') }}
      </div>
    @endif

    <form action="/sign-in" method="POST">
      @csrf
      <div class="mb-3">
        <input type="email" name="email" id="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror" required autofocus value="{{ old('email') }}">
        @error('email')
        <div class="invalid-feedback text-start">
          {{ $message }}
        </div>
        @enderror
      </div>

      <div class="mb-3">
        <input type="password" name="password" id="password" placeholder="Kata sandi" class="form-control" required>
      </div>

      <a href="#" class="forgot">Lupa kata sandi?</a>
      <button type="submit" class="btn-login">Masuk</button>
    </form>

    <div class="signup">
      Belum punya akun?
      <a href="/sign-up">Daftar</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

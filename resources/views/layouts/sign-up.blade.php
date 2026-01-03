<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar | Galeriku</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
    :root{
      --red:#e60023; --gray:#f7f7f7; --line:#e5e7eb; --text:#111; --muted:#6b7280;
      --radius:16px;
    }
    html,body{height:100%}
    body{
      font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background:#fff; display:flex; align-items:center; justify-content:center;
      padding:24px;
    }
    .card-auth{
      width:100%; max-width:420px; border-radius:24px; background:#fff;
      box-shadow:0 10px 40px rgba(0,0,0,.08);
      padding:24px 24px 28px; position:relative;
    }
    .close-x{
      position:absolute; right:14px; top:14px; width:36px; height:36px;
      border-radius:999px; border:1px solid var(--line); background:#fff; color:#374151;
      display:grid; place-items:center; text-decoration:none;
    }
    .logo{ width:40px; height:40px; display:block; margin:4px auto 8px; }
    h1{ font-weight:800; font-size:24px; text-align:center; margin:0 0 8px; color:var(--text); }
    .sub{ text-align:center; color:#374151; font-size:14px; margin-bottom:12px; }

    .field-label{ font-size:12px; font-weight:700; color:#374151; margin:8px 6px 6px; }
    .pill{ background:var(--gray); border:1px solid var(--line); border-radius:999px; padding:10px 14px; }
    .pill input{ width:100%; border:0; outline:0; background:transparent; font-size:15px; color:var(--text); }
    .pill:focus-within{ border-color:#000; background:#f3f4f6; }

    .hint{ font-size:12px; color:var(--muted); margin:6px 6px 0; }
    .row-2{ display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    @media (max-width:480px){ .row-2{ grid-template-columns:1fr; } }

    .btn-red{
      width:100%; border:0; border-radius:999px; padding:12px 16px;
      background:var(--red); color:#fff; font-weight:700; margin-top:8px;
    }
    .btn-red:hover{ background:#cc001f; }

    .foot{ font-size:13px; color:#6b7280; text-align:center; margin-top:12px; }
    .foot a{ color:#2563eb; text-decoration:none; font-weight:600; }
    .foot a:hover{ text-decoration:underline; }

    .invalid-feedback{ display:block; font-size:12px; margin:6px 6px 0; }
  </style>
</head>
<body>
  <div class="card-auth">
    <a href="/" class="close-x" aria-label="Close">✕</a>

    {{-- Logo opsional --}}
    <img class="logo" src="{{ asset('assets/img/galeriku-icon.png') }}" alt="Logo">

    <h1>Selamat datang di Galeriku</h1>
    <p class="sub">Dapatkan ide-ide baru untuk dicoba</p>

    @if(session('registerError'))
      <div class="alert alert-danger py-2">{{ session('registerError') }}</div>
    @endif

    <form action="/sign-up" method="POST" novalidate>
      @csrf

      {{-- Email --}}
      <label class="field-label" for="email">Email</label>
      <div class="pill">
        <input type="email" id="email" name="email"
               class="@error('email') is-invalid @enderror"
               placeholder="Email" required
               value="{{ old('email') }}">
      </div>
      @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror

      {{-- Password --}}
      <label class="field-label" for="password">Kata sandi</label>
      <div class="pill">
        <input type="password" id="password" name="password"
               class="@error('password') is-invalid @enderror"
               placeholder="Buat kata sandi" required minlength="8">
      </div>
      <p class="hint">Gunakan 8 atau lebih huruf, angka, dan simbol</p>
      @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror

      {{-- Bidang tambahan (opsional, pakai grid 2 kolom) --}}
      <div class="row-2">
        <div>
          <label class="field-label" for="fullname">Nama lengkap</label>
          <div class="pill">
            <input type="text" id="fullname" name="fullname"
                   value="{{ old('fullname') }}" placeholder="Nama lengkap">
          </div>
          @error('fullname') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="field-label" for="username">Username</label>
          <div class="pill">
            <input type="text" id="username" name="username"
                   value="{{ old('username') }}" placeholder="Username">
          </div>
          @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>



      <button class="btn-red" type="submit">Lanjutkan</button>

      <p class="foot">Sudah punya akun? <a href="/sign-in">Masuk</a></p>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

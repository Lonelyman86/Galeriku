@extends('main')

@section('content')
<div class="container py-4" style="max-width: 800px;">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2 class="mb-3">Profil</h2>

    <div class="card shadow-sm">
      <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PATCH')

          <div class="d-flex align-items-center gap-3 mb-4">
          @if(Auth::user()->avatar != null)
    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Profile"
         class="rounded-circle border" width="36" height="36">
    @else
         <img src="{{ asset('assets/img/default-profile.png')  }}" alt="Profile"
         class="rounded-circle border" width="36" height="36">
    @endif
            <div>
              <label class="form-label mb-1">Foto profil</label>
              <input type="file" name="avatar" accept="image/*" class="form-control form-control-sm @error('avatar') is-invalid @enderror" style="max-width: 320px;">
              @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <div class="form-text">JPG/PNG/WEBP, maks 2MB.</div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama lengkap</label>
              <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}" class="form-control @error('fullname') is-invalid @enderror" required>
              @error('fullname') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Username</label>
              <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control @error('username') is-invalid @enderror" required>
              @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Alamat</label>
              <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control @error('address') is-invalid @enderror">
              @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <hr class="my-4">

          <h5 class="mb-2">Ubah kata sandi (opsional)</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Password baru</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
              @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Konfirmasi password</label>
              <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password">
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button class="btn btn-dark">Simpan perubahan</button>
          </div>
        </form>
      </div>
    </div>
</div>
@endsection
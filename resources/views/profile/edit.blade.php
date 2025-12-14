@extends('main')

@section('content')
<style>
    /* Force font consistency */
    body, p, h1, h2, h3, h4, h5, h6, input, textarea, select, button, .section-title, .form-label {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    }

    /* === DARK MODE OVERRIDES === */
    body.dark-mode .section-title, 
    body.dark-mode .form-label,
    body.dark-mode .form-text {
        color: #e0e0e0 !important;
    }
    
    /* Input & Textarea */
    body.dark-mode .form-control {
        background-color: #2b2b2b !important;
        border: 1px solid #444 !important;
        color: #e0e0e0 !important;
    }
    body.dark-mode .form-control:focus {
        background-color: #333 !important;
        border-color: #666 !important;
        color: #fff !important;
    }
    body.dark-mode .form-control::placeholder {
        color: #888 !important;
    }
    
    /* Buttons */
    body.dark-mode .btn-dark {
        background-color: #e0e0e0;
        color: #121212;
        border: none;
    }
    body.dark-mode .btn-dark:hover {
        background-color: #fff;
    }

    .profile-avatar-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto;
    }
    .avatar-edit-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #000;
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 2px solid #fff;
    }
    body.dark-mode .avatar-edit-icon {
        border-color: #121212;
    }
</style>

<div class="container py-5" style="max-width: 720px;">
    
    <div class="text-center mb-5">
        <h2 class="fw-bold section-title">Edit Profil</h2>
        <p class="text-muted small">Perbarui informasi profil Anda</p>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show rounded-4 text-center" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        {{-- PHOTO UPLOAD SECTION --}}
        <div class="text-center mb-5">
            <div class="profile-avatar-wrapper mb-3">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                         alt="Profile" 
                         class="rounded-circle shadow-sm" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <i class="bi bi-person-circle default-avatar-icon" style="font-size: 100px; line-height: 1;"></i>
                @endif
                
                {{-- Overlay Icon for Edit --}}
                <label for="avatarInput" class="avatar-edit-icon shadow-sm" style="cursor: pointer;">
                    <i class="bi bi-pencil-fill"></i>
                </label>
            </div>
            
            <input type="file" id="avatarInput" name="avatar" accept="image/*" class="d-none" onchange="previewAvatar(this)">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="document.getElementById('avatarInput').click()">Ubah Foto</button>
            <div class="form-text mt-2 small">JPG, PNG atau WEBP. Maks 2MB.</div>
            @error('avatar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- FORM FIELDS --}}
        <div class="row g-4">
            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">Nama Lengkap</label>
                <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}" class="form-control rounded-pill px-3 py-2" placeholder="Nama Lengkap" required>
                @error('fullname') <div class="text-danger small ms-2">{{ $message }}</div> @enderror
            </div>

            {{-- Username --}}
            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control rounded-pill px-3 py-2" placeholder="Username" required>
                @error('username') <div class="text-danger small ms-2">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div class="col-12">
                <label class="form-label fw-bold small text-muted">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control rounded-pill px-3 py-2" placeholder="Alamat Email" required>
                @error('email') <div class="text-danger small ms-2">{{ $message }}</div> @enderror
            </div>
            
            {{-- Address --}}
            <div class="col-12">
                <label class="form-label fw-bold small text-muted">Alamat</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control rounded-pill px-3 py-2" placeholder="Alamat Lengkap (Opsional)">
                @error('address') <div class="text-danger small ms-2">{{ $message }}</div> @enderror
            </div>

            {{-- Bio --}}
            <div class="col-12">
                <label class="form-label fw-bold small text-muted">Bio</label>
                <textarea name="bio" rows="3" class="form-control rounded-4 px-3 py-2" placeholder="Ceritakan sedikit tentang dirimu...">{{ old('bio', $user->bio) }}</textarea>
                @error('bio') <div class="text-danger small ms-2">{{ $message }}</div> @enderror
            </div>
        </div>

        <hr class="my-5 opacity-25">

        {{-- PASSWORD SECTION --}}
        <h5 class="fw-bold mb-4 section-title">Ubah Kata Sandi <span class="fw-normal text-muted fs-6 ms-2">(Opsional)</span></h5>
        <div class="row g-4">
             <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">Kata Sandi Baru</label>
                <input type="password" name="password" class="form-control rounded-pill px-3 py-2" placeholder="Min. 8 Karakter">
                @error('password') <div class="text-danger small ms-2">{{ $message }}</div> @enderror
            </div>
             <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">Ulangi Kata Sandi</label>
                <input type="password" name="password_confirmation" class="form-control rounded-pill px-3 py-2" placeholder="Konfirmasi Password">
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="d-flex justify-content-end gap-3 mt-5">
            <a href="{{ route('profile.public', Auth::id()) }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">Lihat Profil</a>
            <button class="btn btn-dark rounded-pill px-5 fw-bold" style="min-width: 140px;">Simpan</button>
        </div>

    </form>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // Find wrapper
                const wrapper = document.querySelector('.profile-avatar-wrapper');
                // Remove existing content inside wrapper (img or icon) but keep edit icon
                const editIcon = wrapper.querySelector('.avatar-edit-icon');
                
                // Clear content
                wrapper.innerHTML = ''; 
                
                // Create new img
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'rounded-circle shadow-sm';
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                
                wrapper.appendChild(img);
                wrapper.appendChild(editIcon); // re-append icon
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
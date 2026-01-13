<nav class="navbar fixed-bottom navbar-light bg-white border-top shadow-sm d-lg-none" style="z-index: 1030; padding-bottom: env(safe-area-inset-bottom);">
  <div class="container-fluid d-flex justify-content-around align-items-center">

    {{-- Home --}}
    <a href="/" class="nav-link text-center {{ Request::is('/') ? 'text-dark' : 'text-secondary' }}">
      <i class="bi bi-house{{ Request::is('/') ? '-fill' : '' }} fs-3"></i>
    </a>

    {{-- Discovery (Search/Explore) --}}
    <a href="/discovery" class="nav-link text-center {{ Request::is('discovery*') ? 'text-dark' : 'text-secondary' }}">
      <i class="bi bi-compass{{ Request::is('discovery*') ? '-fill' : '' }} fs-3"></i>
    </a>

    @auth
        {{-- Following (New) --}}
        <a href="/following" class="nav-link text-center {{ Request::is('following*') ? 'text-dark' : 'text-secondary' }}">
            <i class="bi bi-people{{ Request::is('following*') ? '-fill' : '' }} fs-3"></i>
        </a>
    @endauth

    @auth
        {{-- Create / Upload --}}
        <a href="/studio" class="nav-link text-center {{ Request::is('studio*') ? 'text-dark' : 'text-secondary' }}">
          <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
           <i class="bi bi-plus-lg fs-4 text-dark"></i>
          </div>
        </a>

        {{-- Notifications --}}
        <a href="#" class="nav-link text-center position-relative text-secondary" data-bs-toggle="offcanvas" data-bs-target="#notifDrawer">
          <i class="bi bi-bell fs-3"></i>
          @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 10px; height: 10px;">
              <span class="visually-hidden">New alerts</span>
            </span>
          @endif
        </a>

        {{-- Profile (Opens Drawer) --}}
        <a href="#" class="nav-link text-center" data-bs-toggle="offcanvas" data-bs-target="#menuDrawer">
           @if(Auth::user()->avatar)
              <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                   class="rounded-circle border"
                   style="width: 28px; height: 28px; object-fit: cover;">
           @else
              <i class="bi bi-person-circle fs-3 text-secondary"></i>
           @endif
        </a>
    @else
        {{-- Login Button for Guest --}}
        <a href="/sign-in" class="nav-link text-center text-danger fw-bold">
            Login
        </a>
    @endauth

  </div>
</nav>

{{-- MENU DRAWER (User Dropdown Replacement) --}}
@auth
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="menuDrawer" style="height: auto; border-radius: 20px 20px 0 0;">
  <div class="offcanvas-header pb-0">
    <h5 class="offcanvas-title fw-bold">Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body small">
    <div class="d-flex align-items-center mb-4">
        @if(Auth::user()->avatar)
            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
        @else
            <i class="bi bi-person-circle fs-1 me-3 text-secondary"></i>
        @endif
        <div>
            <h6 class="fw-bold mb-0">{{ Auth::user()->username }}</h6>
            <span class="text-muted">{{ Auth::user()->email }}</span>
        </div>
    </div>

    <div class="list-group list-group-flush">
        <a href="{{ route('profile.public', ['username' => Auth::user()->username]) }}" class="list-group-item list-group-item-action border-0 px-0">
            <i class="bi bi-person me-2"></i> Lihat Galeri Saya
        </a>
        <a href="{{ route('profile') }}" class="list-group-item list-group-item-action border-0 px-0">
            <i class="bi bi-pencil-square me-2"></i> Edit Profil
        </a>

        {{-- ADDED LINKS for Mobile Access --}}
        <a href="{{ route('photo.liked') }}" class="list-group-item list-group-item-action border-0 px-0">
            <i class="bi bi-heart-fill me-2 text-danger"></i> Koleksi Favorit
        </a>
        <a href="{{ route('feed.following') }}" class="list-group-item list-group-item-action border-0 px-0">
            <i class="bi bi-people-fill me-2 text-success"></i> Mengikuti
        </a>

        @if(Auth::user()->role_id == 1)
            <div class="my-2 border-top"></div>
            <span class="text-muted small text-uppercase fw-bold my-2 d-block">Admin Area</span>
            <a href="/admin/dashboard" class="list-group-item list-group-item-action border-0 px-0 text-danger">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="/admin/foto" class="list-group-item list-group-item-action border-0 px-0 text-danger">
                <i class="bi bi-images me-2"></i> Kelola Foto
            </a>
        @endif

        <div class="my-2 border-top"></div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="list-group-item list-group-item-action border-0 px-0 text-danger fw-bold bg-transparent">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
        </form>
    </div>
  </div>
</div>
@endauth

<nav class="navbar navbar-expand-lg pinterest-nav px-3">
  <div class="container-fluid align-items-center">

    <!-- Search -->
    <div class="flex-grow-1 d-flex align-items-center">
      <form class="w-100" action="{{ route('search') }}" method="GET">
        <input class="form-control pinterest-search" type="search" name="q"
               placeholder="Cari foto atau album..." aria-label="Search"
               value="{{ request('q') }}">
      </form>
    </div>

    <!-- Profile kanan -->
    <ul class="navbar-nav ms-3">
      @auth
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown"
             role="button" data-bs-toggle="dropdown" aria-expanded="false">

@if(Auth::user()->avatar != null)     <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
         alt="Profile"
         class="rounded-circle border"
         width="36"
         height="36"> @else <img src="{{ asset('assets/img/default-profile.png') }}" alt="Profile" class="rounded-circle border" width="36" height="36"> @endif

            {{-- 🧩 Tambahin badge kalau admin --}}
            @if(Auth::user()->role == 1)
              <span class="badge bg-danger ms-2">Admin</span>
            @endif
          </a>

          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>

            {{-- Tambahan link ke dashboard admin --}}
            @if(Auth::user()->role == 1)
              <li><a class="dropdown-item text-danger" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
              </a></li>
              <li><a class="dropdown-item" href="{{ route('admin.foto.index') }}">
                <i class="bi bi-images me-2"></i>Kelola Foto
              </a></li>
              <li><hr class="dropdown-divider"></li>
            @endif

            <li>
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item text-danger">Logout</button>
              </form>
            </li>
          </ul>
        </li>
      @else
        <li class="nav-item me-2"><a class="btn btn-outline-dark" href="/sign-in">Sign In</a></li>
        <li class="nav-item"><a class="btn btn-danger" href="/sign-up">Sign Up</a></li>
      @endauth
    </ul>

  </div>
</nav>

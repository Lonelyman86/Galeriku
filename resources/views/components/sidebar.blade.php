<aside class="sidenav bg-white border-end shadow-sm vh-100 position-fixed top-0 start-0 d-flex flex-column">
  <div class="text-center py-3">
    <a href="/" class="text-danger fs-3 text-decoration-none">
      <img src="{{ asset('assets/img/galeriku-icon.png') }}" alt="Logo" class="logo" style="width: 45px; margin-bottom: 8px;">
    </a>
  </div>

  <ul class="nav flex-column text-center flex-grow-1">
    
    {{-- Home --}}
    <li class="nav-item my-1">
      <a class="nav-link text-dark py-3 rounded-3" href="/" title="Home">
        <i class="bi bi-house fs-4"></i>
      </a>
    </li>

    @auth
      {{-- Admin Menu --}}
      @if(Auth::user()->role_id == 1)
        <li class="nav-item my-1">
          <a class="nav-link text-danger py-3 rounded-3" href="/admin/dashboard" title="Admin Dashboard">
            <i class="bi bi-speedometer2 fs-4"></i>
          </a>
        </li>

        <li class="nav-item my-1">
          <a class="nav-link text-danger py-3 rounded-3" href="/admin/foto" title="Kelola Foto">
            <i class="bi bi-images fs-4"></i>
          </a>
        </li>
      @endif

      {{-- Studio --}}
      <li class="nav-item my-1">
        <a class="nav-link text-dark py-3 rounded-3" href="/studio" title="Studio">
          <i class="bi bi-brush fs-4"></i>
        </a>
      </li>

      {{-- Liked --}}
      <li class="nav-item my-1">
        <a class="nav-link text-dark py-3 rounded-3" href="/liked" title="Liked">
          <i class="bi bi-heart fs-4"></i>
        </a>
      </li>

      {{-- =========================================================
           NOTIFIKASI (TOMBOL BEL)
      ========================================================== --}}
      <li class="nav-item my-1">
  <a class="nav-link text-dark py-3 rounded-3 position-relative"
     href="#"
     data-bs-toggle="offcanvas"
     data-bs-target="#notifDrawer"
     title="Notifikasi">
    <i class="bi bi-bell fs-4"></i>

    @if(!empty($unreadNotificationsCount) && $unreadNotificationsCount > 0)
      <span class="position-absolute bottom-0 end-0 translate-middle badge rounded-pill bg-danger"
            style="font-size: 0.65rem; min-width: 18px;">
        {{ $unreadNotificationsCount }}
      </span>
    @endif
  </a>
</li>


    @endauth

  </ul>

  {{-- Dark Mode Toggle --}}
  <div class="text-center pb-4">
    <button id="darkModeToggle" class="btn btn-link link-dark p-0 border-0" title="Dark Mode">
      <i class="bi bi-moon fs-4" id="darkModeIcon"></i>
    </button>
  </div>
</aside>

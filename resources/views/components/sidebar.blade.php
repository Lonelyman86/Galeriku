<aside class="sidenav bg-white border-end shadow-sm vh-100 position-fixed top-0 start-0 d-flex flex-column">
  <div class="text-center py-3">
    <a href="/" class="text-danger fs-3 text-decoration-none">
      <img src="{{ asset('assets/img/galeriku-icon.png') }}" alt="Logo" class="logo" style="width: 45px; margin-bottom: 8px;">
    </a>
  </div>

  <ul class="nav flex-column text-center flex-grow-1">
    <li class="nav-item my-1">
      <a class="nav-link text-dark py-3 rounded-3" href="/" title="Home"><i class="bi bi-house fs-4"></i></a>
    </li>

    @auth
    @if(Auth::user()->role_id == 1)
        <li class="nav-item my-1">
          <a class="nav-link text-danger py-3 rounded-3" href="{{ route('admin.dashboard') }}" title="Admin Dashboard">
            <i class="bi bi-speedometer2 fs-4"></i>
          </a>
        </li>
        <li class="nav-item my-1">
          <a class="nav-link text-danger py-3 rounded-3" href="{{ route('admin.foto.index') }}" title="Kelola Foto">
            <i class="bi bi-images fs-4"></i>
          </a>
        </li>
      @endif
      <li class="nav-item my-1">
        <a class="nav-link text-dark py-3 rounded-3" href="/studio" title="Studio"><i class="bi bi-brush fs-4"></i></a>
      </li>
      <li class="nav-item my-1">
        <a class="nav-link text-dark py-3 rounded-3" href="/liked" title="Liked"><i class="bi bi-heart fs-4"></i></a>
      </li>

      {{-- ✅ Tambahan khusus admin --}}
      
    @endauth
  </ul>
</aside>

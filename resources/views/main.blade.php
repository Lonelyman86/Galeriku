<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Galeriku</title>

  <link rel="icon" type="image/png" href="{{ asset('assets/img/galeriku-icon.png') }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/style-pinterest.css') }}">
  
  {{-- Tambahan CSS Global untuk Modal Loading --}}
  <style>
    .modal-img-container {
        background: #f0f0f0;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-img-container img {
        max-height: 80vh;
        width: auto;
        margin: 0 auto;
    }
  </style>
</head>

<body class="bg-light">

  {{-- Sidebar --}}
  @include('components.sidebar')

  {{-- Navbar --}}
  @include('components.navbar')

  <main class="main-content">
    @yield('content')
  </main>

  {{-- =========================================
     OFFCANVAS NOTIFIKASI
   ========================================= --}}
  @auth
  <div class="offcanvas offcanvas-start offcanvas-notif" tabindex="-1" id="notifDrawer" data-bs-backdrop="false">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Pemberitahuan</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      @if(isset($recentNotifications) && $recentNotifications->isNotEmpty())
        <ul class="list-group list-group-flush">
          @foreach($recentNotifications as $notif)
            <li class="list-group-item d-flex justify-content-between align-items-start {{ $notif->read_at ? '' : 'bg-light' }}">
              <div>
                <div class="fw-semibold">{{ $notif->message }}</div>
                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
              </div>
            </li>
          @endforeach
        </ul>
        <form action="{{ route('notifications.read-all') }}" method="POST" class="mt-3">
          @csrf
          <button class="btn btn-sm btn-outline-secondary w-100">Tandai semua dibaca</button>
        </form>
      @else
        <p class="text-muted mb-0">Belum ada notifikasi.</p>
      @endif
    </div>
  </div>
  @endauth

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  {{-- Stack untuk script khusus per halaman --}}
  @stack('scripts')
</body>
</html>
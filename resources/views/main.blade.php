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
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  
  {{-- Tambahan CSS Global untuk Modal Loading --}}
  <style>
    /* GLOBAL FONT STACK - Premium Feel */
    body, p, h1, h2, h3, h4, h5, h6, input, textarea, select, button, .form-label, .section-title {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    }

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

    /* === DARK MODE VARIABLES === */
    body.dark-mode {
        background-color: #121212 !important;
        color: #e0e0e0;
    }
    body.dark-mode .bg-light, 
    body.dark-mode .bg-white,
    body.dark-mode .pinterest-nav,
    body.dark-mode .sidenav,
    body.dark-mode .modal-content,
    body.dark-mode .offcanvas {
        background-color: #1e1e1e !important; /* Back to Darker Gray */
        color: #e0e0e0 !important;
        border-color: #333 !important;
    }

    /* Global Dark Mode Text Overrides */
    body.dark-mode .section-title {
        color: #e0e0e0 !important;
    }

    /* Cards specifically lighter to stand out */
    body.dark-mode .card,
    body.dark-mode .folder-card,
    body.dark-mode .pin, 
    body.dark-mode .pin-wrapper,
    body.dark-mode .pin-meta,
    body.dark-mode .modal-image-wrapper { /* Added wrapper */
        background-color: #2b2b2b !important;
        color: #e0e0e0 !important;
        border-color: #444 !important;
    }

    /* Table Styles for Dark Mode */
    body.dark-mode .table {
        color: #e0e0e0 !important;
        border-color: #444 !important;
    }
    body.dark-mode .table > :not(caption) > * > * {
        background-color: #2b2b2b !important;
        color: #e0e0e0 !important;
        border-color: #444 !important;
    }
    body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) > * {
        background-color: #333 !important; /* Slightly lighter for striped rows */
        color: #e0e0e0 !important;
    }
    body.dark-mode .table-hover > tbody > tr:hover > * {
        background-color: #3a3a3a !important;
    }
    
    body.dark-mode .text-dark { color: #e0e0e0 !important; }
    body.dark-mode .text-muted { color: #aaa !important; }
    body.dark-mode .text-secondary { color: #ccc !important; }
    
    body.dark-mode .btn-outline-secondary {
        color: #ccc;
        border-color: #666;
    }
    body.dark-mode .btn-outline-secondary:hover {
        background-color: #333;
        color: #fff;
    }

    /* Fix btn-outline-dark in Dark Mode (Inverse it) */
    body.dark-mode .btn-outline-dark {
        color: #e0e0e0;
        border-color: #666;
    }
    body.dark-mode .btn-outline-dark:hover {
        background-color: #e0e0e0;
        color: #121212;
    }
    
    body.dark-mode .form-control, 
    body.dark-mode .pinterest-search,
    body.dark-mode .form-select {
        background-color: #2c2c2c !important;
        border-color: #444 !important;
        color: #e0e0e0 !important;
    }
    body.dark-mode .form-control::placeholder,
    body.dark-mode .pinterest-search::placeholder {
        color: #aaa !important;
    }
    body.dark-mode .form-control:focus,
    body.dark-mode .pinterest-search:focus {
        background-color: #333 !important;
        color: #fff !important;
        border-color: #666 !important;
    }

    body.dark-mode .list-group-item {
        background-color: #1e1e1e;
        color: #e0e0e0;
        border-color: #333;
    }

    /* Fix Input Group in Dark Mode */
    body.dark-mode .input-group-text {
        background-color: #2c2c2c !important;
        border-color: #444 !important;
        color: #e0e0e0 !important;
    }
    body.dark-mode .input-group .form-select {
        background-color: #2c2c2c !important;
        color: #e0e0e0 !important;
        border-color: #444 !important;
    }

    /* Search Dropdown Dark Mode */
    body.dark-mode .search-history-container {
        background-color: #2c2c2c !important;
        border-color: #444 !important;
    }
    body.dark-mode .history-text {
        color: #e0e0e0 !important;
    }
    body.dark-mode .history-item:hover {
        background-color: #333 !important;
    }

    /* === DARK MODE MODAL & COMMENTS === */
    body.dark-mode .modal-right-col,
    body.dark-mode .modal-comment-bg,
    body.dark-mode .comment-bubble {
        background-color: #2b2b2b !important;
        border-color: #444 !important;
        color: #e0e0e0 !important;
    }
    body.dark-mode .comment-user,
    body.dark-mode .comment-text {
        color: #e0e0e0 !important;
    }
    body.dark-mode .modal-right-col .border-bottom,
    body.dark-mode .modal-right-col .border-top {
        border-color: #444 !important;
    }
    
    /* Default Avatar Icon Colors */
    .default-avatar-icon {
        color: #888 !important; /* Force Gray for Light Mode */
        transition: color 0.3s;
    }
    body.dark-mode .default-avatar-icon {
        color: #fff !important; /* Force White for Dark Mode */
    }

    /* Dropdown Menus Dark Mode */
    body.dark-mode .dropdown-menu {
        background-color: #2b2b2b !important;
        border-color: #444 !important;
    }
    body.dark-mode .dropdown-item {
        color: #e0e0e0 !important;
    }
    body.dark-mode .dropdown-item:hover {
        background-color: #333 !important;
        color: #fff !important;
    }
    body.dark-mode .dropdown-header {
        color: #aaa !important;
    }
    body.dark-mode .dropdown-divider {
        border-color: #444 !important;
    }
    
    /* Logo filter removed per user request */
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
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
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
  
  {{-- DARK MODE SCRIPT --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('darkModeToggle');
        const icon = document.getElementById('darkModeIcon');
        const body = document.body;
        
        // 1. Check LocalStorage
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme === 'dark') {
            enableDarkMode();
        }

        // 2. Event Listener
        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                if (body.classList.contains('dark-mode')) {
                    disableDarkMode();
                } else {
                    enableDarkMode();
                }
            });
        }

        function enableDarkMode() {
            body.classList.add('dark-mode');
            body.classList.remove('bg-light'); // Matikan bg-light default
            if(icon) {
                icon.classList.remove('bi-moon');
                icon.classList.add('bi-sun-fill');
                icon.classList.add('text-warning'); // Biar ikon matahari kuning
            }
            localStorage.setItem('theme', 'dark');
        }

        function disableDarkMode() {
            body.classList.remove('dark-mode');
            body.classList.add('bg-light');
            if(icon) {
                icon.classList.remove('bi-sun-fill');
                icon.classList.remove('text-warning');
                icon.classList.add('bi-moon');
            }
            localStorage.setItem('theme', 'light');
        }
    });
  </script>

  {{-- Stack untuk script khusus per halaman --}}
  @stack('scripts')
</body>
</html>
<nav class="navbar navbar-expand-lg pinterest-nav px-3">
  <div class="container-fluid align-items-center">

    <div class="flex-grow-1 d-flex align-items-center position-relative">
      
      {{-- Form Search --}}
      <form id="searchForm" class="w-100" action="{{ route('search') }}" method="GET">
        <input 
            id="searchInput"
            class="form-control pinterest-search" 
            type="search" 
            name="q"
            placeholder="Cari foto atau album..." 
            aria-label="Search"
            autocomplete="off" 
            value="{{ request('q') }}"
        >
      </form>

      {{-- Dropdown History (Muncul via JS) --}}
      <div id="searchHistoryDropdown" class="search-history-container">
          {{-- Item history akan di-inject di sini oleh JS --}}
      </div>

    </div>

    <ul class="navbar-nav ms-3">
      @auth
        {{-- Container Flex untuk Avatar & Panah --}}
        <li class="nav-item dropdown d-flex align-items-center">
          
          {{-- 1. KLIK AVATAR: Langsung ke Halaman Public Profile --}}
          <a href="{{ route('profile.public', Auth::user()->id) }}" class="d-block p-1 text-decoration-none" title="Lihat Galeri Saya">
            @if(Auth::user()->avatar)
              <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                   alt="Profile"
                   class="rounded-circle border"
                   style="object-fit: cover;"
                   width="36"
                   height="36">
            @else
              <i class="bi bi-person-circle default-avatar-icon" style="font-size: 36px;"></i>
            @endif
          </a>

          {{-- 2. KLIK PANAH: Pemicu Dropdown Menu --}}
          <a class="nav-link dropdown-toggle ms-1 px-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            @if(Auth::user()->role_id == 1)
               <span class="badge bg-danger" style="font-size: 10px;">Admin</span>
            @endif
          </a>

          {{-- 3. ISI MENU DROPDOWN --}}
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarDropdown" style="border-radius: 12px; margin-top: 10px;">
            <li>
              <a class="dropdown-item py-2" href="{{ route('profile') }}">
                <i class="bi bi-pencil-square me-2 text-secondary"></i> Edit Profil
              </a>
            </li>
            <li>
                <a class="dropdown-item py-2" href="{{ route('profile.public', Auth::user()->id) }}">
                  <i class="bi bi-person me-2 text-secondary"></i> Lihat Galeri Saya
                </a>
            </li>

            {{-- Menu Khusus Admin --}}
            @if(Auth::user()->role_id == 1)
              <li><hr class="dropdown-divider"></li>
              <li><h6 class="dropdown-header text-uppercase small text-danger fw-bold">Admin Area</h6></li>
              <li>
                <a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}">
                  <i class="bi bi-speedometer2 me-2 text-danger"></i> Dashboard
                </a>
              </li>
              <li>
                <a class="dropdown-item py-2" href="{{ route('admin.foto.index') }}">
                  <i class="bi bi-images me-2 text-danger"></i> Kelola Foto
                </a>
              </li>
            @endif

            <li><hr class="dropdown-divider"></li>
            
            <li>
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item py-2 text-danger fw-bold">
                  <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
              </form>
            </li>
          </ul>

        </li>
      @else
        <li class="nav-item me-2"><a class="btn btn-outline-dark rounded-pill px-4" href="/sign-in">Sign In</a></li>
        <li class="nav-item"><a class="btn btn-danger rounded-pill px-4" href="/sign-up">Sign Up</a></li>
      @endauth
    </ul>

  </div>
</nav>

{{-- ===============================================
     STYLE & SCRIPT KHUSUS SEARCH HISTORY
     =============================================== --}}
<style>
    /* Style Dropdown History */
    .search-history-container {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 0 0 16px 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 1000;
        display: none;
        overflow: hidden;
        margin-top: 4px;
    }
    .history-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .history-item:hover { background-color: #f3f4f6; }
    .history-text {
        flex-grow: 1;
        font-size: 14px;
        color: #333;
        display: flex; align-items: center; gap: 10px;
    }
    .delete-history-btn {
        border: none; background: transparent; color: #999;
        font-size: 16px; padding: 4px 8px; border-radius: 50%;
        cursor: pointer;
    }
    .delete-history-btn:hover { color: #ef4444; background: #fee2e2; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const historyDropdown = document.getElementById('searchHistoryDropdown');
    const STORAGE_KEY = 'galeriku_search_history';

    // 1. Ambil History
    function getHistory() {
        const history = localStorage.getItem(STORAGE_KEY);
        return history ? JSON.parse(history) : [];
    }

    // 2. Simpan History
    function saveHistory(keyword) {
        let history = getHistory();
        history = history.filter(item => item !== keyword); // Hapus duplikat
        history.unshift(keyword); // Tambah ke depan
        if (history.length > 8) history.pop(); // Batasi 8 item
        localStorage.setItem(STORAGE_KEY, JSON.stringify(history));
    }

    // 3. Render Dropdown
    function renderHistory() {
        const history = getHistory();
        if (history.length === 0) {
            historyDropdown.style.display = 'none';
            return;
        }
        let html = '';
        history.forEach((text) => {
            html += `
                <div class="history-item">
                    <div class="history-text" onclick="submitHistory('${text}')">
                        <i class="bi bi-clock-history text-secondary"></i> ${text}
                    </div>
                    <button class="delete-history-btn" onclick="deleteHistory(event, '${text}')">&times;</button>
                </div>`;
        });
        historyDropdown.innerHTML = html;
        historyDropdown.style.display = 'block';
    }

    // 4. Event Listeners
    if(searchInput) {
        searchInput.addEventListener('focus', renderHistory);
        
        // Klik di luar -> Tutup dropdown
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !historyDropdown.contains(e.target)) {
                historyDropdown.style.display = 'none';
            }
        });

        // Submit form -> Simpan
        searchForm.addEventListener('submit', function() {
            const val = searchInput.value.trim();
            if (val) saveHistory(val);
        });
    }

    // 5. Global Functions (agar bisa dipanggil onclick HTML)
    window.submitHistory = function(text) {
        searchInput.value = text;
        searchForm.submit();
    };

    window.deleteHistory = function(e, text) {
        e.stopPropagation();
        let history = getHistory();
        history = history.filter(item => item !== text);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(history));
        renderHistory();
        searchInput.focus();
    };
});
</script>
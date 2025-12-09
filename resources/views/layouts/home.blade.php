@extends('main')

@section('content')

{{-- LIBRARIES (JANGAN DIHAPUS) --}}
<script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>

<style>
  /* === STYLE ELEMENT DARI KODEMU (DIPERTAHANKAN) === */
  .pin-wrapper {
    position: relative; border-radius: 16px; overflow: hidden; background: #f3f4f6;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform .2s ease, box-shadow .2s ease;
  }
  .pin-wrapper:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); z-index: 5; }
  .pin-wrapper img { width: 100%; height: auto; display: block; cursor: pointer; }

  /* Tombol Menu 3 Titik */
  .pin-menu-btn {
    position: absolute; top: 10px; right: 10px; width: 32px; height: 32px; border-radius: 50%;
    border: 0; background: rgba(0, 0, 0, .6); color: #fff; display: flex; align-items: center; justify-content: center;
    font-size: 16px; opacity: 0; transition: all .2s ease; cursor: pointer; z-index: 10;
  }
  .pin-wrapper:hover .pin-menu-btn, .pin-menu-btn.active { opacity: 1; }
  .pin-menu-btn:hover { background: rgba(0, 0, 0, .8); transform: scale(1.1); }

  /* Dropdown Menu */
  .pin-menu {
    position: absolute; top: 46px; right: 10px; min-width: 160px; background: #fff;
    border-radius: 12px; box-shadow: 0 10px 24px rgba(0, 0, 0, .2); padding: 6px 0; font-size: 13px;
    z-index: 20; display: none;
  }
  .pin-menu.show { display: block; animation: fadeInMenu 0.1s ease-out; }
  @keyframes fadeInMenu { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

  .pin-menu-item {
    display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 16px;
    border: 0; background: transparent; text-align: left; font-size: 13px; font-weight: 500;
    color: #333; cursor: pointer; text-decoration: none; transition: background 0.1s;
  }
  .pin-menu-item:hover { background: #f3f4f6; }

  /* === MODAL STYLE === */
  .modal-image-wrapper {
    position: relative; 
    width: 100%; 
    height: 100%; 
    background: #f8f9fa; /* GANTI: Dari hitam (#000) ke abu-abu terang */
    display: flex; 
    justify-content: center; 
    align-items: center; 
    min-height: 400px;
    padding: 24px; /* Tambah padding biar foto gak mepet pinggir */
  }
  .modal-image-wrapper img { 
      max-width: 100%; 
      max-height: 85vh; 
      object-fit: contain; 
      
      /* TAMBAHAN: Biar curved dan ada bayangan */
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.12);
  }
  .pin-menu-btn-modal { top: 24px; right: 24px; opacity: 1; }
  .pin-menu-modal { top: 60px; right: 24px; }
  
  /* Scrollbar Custom di Modal */
  .custom-scrollbar::-webkit-scrollbar { width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #bbb; }
</style>

<div class="container-fluid py-4">
  <h1 class="fs-4 mb-4 fw-bold px-2">Jelajahi</h1>

  {{-- CONTAINER UTAMA --}}
  <div class="row" id="masonry-grid">
    @foreach ($foto as $item)
      <div class="col-6 col-md-4 col-lg-3 mb-4 masonry-item">
        
        {{-- WRAPPER FOTO --}}
        <div class="pin-wrapper">
          {{-- Gambar Utama (Klik Trigger Modal JS) --}}
          <img
            src="{{ asset('storage/foto/'.$item->lokasi_file) }}"
            alt="{{ $item->judul_foto }}"
            onclick="openSingleModal({{ $item->id }})"
          >

          {{-- Tombol 3 Titik (Grid) --}}
          <button type="button" class="pin-menu-btn" data-menu-target="pin-menu-{{ $item->id }}" onclick="toggleMenu(event, 'pin-menu-{{ $item->id }}')">
            <i class="bi bi-three-dots"></i>
          </button>

          {{-- Menu Dropdown (Grid) --}}
          <div id="pin-menu-{{ $item->id }}" class="pin-menu">
            <a href="{{ asset('storage/foto/'.$item->lokasi_file) }}" download class="pin-menu-item">
              <i class="bi bi-download"></i> <span>Unduh gambar</span>
            </a>
            <a href="{{ route('profile.public', $item->user->id ?? 0) }}" class="pin-menu-item">
               <i class="bi bi-person"></i> Lihat Profil
            </a> 
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="d-flex justify-content-center mt-5 mb-5">
      {{ $foto->withQueryString()->links() }}
  </div>
</div>

{{-- ===============================================
     SINGLE MODAL (CUKUP SATU SAJA DI SINI)
   =============================================== --}}
<div class="modal fade" id="globalDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border:none;">
      <div class="modal-body p-0">
        <div class="row g-0" style="min-height: 500px;">
          
          {{-- Kiri: Gambar Full --}}
          {{-- GANTI: Class 'bg-black' dihapus/diganti jadi 'bg-light' --}}
          <div class="col-lg-8 bg-light d-flex align-items-center justify-content-center position-relative">
            <div class="modal-image-wrapper">
              <img id="modalImg" src="" alt="">
              
              {{-- Menu 3 Titik di Modal --}}
              <button type="button" class="pin-menu-btn pin-menu-btn-modal" onclick="toggleMenu(event, 'pin-menu-modal-global')">
                <i class="bi bi-three-dots"></i>
              </button>
              <div id="pin-menu-modal-global" class="pin-menu pin-menu-modal">
                <a id="modalDownloadBtn" href="" download class="pin-menu-item">
                  <i class="bi bi-download"></i><span>Unduh gambar</span>
                </a>
              </div>
            </div>
          </div>

          {{-- Kanan: Detail --}}
          <div class="col-lg-4 bg-white d-flex flex-column" style="max-height: 90vh;">
            {{-- Header User --}}
            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="d-flex align-items-center">
                      <a id="modalUserLink" href="#">
                          <img id="modalAvatar" src="" class="rounded-circle me-2" width="36" height="36" style="object-fit: cover;">
                      </a>
                      <div>
                         <a id="modalUsername" href="#" class="fw-bold text-dark text-decoration-none d-block lh-1"></a>
                         <small id="modalTime" class="text-muted" style="font-size: 11px;"></small>
                      </div>
                  </div>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <h5 id="modalTitle" class="fw-bold mb-1 fs-6"></h5>
                <p id="modalDesc" class="text-secondary mb-0 small"></p>
            </div>

            {{-- Body: Komentar --}}
            <div class="flex-grow-1 p-3 overflow-auto custom-scrollbar" style="background: #f9fafb;">
                <div class="mb-3">
                   {{-- Tombol Like --}}
                   <form id="modalLikeForm" method="POST" action="">
                     @csrf
                     <button type="submit" class="btn btn-danger rounded-pill px-4 btn-sm w-100">
                       ❤ Like <span id="modalLikeCount" class="ms-1 fw-bold"></span>
                     </button>
                   </form>
                </div>

                <h6 class="fw-bold small text-muted mb-2">Komentar</h6>
                <div id="modalCommentsList">
                   {{-- Komentar akan diisi lewat JS --}}
                </div>
            </div>

            {{-- Footer: Input Komentar --}}
            @auth
            <div class="p-3 border-top bg-white">
              <form id="modalCommentForm" method="POST" action="">
                @csrf
                <div class="input-group">
                  <input type="text" name="isi_komentar" class="form-control rounded-start-pill ps-3 bg-light border-0" placeholder="Tulis komentar..." required>
                  <button class="btn btn-primary rounded-end-pill px-3">Kirim</button>
                </div>
              </form>
            </div>
            @else
            <div class="p-3 border-top text-center bg-white">
               <small><a href="{{ route('login') }}" class="fw-bold">Login</a> untuk berkomentar</small>
            </div>
            @endauth

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // === 1. Init Masonry (Sama seperti kodemu) ===
  var grid = document.querySelector('#masonry-grid');
  if(grid) {
    imagesLoaded(grid, function() {
      new Masonry(grid, { itemSelector: '.masonry-item', percentPosition: true });
    });
  }

  // === 2. Data dari Backend (PENTING untuk Efisiensi) ===
  const photosData = @json($foto->items());
  const baseUrlFoto = "{{ asset('storage/foto') }}";
  // Fix URL Avatar (Menghapus 'avatars' ganda)
  const baseUrlAvatar = "{{ asset('storage') }}"; 
  const defaultAvatar = "{{ asset('assets/img/default-profile.png') }}";
  
  // Template Route
  const routes = {
      profile: "{{ route('profile.public', '000') }}",
      like: "{{ route('likes.toggle', ['photo' => '000']) }}",
      comment: "{{ route('komentar.store', ['photo' => '000']) }}"
  };

  // === 3. Logic Single Modal ===
  function openSingleModal(id) {
      const item = photosData.find(p => p.id === id);
      if(!item) return;

      // Isi Gambar & Judul
      document.getElementById('modalImg').src = baseUrlFoto + '/' + item.lokasi_file;
      document.getElementById('modalDownloadBtn').href = baseUrlFoto + '/' + item.lokasi_file;
      document.getElementById('modalTitle').innerText = item.judul_foto;
      document.getElementById('modalDesc').innerText = item.deskripsi_foto;

      // Isi User Info
      const user = item.user || {};
      const avatarPath = user.avatar ? (baseUrlAvatar + '/' + user.avatar) : defaultAvatar;
      const profileUrl = routes.profile.replace('000', user.id);
      
      document.getElementById('modalAvatar').src = avatarPath;
      document.getElementById('modalUsername').innerText = user.username || 'Unknown';
      document.getElementById('modalUsername').href = profileUrl;
      document.getElementById('modalUserLink').href = profileUrl;
      
      document.getElementById('modalTime').innerText = new Date(item.created_at).toLocaleDateString();

      // Isi Like
      document.getElementById('modalLikeCount').innerText = item.like ? item.like.length : 0;
      document.getElementById('modalLikeForm').action = routes.like.replace('000', item.id);

      // Isi Form Komentar
      const commForm = document.getElementById('modalCommentForm');
      if(commForm) commForm.action = routes.comment.replace('000', item.id);

      // Render Komentar
      const listDiv = document.getElementById('modalCommentsList');
      listDiv.innerHTML = '';

      if (item.komentarfoto && item.komentarfoto.length > 0) {
          item.komentarfoto.forEach(c => {
              const cUser = c.user || {};
              const cAvatar = cUser.avatar ? (baseUrlAvatar + '/' + cUser.avatar) : defaultAvatar;
              
              const html = `
                <div class="mb-2 d-flex gap-2">
                    <div class="flex-shrink-0">
                        <img src="${cAvatar}" class="rounded-circle" width="28" height="28" style="object-fit: cover;">
                    </div>
                    <div class="bg-white px-3 py-2 rounded-3 shadow-sm border w-100">
                        <span class="fw-bold small d-block">${cUser.username || 'Anonim'}</span>
                        <p class="mb-0 small text-dark lh-sm mt-1">${c.isi_komentar}</p>
                    </div>
                </div>`;
              listDiv.innerHTML += html;
          });
      } else {
          listDiv.innerHTML = '<div class="text-center py-4"><p class="text-muted small">Belum ada komentar.</p></div>';
      }

      // Tampilkan Modal
      const myModal = new bootstrap.Modal(document.getElementById('globalDetailModal'));
      myModal.show();
  }

  // === 4. Logic Menu Dropdown ===
  function toggleMenu(event, menuId) {
    event.stopPropagation(); 
    const menu = document.getElementById(menuId);
    document.querySelectorAll('.pin-menu').forEach(m => {
      if (m.id !== menuId) m.classList.remove('show');
    });
    if (menu) menu.classList.toggle('show');
  }

  document.addEventListener('click', function () {
    document.querySelectorAll('.pin-menu').forEach(m => m.classList.remove('show'));
  });
</script>
@endpush

@endsection
@extends('main')

@section('content')

{{-- LIBRARIES (JANGAN DIHAPUS) --}}
<script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>

<style>
  /* === 1. STYLE PIN (FOTO) - Persis Home === */
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

  /* === 2. STYLE RESULT CARD (USER & ALBUM) === */
  .result-card {
    position: relative; border-radius: 16px; overflow: hidden; background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform .2s ease, box-shadow .2s ease;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 24px; height: 100%; text-decoration: none;
  }
  .result-card:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); z-index: 5; }
  .album-card-style { background: #f8f9fa; color: #333; }
  .album-card-style:hover { background: #fff; }

  /* === 3. MODAL STYLE === */
  .modal-image-wrapper {
    position: relative; width: 100%; height: 100%; background: #f8f9fa;
    display: flex; justify-content: center; align-items: center; min-height: 400px; padding: 24px;
  }
  .modal-image-wrapper img {
      max-width: 100%; max-height: 85vh; object-fit: contain;
      border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.12);
  }
  .pin-menu-btn-modal { top: 24px; right: 24px; opacity: 1; }
  .pin-menu-modal { top: 60px; right: 24px; }
  .custom-scrollbar::-webkit-scrollbar { width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

  /* Comment Styles */
  .comment-item { transition: background-color 0.2s; }
  .comment-delete-btn {
      opacity: 0;
      transition: opacity 0.2s ease, transform 0.2s ease;
      cursor: pointer;
  }
  .comment-item:hover .comment-delete-btn { opacity: 1; }
  .comment-delete-btn:hover { transform: scale(1.1); color: #dc3545 !important; }
  .comment-bubble { border-radius: 12px; background-color: #f8f9fa; }
</style>

<div class="container py-4">
    <h3 class="mb-4 fw-bold px-2">Hasil untuk: "<span>{{ $query }}</span>"</h3>

    {{-- ==========================================
         SECTION 1: USER (PENGGUNA)
         ========================================== --}}
    @if(isset($users) && $users->count() > 0)
        <h5 class="fw-bold mb-3 mt-2 px-2"><i class="bi bi-people me-2"></i>Pengguna</h5>
        <div class="row g-3 mb-5">
            @foreach($users as $user)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="result-card">
                    <a href="{{ route('profile.public', $user->id) }}" class="text-decoration-none text-center">
                        @if($user->avatar)
                            <img src="{{ Str::startsWith($user->avatar, ['http', 'data:']) ? $user->avatar : asset('storage/'.$user->avatar) }}"
                                 class="rounded-circle mb-3 border shadow-sm"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <i class="bi bi-person-circle default-avatar-icon mb-3 d-inline-block" style="font-size: 80px;"></i>
                        @endif
                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 150px;">{{ $user->username }}</h6>
                        <small class="text-muted text-truncate d-block mb-3">{{ $user->fullname }}</small>
                    </a>
                    @auth
                        <form action="{{ route('user.follow', $user->id) }}" method="POST" class="w-100">
                            @csrf
                            @if(Auth::user()->isFollowing($user))
                                <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill w-100 fw-bold"><i class="bi bi-check2"></i> Mengikuti</button>
                            @else
                                <button type="submit" class="btn btn-danger btn-sm rounded-pill w-100 fw-bold">Ikuti</button>
                            @endif
                        </form>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
        <hr class="text-muted my-5">
    @endif

    {{-- ==========================================
         SECTION 2: ALBUM
         ========================================== --}}
    @if(!$albums->isEmpty())
        <h5 class="fw-bold mb-3 px-2"><i class="bi bi-journal-album me-2"></i>Album</h5>
        <div class="row g-3 mb-5">
            @foreach($albums as $album)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('album.show', ['album' => $album->id]) }}" class="text-decoration-none">
                        <div class="result-card album-card-style">
                            <i class="bi bi-folder-fill text-warning display-4 mb-2"></i>
                            <h6 class="text-dark fw-bold text-truncate mb-0 w-100 text-center">{{ $album->nama_album }}</h6>
                            <small class="text-muted">{{ $album->user->username ?? 'Unknown' }}</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <hr class="text-muted my-5">
    @endif

    {{-- ==========================================
         SECTION 3: FOTO (MASONRY FULL FEATURE)
         ========================================== --}}
    @if(!$fotos->isEmpty())
        <h5 class="fw-bold mb-3 px-2"><i class="bi bi-images me-2"></i>Foto</h5>

        <div class="row" id="masonry-grid-search">
            @foreach ($fotos as $item)
            <div class="col-6 col-md-4 col-lg-3 mb-4 masonry-item">

                {{-- PIN WRAPPER (Style Home) --}}
                <div class="pin-wrapper">
                    {{-- Gambar --}}
                    <img src="{{ Str::startsWith($item->lokasi_file, ['http', 'data:']) ? $item->lokasi_file : asset('storage/foto/'.$item->lokasi_file) }}"
                         alt="{{ $item->judul_foto }}"
                         onclick="openSingleModal({{ $item->id }})">

                    {{-- Tombol 3 Titik --}}
                    <button type="button" class="pin-menu-btn" data-menu-target="pin-menu-{{ $item->id }}" onclick="toggleMenu(event, 'pin-menu-{{ $item->id }}')">
                        <i class="bi bi-three-dots"></i>
                    </button>

                    {{-- Menu Dropdown --}}
                    <div id="pin-menu-{{ $item->id }}" class="pin-menu">
                        <a href="{{ Str::startsWith($item->lokasi_file, ['http', 'data:']) ? $item->lokasi_file : asset('storage/foto/'.$item->lokasi_file) }}" download class="pin-menu-item">
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
    @endif

    {{-- State Kosong --}}
    @if(($users->isEmpty() ?? true) && ($albums->isEmpty() ?? true) && ($fotos->isEmpty() ?? true))
        <div class="text-center py-5">
            <img src="{{ asset('assets/img/empty-box.png') }}" style="width: 150px; opacity: 0.5;">
            <p class="text-muted mt-3">Tidak ditemukan hasil untuk "<b>{{ $query }}</b>"</p>
        </div>
    @endif
</div>

{{-- ===============================================
     SINGLE MODAL (SAMA SEPERTI HOME)
   =============================================== --}}
<div class="modal fade" id="globalDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border:none;">
      <div class="modal-body p-0">
        <div class="row g-0" style="min-height: 500px;">

          {{-- Kiri: Gambar --}}
          <div class="col-lg-8 bg-light d-flex align-items-center justify-content-center position-relative">
            <div class="modal-image-wrapper">
              <img id="modalImg" src="" alt="">
              <button type="button" class="pin-menu-btn pin-menu-btn-modal" onclick="toggleMenu(event, 'pin-menu-modal-global')">
                <i class="bi bi-three-dots"></i>
              </button>
              <div id="pin-menu-modal-global" class="pin-menu pin-menu-modal">
                <a id="modalDownloadBtn" href="" download class="pin-menu-item">
                  <i class="bi bi-download"></i><span>Unduh gambar</span>
                </a>
                <button type="button" class="pin-menu-item text-danger" onclick="openReportModal()">
                   <i class="bi bi-flag"></i> <span>Laporkan Gambar</span>
                </button>
              </div>
            </div>
          </div>

          {{-- Kanan: Detail --}}
          <div class="col-lg-4 bg-white d-flex flex-column modal-right-col" style="max-height: 90vh;">
            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="d-flex align-items-center">
                      <a id="modalUserLink" href="#">
                          <div id="modalAvatarContainer"></div>
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

                {{-- Category & Tags Placeholders --}}
                <div id="modalCategoryTag" class="mt-2"></div>
            </div>

            <div class="flex-grow-1 p-3 overflow-auto custom-scrollbar modal-comment-bg" style="background: #f9fafb;">
                <div class="mb-3">
                   <form id="modalLikeForm" method="POST" action="">
                     @csrf
                     <button type="submit" class="btn btn-danger rounded-pill px-4 btn-sm w-100">
                       ❤ Like <span id="modalLikeCount" class="ms-1 fw-bold"></span>
                     </button>
                   </form>
                </div>
                <h6 class="fw-bold small text-muted mb-2">Komentar</h6>
                <div id="modalCommentsList"></div>
            </div>

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
            <div class="p-3 border-top text-center bg-white"><small><a href="{{ route('login') }}" class="fw-bold">Login</a> untuk berkomentar</small></div>
            @endauth
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- REPORT MODAL --}}
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Laporkan Foto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('report.store') }}" method="POST">
        @csrf
        <input type="hidden" name="foto_id" id="reportFotoId">
        <div class="modal-body">
            <p class="mb-3">Mengapa Anda melaporkan foto ini?</p>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="reason" value="Konten seksual atau telanjang" id="r1" required>
                <label class="form-check-label" for="r1">Konten seksual atau telanjang</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="reason" value="Kekerasan atau berbahaya" id="r2">
                <label class="form-check-label" for="r2">Kekerasan atau berbahaya</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="reason" value="Pelecehan atau intimidasi" id="r3">
                <label class="form-check-label" for="r3">Pelecehan atau intimidasi</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="reason" value="Spam atau menyesatkan" id="r4">
                <label class="form-check-label" for="r4">Spam atau menyesatkan</label>
            </div>
        </div>
        <div class="modal-footer border-0">
            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger rounded-pill px-4">Kirim Laporan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // 1. Init Masonry
  var gridSearch = document.querySelector('#masonry-grid-search');
  if(gridSearch) {
    imagesLoaded(gridSearch, function() {
      new Masonry(gridSearch, { itemSelector: '.masonry-item', percentPosition: true });
    });
  }

  // 2. Data Backend
  const photosData = @json($fotos); // <-- Variabel dari SearchController
  const baseUrlFoto = "{{ asset('storage/foto') }}";
  const baseUrlAvatar = "{{ asset('storage') }}";
  const defaultAvatar = "{{ asset('assets/img/default-profile.png') }}";

  /*
  const routes = {
      profile: "{{ route('profile.public', '000') }}",
      like: "{{ route('likes.toggle', ['photo' => '000']) }}",
      comment: "{{ route('komentar.store', ['photo' => '000']) }}"
  };
  */

  // 3. Logic Single Modal
  function openSingleModal(id) {
      const item = photosData.find(p => p.id === id);
      if(!item) return;

      let imgSrc = '';
      if (item.lokasi_file.startsWith('http') || item.lokasi_file.startsWith('data:')) {
          imgSrc = item.lokasi_file;
      } else {
          imgSrc = baseUrlFoto + '/' + item.lokasi_file;
      }
      document.getElementById('modalImg').src = imgSrc;
      document.getElementById('modalDownloadBtn').href = imgSrc;
      document.getElementById('modalTitle').innerText = item.judul_foto;
      document.getElementById('modalDesc').innerText = item.deskripsi_foto;

      // Isi Category & Tags
      const catTagDiv = document.getElementById('modalCategoryTag');
      let catTagHtml = '';
      const baseUrlSearch = "{{ url('search') }}";

      // 1. Kategori
      if(item.category) {
          catTagHtml += `<a href="${baseUrlSearch}?category=${item.category.slug}" class="badge bg-secondary me-2 text-decoration-none">${item.category.name}</a>`;
      }

      // 2. Tags
      if(item.tags && item.tags.length > 0) {
          item.tags.forEach(tag => {
              catTagHtml += `<a href="${baseUrlSearch}?tag=${tag.slug}" class="text-decoration-none me-1" style="font-size:12px;">#${tag.name}</a>`;
          });
      }

      catTagDiv.innerHTML = catTagHtml;

      const user = item.user || {};
      const profileUrl = "/user/" + (user.id || 0);

      const modalAvatarContainer = document.getElementById('modalAvatarContainer');
      if(user.avatar) {
          modalAvatarContainer.innerHTML = `<img src="${baseUrlAvatar}/${user.avatar}" class="rounded-circle me-2" width="36" height="36" style="object-fit: cover;">`;
      } else {
          modalAvatarContainer.innerHTML = `<i class="bi bi-person-circle me-2 default-avatar-icon" style="font-size: 36px;"></i>`;
      }

      document.getElementById('modalUsername').innerText = user.username || 'Unknown';
      document.getElementById('modalUsername').href = profileUrl;
      document.getElementById('modalUserLink').href = profileUrl;
      document.getElementById('modalTime').innerText = new Date(item.created_at).toLocaleDateString();

      document.getElementById('modalLikeCount').innerText = item.like ? item.like.length : 0;
      document.getElementById('modalLikeForm').action = "/albums/" + item.id + "/toggle-like";

      const commForm = document.getElementById('modalCommentForm');
      if(commForm) commForm.action = "/photos/" + item.id + "/komentar";

      const listDiv = document.getElementById('modalCommentsList');
      listDiv.innerHTML = '';

      if (item.komentarfoto && item.komentarfoto.length > 0) {
          item.komentarfoto.forEach(c => {
              const cUser = c.user || {};
              // Avatar Logic Comment
              let avatarHtml = '';
              if(cUser.avatar) {
                  avatarHtml = `<img src="${baseUrlAvatar}/${cUser.avatar}" class="rounded-circle" width="28" height="28" style="object-fit: cover;">`;
              } else {
                  avatarHtml = `<i class="bi bi-person-circle default-avatar-icon" style="font-size: 28px;"></i>`;
              }

              // Delete Button Logic
              let deleteBtn = '';
              if (c.user_id == {{ Auth::id() ?? 'null' }}) {
                 deleteBtn = `
                   <button onclick="deleteComment(${c.id}, this)" class="comment-delete-btn btn btn-link text-secondary p-0 ms-2" style="font-size: 14px; text-decoration: none;" title="Hapus">
                     <i class="bi bi-trash-fill"></i>
                   </button>
                 `;
              }

              const html = `
                <div class="mb-3 d-flex gap-2 comment-item">
                    <div class="flex-shrink-0">${avatarHtml}</div>
                    <div class="bg-light px-3 py-2 shadow-sm border w-100 comment-bubble position-relative">
                        <div class="d-flex justify-content-between align-items-start">
                             <a href="/user/${cUser.id}" class="fw-bold small d-block text-dark text-decoration-none">${cUser.username || 'Anonim'}</a>
                             ${deleteBtn}
                        </div>
                        <p class="mb-0 small text-dark lh-sm mt-1">${c.isi_komentar}</p>
                    </div>
                </div>`;
              listDiv.innerHTML += html;
          });
      } else {
          listDiv.innerHTML = '<div class="text-center py-4"><p class="text-muted small">Belum ada komentar.</p></div>';
      }

      const myModal = new bootstrap.Modal(document.getElementById('globalDetailModal'));
      myModal.show();
  }

  // === Delete Comment Function ===
  function deleteComment(id, btn) {
      if(!confirm('Hapus komentar ini?')) return;

      fetch(`/komentar/${id}`, {
          method: 'DELETE',
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Content-Type': 'application/json'
          }
      })
      .then(res => {
          if(res.ok) {
              const bubble = btn.closest('.d-flex.gap-2');
              if(bubble) bubble.remove();
          } else {
              alert('Gagal menghapus komentar');
          }
      })
      .catch(err => console.error(err));
  }

  // 4. Logic Menu
  function toggleMenu(event, menuId) {
    event.stopPropagation();
    const menu = document.getElementById(menuId);
    document.querySelectorAll('.pin-menu').forEach(m => {
      if (m.id !== menuId) m.classList.remove('show');
    });
    if (menu) menu.classList.toggle('show');
  }

  // === Global Variable for Report ===
  let currentReportFotoId = null;

  function openReportModal() {
      if(currentReportFotoId) {
          document.getElementById('reportFotoId').value = currentReportFotoId;
          const reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
          reportModal.show();
      }
  }

  // Inject logic ke openSingleModal
  const originalOpenModal = openSingleModal;
  openSingleModal = function(id) {
      currentReportFotoId = id; // Simpan ID
      originalOpenModal(id);
  }

  document.addEventListener('click', function () {
    document.querySelectorAll('.pin-menu').forEach(m => m.classList.remove('show'));
  });
</script>
@endpush

@endsection

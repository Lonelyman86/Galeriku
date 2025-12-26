@extends('main')

@section('content')

{{-- 1. SCRIPT WAJIB UNTUK LAYOUT HOME (Masonry JS) --}}
<script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>

<style>
  /* === STYLE FOLDER ALBUM === */
  .folder-card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      background: #fff; cursor: pointer;
  }
  .folder-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
      background: #fdfdfd;
  }

  /* === STYLE GRID FOTO === */
  .pin-wrapper {
    position: relative; border-radius: 16px; overflow: hidden;
    background: #f3f4f6; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform .2s ease, box-shadow .2s ease; margin-bottom: 0;
  }
  .pin-wrapper:hover {
    transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); z-index: 5;
  }
  .pin-wrapper img { width: 100%; height: auto; display: block; cursor: pointer; }

  /* Tombol Menu 3 Titik */
  .pin-menu-btn {
    position: absolute; top: 10px; right: 10px; width: 32px; height: 32px;
    border-radius: 50%; border: 0; background: rgba(0, 0, 0, .6); color: #fff;
    display: flex; align-items: center; justify-content: center; font-size: 16px;
    opacity: 0; transition: all .2s ease; cursor: pointer; z-index: 10;
  }
  .pin-wrapper:hover .pin-menu-btn, .pin-menu-btn.active { opacity: 1; }
  .pin-menu-btn:hover { background: rgba(0, 0, 0, .8); transform: scale(1.1); }

  /* Dropdown Menu */
  .pin-menu {
    position: absolute; top: 46px; right: 10px; min-width: 160px;
    background: #fff; border-radius: 12px; box-shadow: 0 10px 24px rgba(0, 0, 0, .2);
    padding: 6px 0; font-size: 13px; z-index: 20; display: none;
  }
  .pin-menu.show { display: block; animation: fadeInMenu 0.1s ease-out; }
  @keyframes fadeInMenu { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
  .pin-menu-item {
    display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 16px;
    border: 0; background: transparent; text-align: left; font-size: 13px;
    font-weight: 500; color: #333; cursor: pointer; text-decoration: none;
  }
  .pin-menu-item:hover { background: #f3f4f6; }

  /* Modal Style */
  .modal-image-wrapper {
    position: relative; width: 100%; height: 100%; background: #000;
    display: flex; justify-content: center; align-items: center; min-height: 400px;
  }
  .modal-image-wrapper img { max-width: 100%; max-height: 85vh; object-fit: contain; }
  .pin-menu-btn-modal { top: 16px; right: 16px; opacity: 1; }
  .pin-menu-modal { top: 52px; right: 16px; }
</style>

<div class="container py-4">

    {{-- 2. HEADER PROFIL BARU (CLEAN & MODERN) --}}
    <div class="text-center mb-5">
        <div class="position-relative d-inline-block mb-3">
            @if($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}"
                     class="rounded-circle border border-4 border-white shadow-sm"
                     width="120" height="120"
                     style="object-fit: cover;">
            @else
                <i class="bi bi-person-circle default-avatar-icon" style="font-size: 120px; line-height: 1;"></i>
            @endif
        </div>

        <h2 class="fw-bold mb-1 section-title">{{ $user->fullname ?? $user->username }}</h2>
        <p class="text-muted mb-3">{{ '@' . $user->username }}</p>

        {{-- BIO USER --}}
        @if($user->bio)
            <p class="text-secondary mx-auto mb-4" style="max-width: 600px; line-height: 1.6; font-size: 0.95rem;">
                {{ $user->bio }}
            </p>
        @endif

        {{-- TOMBOL FOLLOW / UNFOLLOW --}}
        @auth
            @if(Auth::id() !== $user->id)
                <form action="{{ route('user.follow', $user->id) }}" method="POST" class="d-inline-block mb-4">
                    @csrf
                    @if(Auth::user()->isFollowing($user))
                        <button type="submit" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                            <i class="bi bi-person-check-fill me-1"></i> Mengikuti
                        </button>
                    @else
                        <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold">
                            <i class="bi bi-person-plus-fill me-1"></i> Ikuti
                        </button>
                    @endif
                </form>
            @endif
        @endauth

        {{-- STATISTIK (CLEAN TEXT) --}}
        <div class="d-flex justify-content-center gap-4 gap-md-5 mt-2">
            <div class="text-center">
                <span class="d-block fw-bold fs-5 section-title">{{ $foto->total() }}</span>
                <span class="text-muted small text-uppercase" style="letter-spacing: 1px; font-size: 11px;">Postingan</span>
            </div>
            <div class="text-center">
                <span class="d-block fw-bold fs-5 section-title">{{ $albums->count() }}</span>
                <span class="text-muted small text-uppercase" style="letter-spacing: 1px; font-size: 11px;">Album</span>
            </div>
            <div class="text-center">
                <span class="d-block fw-bold fs-5 section-title">{{ $user->followers->count() }}</span>
                <span class="text-muted small text-uppercase" style="letter-spacing: 1px; font-size: 11px;">Pengikut</span>
            </div>
            <div class="text-center">
                <span class="d-block fw-bold fs-5 section-title">{{ $user->following->count() }}</span>
                <span class="text-muted small text-uppercase" style="letter-spacing: 1px; font-size: 11px;">Mengikuti</span>
            </div>
        </div>
    </div>

    <hr class="my-5 opacity-25">

    {{-- 3. BAGIAN FOTO --}}
    <h4 class="fw-bold mb-4 section-title">Semua Foto</h4>

    <div class="row" id="masonry-grid-photos">
      @include('partials.public-profile-grid', ['foto' => $foto, 'user' => $user])
    </div>

    {{-- Loading Spinner (Photos) --}}
    <div id="loading-spinner-photos" class="spinner-border text-primary d-none mx-auto mt-4 mb-5" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>

    {{-- End of Content Message (Photos) --}}
    <div id="end-of-content-photos" class="text-muted small d-none text-center mt-4 mb-5">
        Sudah sampai bawah
    </div>

    {{-- 4. BAGIAN ALBUM --}}
    @if($albums->count() > 0)
        <div class="d-flex align-items-center mb-4 mt-5">
            <h4 class="fw-bold mb-0 section-title">Album</h4>
            <span class="badge bg-secondary ms-2 rounded-pill">{{ $albums->count() }}</span>
        </div>

        <div class="row mb-5">
            @foreach($albums as $album)
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <a href="{{ route('album.show', $album->id) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-4 h-100 folder-card text-center position-relative overflow-hidden">
                            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-folder-fill text-warning" style="font-size: 4rem;"></i>
                                <h6 class="fw-bold text-dark mt-2 mb-1 text-truncate w-100 section-title">{{ $album->nama_album }}</h6>
                                <p class="text-muted small mb-0 text-truncate w-100">
                                    {{ $album->deskripsi ?? 'Tidak ada deskripsi' }}
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3 pt-0">
                                <small class="text-primary fw-bold" style="font-size: 12px;">Lihat Isi Album <i class="bi bi-arrow-right"></i></small>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

</div>

{{-- ===============================================
     SINGLE MODAL (GLOBAL)
   =============================================== --}}
<div class="modal fade" id="globalDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border:none;">
      <div class="modal-body p-0">
        <div class="row g-0" style="min-height: 500px;">

          {{-- Kiri: Gambar Full --}}
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

                <div id="modalCategoryTag" class="mt-2"></div>
            </div>

            <div class="flex-grow-1 p-3 overflow-auto custom-scrollbar bg-light modal-comment-bg">
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

{{-- SCRIPT INIT MASONRY & MENU --}}
<script>
    // 1. Data Backend
    let photosData = @json($foto->items());
    let nextPageUrl = "{{ $foto->nextPageUrl() }}";
    let isLoading = false;

    // 2. Static Strings
    const baseUrlFoto = "{{ Storage::url('foto') }}";
    const baseUrlAvatar = "{{ Storage::url('') }}";
    const defaultAvatar = "{{ asset('assets/img/default-profile.png') }}";
    const baseUrlSearch = "{{ url('search') }}";

    const routes = {
       profile: "{{ route('profile.public', '000') }}",
       like: "{{ route('likes.toggle', ['photo' => '000']) }}",
       comment: "{{ route('komentar.store', ['photo' => '000']) }}"
    };

    // 3. Init Masonry
    const grid = document.querySelector('#masonry-grid-photos');
    let msnry;
    if(grid) {
        imagesLoaded(grid, function() {
            msnry = new Masonry(grid, { itemSelector: '.masonry-item', percentPosition: true });
        });
    }

    // 4. Infinite Scroll
    window.addEventListener('scroll', () => {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            loadMorePublicImages();
        }
    });

    function loadMorePublicImages() {
        if (isLoading || !nextPageUrl) return;

        isLoading = true;
        document.getElementById('loading-spinner-photos').classList.remove('d-none');

        fetch(nextPageUrl, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.json())
        .then(data => {
            // Append HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;

            const newItems = Array.from(tempDiv.children);
            grid.append(...newItems);

            // Append Data for Modals
            if(data.data && Array.isArray(data.data)) {
                photosData.push(...data.data);
            }

            // Update Next URL
            nextPageUrl = data.next_page_url;
            if(!nextPageUrl) {
                document.getElementById('end-of-content-photos').classList.remove('d-none');
            }

            imagesLoaded(grid, function() {
                if(msnry) {
                    msnry.appended(newItems);
                    msnry.layout();
                } else {
                    msnry = new Masonry(grid, { itemSelector: '.masonry-item', percentPosition: true });
                }
            });

            isLoading = false;
            document.getElementById('loading-spinner-photos').classList.add('d-none');
        })
        .catch(err => {
            console.error(err);
            isLoading = false;
            document.getElementById('loading-spinner-photos').classList.add('d-none');
        });
    }

    // 5. Logic Single Modal
    function openSingleModal(id) {
        const item = photosData.find(p => p.id === id);
        if(!item) return;

        // Gambar & Teks
        document.getElementById('modalImg').src = baseUrlFoto + '/' + item.lokasi_file;
        document.getElementById('modalDownloadBtn').href = baseUrlFoto + '/' + item.lokasi_file;
        document.getElementById('modalTitle').innerText = item.judul_foto;
        document.getElementById('modalDesc').innerText = item.deskripsi_foto;

        // Categories & Tags
        const catTagDiv = document.getElementById('modalCategoryTag');
        let catTagHtml = '';
        if(item.category) {
            catTagHtml += `<a href="${baseUrlSearch}?category=${item.category.slug}" class="badge bg-secondary me-2 text-decoration-none">${item.category.name}</a>`;
        }
        if(item.tags && item.tags.length > 0) {
            item.tags.forEach(tag => {
                catTagHtml += `<a href="${baseUrlSearch}?tag=${tag.slug}" class="text-decoration-none me-1" style="font-size:12px;">#${tag.name}</a>`;
            });
        }
        catTagDiv.innerHTML = catTagHtml;

        // User Info
        const user = item.user || {};
        const profileUrl = routes.profile.replace('000', user.id);

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

        // Like & Comment Form
        document.getElementById('modalLikeCount').innerText = item.like ? item.like.length : 0;
        document.getElementById('modalLikeForm').action = routes.like.replace('000', item.id);
        const commForm = document.getElementById('modalCommentForm');
        if(commForm) commForm.action = routes.comment.replace('000', item.id);

        // List Comments
        const listDiv = document.getElementById('modalCommentsList');
        listDiv.innerHTML = '';

        if (item.komentarfoto && item.komentarfoto.length > 0) {
           item.komentarfoto.forEach(c => {
               const cUser = c.user || {};
               let avatarHtml = '';
               if(cUser.avatar) {
                   avatarHtml = `<img src="${baseUrlAvatar}/${cUser.avatar}" class="rounded-circle" width="28" height="28" style="object-fit: cover;">`;
               } else {
                   avatarHtml = `<i class="bi bi-person-circle default-avatar-icon" style="font-size: 28px;"></i>`;
               }

               const html = `
                 <div class="mb-2 d-flex gap-2">
                     <div class="flex-shrink-0">${avatarHtml}</div>
                     <div class="bg-white px-3 py-2 rounded-3 shadow-sm border w-100 comment-bubble">
                        <span class="fw-bold small d-block comment-user">${cUser.username || 'Anonim'}</span>
                        <p class="mb-0 small text-dark lh-sm mt-1 comment-text">${c.isi_komentar}</p>
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

    // 6. Menu Logic
    function toggleMenu(event, menuId) {
       event.stopPropagation();
       const menu = document.getElementById(menuId);
       document.querySelectorAll('.pin-menu').forEach(m => {
          if (m.id !== menuId) m.classList.remove('show');
       });
       if (menu) menu.classList.toggle('show');
    }

    // 7. Report Logic
    let currentReportFotoId = null;
    function openReportModal() {
        if(currentReportFotoId) {
            document.getElementById('reportFotoId').value = currentReportFotoId;
            const reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
            reportModal.show();
        }
    }
    const originalOpenModal = openSingleModal;
    openSingleModal = function(id) {
       currentReportFotoId = id;
       originalOpenModal(id);
    }

    document.addEventListener('click', function () {
       document.querySelectorAll('.pin-menu').forEach(m => m.classList.remove('show'));
    });
</script>

@endsection

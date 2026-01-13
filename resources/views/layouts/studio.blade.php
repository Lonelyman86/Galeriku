@extends('main')
@section('content')

<style>
  /* Status Overlay */
  .status-overlay{ position:absolute; inset:0; display:flex; justify-content:center; align-items:center;
                   color:#fff; background:rgba(0,0,0,0.5); opacity:0; transition:0.3s; border-radius:16px; font-weight:bold;}
  .pin:hover .status-overlay { opacity:1; }
  .status-rejected { background:rgba(220,53,69,0.8); }
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

{{-- Tombol Tambah (FAB) --}}
<div class="fab-container" style="position: fixed; right: 24px; bottom: 24px; z-index: 99;">
  <button class="btn btn-danger rounded-circle shadow d-flex align-items-center justify-content-center"
          type="button" data-bs-toggle="dropdown" aria-expanded="false"
          style="width: 56px; height: 56px;">
    <i class="bi bi-plus-lg fs-4"></i>
  </button>
  <ul class="dropdown-menu dropdown-menu-end mb-2 shadow rounded-3 border-0">
    <li><a class="dropdown-item py-2" href="/createalbum"><i class="bi bi-folder-plus me-2"></i>Buat Album Baru</a></li>
    <li><a class="dropdown-item py-2" href="/createfoto"><i class="bi bi-cloud-upload me-2"></i>Upload Foto Baru</a></li>
  </ul>
</div>

<div class="container-fluid py-4">
  {{-- HERO STATS SECTION (Pinned Style - Studio) --}}
  <div class="row g-4 mb-5">
      <div class="col-12">
          <div class="bg-white rounded-5 p-4 shadow-sm d-flex align-items-center justify-content-between position-relative overflow-hidden">
              {{-- Decorative BG (Blue Gradient) --}}
              <div class="position-absolute top-0 start-0 w-100 h-100 opacity-25"
                   style="background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%); z-index:0;"></div>

              <div class="position-relative z-1 d-flex align-items-center gap-3">
                  <div class="bg-white p-3 rounded-circle shadow-sm text-primary d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                      <i class="bi bi-palette-fill fs-3"></i>
                  </div>
                  <div>
                      <h1 class="fw-bold mb-0 fs-4 text-dark">Studio Saya</h1>
                      <p class="text-muted mb-0 small">Kelola karya dan album Anda.</p>
                  </div>
              </div>

              <div class="position-relative z-1 d-none d-md-flex gap-2">
                   {{-- Stats Pills (Hidden on very small screens if needed, but lets keep d-md-flex or just handle overflow) --}}
                   {{-- Actually, if I just use d-flex, it might overflow. Let's try to replicate the 'Liked' behavior but adapted. --}}
                   {{-- User wants consistency. Liked page has stats on right. --}}
                   {{-- Since Studio has 3 pills, maybe hide text label on mobile? --}}
                   <div class="bg-primary text-white px-3 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2">
                      <span class="fs-5 fw-bold lh-1">{{ $totalPhotos }}</span>
                      <span class="small text-uppercase opacity-75" style="letter-spacing:1px; font-size: 9px;">Foto</span>
                   </div>
                   <div class="bg-danger text-white px-3 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2">
                      <span class="fs-5 fw-bold lh-1">{{ $totalLikes }}</span>
                      <span class="small text-uppercase opacity-75" style="letter-spacing:1px; font-size: 9px;">Suka</span>
                   </div>
                   <div class="bg-info text-white px-3 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2">
                      <span class="fs-5 fw-bold lh-1">{{ $totalComments }}</span>
                      <span class="small text-uppercase opacity-75" style="letter-spacing:1px; font-size: 9px;">Komen</span>
                   </div>
              </div>

               {{-- Mobile Stats (Compact) --}}
               <div class="position-relative z-1 d-flex d-md-none gap-1 flex-column align-items-end">
                   <span class="badge bg-primary rounded-pill">{{ $totalPhotos }} Foto</span>
                   <span class="badge bg-danger rounded-pill">{{ $totalLikes }} Suka</span>
               </div>
          </div>
      </div>
  </div>

  {{-- ALBUM SECTION (Horizontal Scroll - "Stories" Style) --}}
  <div class="mb-5">
     <div class="d-flex justify-content-between align-items-center mb-3 px-2">
        <h5 class="fw-bold mb-0"><i class="bi bi-journal-album me-2 text-warning"></i>Album Saya</h5>
        <a href="/createalbum" class="btn btn-sm btn-outline-dark rounded-pill fw-bold">+ Buat Album</a>
     </div>

     <div class="d-flex gap-3 overflow-auto pb-3 custom-scrollbar px-2" style="white-space: nowrap;">
        @if ($albums->count() > 0)
            @foreach ($albums as $album)
            <div class="card border-0 shadow-sm flex-shrink-0" style="width: 200px; border-radius: 16px; transition: transform .2s;">
                <div class="card-body p-3 d-flex flex-column justify-content-between" style="height: 140px;">
                    <div>
                        <h6 class="fw-bold text-truncate mb-1" title="{{ $album->nama_album }}">{{ $album->nama_album }}</h6>
                        <small class="text-muted d-block text-truncate" style="font-size:11px;">{{ $album->deskripsi ?? 'Tanpa deskripsi' }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-3">
                        <a href="{{ route('album.show', $album->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 py-1" style="font-size:11px;">Lihat</a>
                        <form action="{{ route('albums.destroy', $album->id) }}" method="POST" onsubmit="return confirm('Hapus album ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-link text-danger p-0 m-0" style="font-size:14px;"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center w-100 py-4 border rounded-4 border-dashed">
                <p class="text-muted small mb-2">Belum ada album.</p>
                <a href="/createalbum" class="btn btn-sm btn-primary rounded-pill">Buat Sekarang</a>
            </div>
        @endif
     </div>
  </div>

  <h5 class="fw-bold mb-3 px-2">Galeri Foto</h5>
  <div class="masonry" id="studio-masonry-grid">
    @include('partials.studio-grid', ['foto' => $foto])
  </div>

  {{-- End Content Marker --}}
  <div id="end-of-content" class="text-muted small d-none text-center mt-4 mb-5">
    Sudah sampai bawah
  </div>
</div>

{{-- ===============================================
     SINGLE STUDIO MODAL (UPDATED to match Home)
   =============================================== --}}
<div class="modal fade" id="studioModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border:none;">
      <div class="modal-body p-0">
        <div class="row g-0" style="min-height: 500px;">

          {{-- Kiri: Gambar Full --}}
          <div class="col-lg-8 bg-light d-flex align-items-center justify-content-center position-relative">
            <div class="modal-image-wrapper">
              <img id="studioImg" src="" alt="" style="max-height: 85vh; object-fit: contain; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.12);">
            </div>
          </div>

          {{-- Kanan: Detail --}}
          <div class="col-lg-4 bg-white d-flex flex-column" style="max-height: 90vh;">
            {{-- Header User --}}
            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="d-flex align-items-center">
                      <div id="studioAvatarContainer" class="me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                          {{-- JS will inject IMG or ICON here --}}
                      </div>
                      <div>
                         <span id="studioUsername" class="fw-bold text-dark d-block lh-1"></span>
                         <small id="studioTime" class="text-muted" style="font-size: 11px;"></small>
                      </div>
                  </div>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Alert Status (Studio Specific) --}}
                <div id="alertStatus" class="alert alert-warning d-none py-2 small mb-2"></div>

                <h5 id="studioTitle" class="fw-bold mb-1 fs-6"></h5>
                <p id="studioDesc" class="text-secondary mb-0 small"></p>

                {{-- Category & Tags Placeholders --}}
                <div id="studioCategoryTag" class="mt-2"></div>
            </div>

            {{-- Studio Actions (Specific) --}}
            <div class="p-3 bg-light border-bottom">
                <div class="d-grid gap-2 mb-2">
                    <form id="formDelete" action="" method="POST" onsubmit="return confirm('Yakin hapus foto ini?')">
                        @csrf @method('delete')
                        <button class="btn btn-outline-danger btn-sm w-100 rounded-pill">Hapus Foto</button>
                    </form>

                    <button class="btn btn-outline-primary btn-sm rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAlbum">
                        Pindahkan Album
                    </button>
                </div>

                <div class="collapse" id="collapseAlbum">
                    <div class="card card-body p-2 bg-white border-0 shadow-sm rounded-4">
                        <form id="formAlbum" action="" method="POST">
                            @csrf
                            <label class="small text-muted mb-1">Pilih Album:</label>
                            <select name="album_id" class="form-select form-select-sm mb-2" id="selectAlbum">
                                <option value="">-- Lepas dari Album --</option>
                                @foreach($albums as $alb)
                                <option value="{{ $alb->id }}">{{ $alb->nama_album }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary btn-sm w-100 rounded-pill">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Body: Komentar --}}
            <div class="flex-grow-1 p-3 overflow-auto custom-scrollbar bg-white">
                <div class="mb-3">
                   <button class="btn btn-danger rounded-pill px-4 btn-sm w-100" disabled>
                     ❤ Like <span id="studioLikeCount" class="ms-1 fw-bold"></span>
                   </button>
                </div>

                <h6 class="fw-bold small text-muted mb-2">Komentar Masuk</h6>
                <div id="studioComments"></div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    let studioPhotos = @json($foto->items());
    let nextPageUrl = "{{ $foto->nextPageUrl() }}";
    let isLoading = false;

    // Init Masonry Global
    const grid = document.querySelector('#studio-masonry-grid');
    let msnry;
    if(grid) {
        imagesLoaded(grid, function() {
            msnry = new Masonry(grid, { itemSelector: '.masonry-item', percentPosition: true });
        });
    }

    // Infinite Scroll
    window.addEventListener('scroll', () => {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            loadMoreStudioImages();
        }
    });

    function loadMoreStudioImages() {
        if (isLoading || !nextPageUrl) return;

        isLoading = true;
        document.getElementById('loading-spinner').classList.remove('d-none');

        fetch(nextPageUrl, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.json())
        .then(data => {
            // 1. Append HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;

            const newItems = Array.from(tempDiv.children);
            grid.append(...newItems);

            // 2. Update Data for Modals
            if(data.data && Array.isArray(data.data)) {
                studioPhotos.push(...data.data);
            }

            // 3. Update Next URL
            nextPageUrl = data.next_page_url;
            if(!nextPageUrl) {
                document.getElementById('end-of-content').classList.remove('d-none');
            }

            // 4. Update Masonry
            imagesLoaded(grid, function() {
                if(msnry) {
                    msnry.appended(newItems);
                    msnry.layout();
                } else {
                    msnry = new Masonry(grid, { itemSelector: '.masonry-item', percentPosition: true });
                }
            });

            isLoading = false;
            document.getElementById('loading-spinner').classList.add('d-none');
        })
        .catch(err => {
            console.error(err);
            isLoading = false;
            document.getElementById('loading-spinner').classList.add('d-none');
        });
    }

    const baseUrl = "{{ asset('storage/foto') }}";
    const baseUrlAvatar = "{{ asset('storage') }}";
    const defaultAvatar = "{{ asset('assets/img/default-profile.png') }}";

    // Route Templates
    // const routeDelete = "{{ route('photos.destroy', '000', false) }}";
    // const routeAlbum = "{{ route('foto.update.album', ['photo' => '000'], false) }}";

    // Expose to window to ensure global access
    window.openStudioModal = function(id) {
        // Use loose equality (==) to handle string/number differences
        const photo = studioPhotos.find(p => p.id == id);

        if(!photo) {
            console.error('Photo not found:', id);
            return;
        }

        // Ensure Bootstrap is loaded
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap JS not loaded');
            alert('Error: Bootstrap JS library is missing.');
            return;
        }

        // 1. Populate Data Dasar (Gambar, Judul, Deskripsi)
        const imgEl = document.getElementById('studioImg');
        if(imgEl) {
            if (photo.lokasi_file.startsWith('http') || photo.lokasi_file.startsWith('data:')) {
                imgEl.src = photo.lokasi_file;
            } else {
                imgEl.src = baseUrl + '/' + photo.lokasi_file;
            }
        }

        document.getElementById('studioTitle').innerText = photo.judul_foto || '';
        document.getElementById('studioDesc').innerText = photo.deskripsi_foto || '';

        const dateEl = document.getElementById('studioTime');
        if(dateEl && photo.created_at) {
            dateEl.innerText = new Date(photo.created_at).toLocaleDateString();
        }

        // 2. User Info (Diri Sendiri)
        const user = photo.user || {};
        const avatarContainer = document.getElementById('studioAvatarContainer');

        if(avatarContainer) {
            if(user.avatar) {
                avatarContainer.innerHTML = `<img src="${baseUrlAvatar}/${user.avatar}" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">`;
            } else {
                avatarContainer.innerHTML = `<i class="bi bi-person-circle default-avatar-icon" style="font-size: 40px;"></i>`;
            }
        }

        const usernameEl = document.getElementById('studioUsername');
        if(usernameEl) usernameEl.innerText = user.fullname || user.username || 'Saya';

        const usernameEl = document.getElementById('studioUsername');
        if(usernameEl) usernameEl.innerText = user.username || 'Saya';

        // 3. Category & Tags
        let catTagHtml = '';
        const baseUrlSearch = "{{ url('search') }}";

        if(photo.category) {
            catTagHtml += `<a href="${baseUrlSearch}?category=${photo.category.slug}" class="badge bg-secondary me-2 text-decoration-none">${photo.category.name}</a>`;
        }
        if(photo.tags && photo.tags.length > 0) {
            photo.tags.forEach(tag => {
                catTagHtml += `<span class="text-primary small me-1">#${tag.name}</span>`;
            });
        }
        document.getElementById('studioCategoryTag').innerHTML = catTagHtml;

        // 4. Handle Status Alert
        const alertBox = document.getElementById('alertStatus');
        if(alertBox) {
            if(photo.status !== 'approved') {
                alertBox.classList.remove('d-none');
                alertBox.innerText = `Status: ${photo.status.toUpperCase()} ${photo.note ? '('+photo.note+')' : ''}`;
                alertBox.className = `alert py-2 small mb-2 ${photo.status === 'rejected' ? 'alert-danger' : 'alert-warning'}`;
            } else {
                alertBox.classList.add('d-none');
            }
        }

        // 5. Update Form Actions (Delete & Album)
        const formDelete = document.getElementById('formDelete');
        if(formDelete) formDelete.action = "/photos/" + id;

        const formAlbum = document.getElementById('formAlbum');
        if(formAlbum) formAlbum.action = "/foto/" + id + "/update-album";

        // 6. Set Selected Album
        const selectAlb = document.getElementById('selectAlbum');
        if(selectAlb) selectAlb.value = photo.album_id || "";

        // 7. Like Count
        const likeCountEl = document.getElementById('studioLikeCount');
        if(likeCountEl) likeCountEl.innerText = photo.like ? photo.like.length : 0;

        // 8. Render Komentar
        const commDiv = document.getElementById('studioComments');
        if(commDiv) {
            commDiv.innerHTML = '';
            if(photo.komentarfoto && photo.komentarfoto.length > 0){
                photo.komentarfoto.forEach(c => {
                     const u = c.user ? c.user.username : 'Anonim';

                     let avatarHtml = '';
                     if(c.user && c.user.avatar) {
                        avatarHtml = `<img src="${baseUrlAvatar}/${c.user.avatar}" class="rounded-circle border" width="32" height="32" style="object-fit: cover;">`;
                     } else {
                        avatarHtml = `<i class="bi bi-person-circle default-avatar-icon" style="font-size: 32px;"></i>`;
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

                     commDiv.innerHTML += `
                        <div class="mb-3 d-flex gap-2 comment-item">
                            <div class="flex-shrink-0">
                                ${avatarHtml}
                            </div>
                            <div class="bg-light px-3 py-2 shadow-sm border w-100 comment-bubble position-relative">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="fw-bold small d-block text-dark">${u}</span>
                                    ${deleteBtn}
                                </div>
                                <p class="mb-0 small text-dark lh-sm mt-1">${c.isi_komentar}</p>
                            </div>
                        </div>`;
                });
            } else {
                commDiv.innerHTML = '<div class="text-center py-4"><p class="text-muted small">Belum ada komentar.</p></div>';
            }
        }

        // 9. Show Modal
        try {
            const modalEl = document.getElementById('studioModal');
            if(modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                console.error('Modal element #studioModal not found');
            }
        } catch(e) {
            console.error('Failed to show modal:', e);
            alert('Gagal membuka modal. Pastikan halaman termuat sempurna.');
        }
    }
</script>
@endpush

@endsection

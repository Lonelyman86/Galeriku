@extends('main')

@section('content')

{{-- LIBRARIES --}}
<script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<style>
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



{{-- FAB: ambil foto dari galeri --}}
@if($album->user_id == Auth::id())
<div class="fab-create">
  <button class="fab-btn"
          data-bs-toggle="modal"
          data-bs-target="#modalAddPhotos"
          title="Ambil Foto dari Galeri">
    ＋
  </button>
</div>
@endif

<div class="container-fluid py-4">
  @php
    $bgStyle = "background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);";
    $hasImage = false;
    if($album->cover_image) {
        $bgStyle = "background: url('" . asset('storage/' . $album->cover_image) . "') center/cover no-repeat;";
        $hasImage = true;
    }
  @endphp

  <div class="album-hero rounded-4 p-4 p-md-5 mb-5 text-white position-relative overflow-hidden shadow-sm"
       style="{{ $bgStyle }}">

    @if($hasImage)
      <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>
    @else
      {{-- Decorative Circle (Only for gradient) --}}
      <div class="position-absolute top-0 end-0 bg-white opacity-10 rounded-circle"
           style="width: 200px; height: 200px; transform: translate(50%, -50%);"></div>
      <div class="position-absolute bottom-0 start-0 bg-black opacity-10 rounded-circle"
           style="width: 150px; height: 150px; transform: translate(-30%, 30%);"></div>
    @endif

    <div class="position-relative z-1">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <span class="badge bg-white text-danger mb-2 px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-journal-album me-1"></i> Album Koleksi
                </span>
                <h1 class="display-5 fw-bold mb-2">{{ $album->nama_album }}</h1>
                <p class="fs-5 opacity-75 mb-4" style="max-width: 600px;">
                    {{ $album->deskripsi ?? 'Tidak ada deskripsi untuk album ini.' }}
                </p>

                <div class="d-flex align-items-center gap-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-images fs-4 me-2"></i>
                        <span class="fw-bold">{{ $foto->total() }}</span> <span class="ms-1 opacity-75">Foto</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-event fs-4 me-2"></i>
                        <span class="opacity-75">Dibuat {{ $album->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            @if($album->user_id == Auth::id())
            <div>
                <button class="btn btn-light text-danger fw-bold shadow-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#editAlbumModal">
                    <i class="bi bi-pencil-fill me-2"></i> Edit Album
                </button>
            </div>
            @endif
        </div>
    </div>
  </div>

<div class="row" id="album-masonry-grid">
  @include('partials.album-grid', ['foto' => $foto, 'album' => $album])
</div>

{{-- Loading Spinner --}}
<div id="loading-spinner" class="spinner-border text-primary d-none mx-auto mt-4 mb-5" role="status">
    <span class="visually-hidden">Loading...</span>
</div>

{{-- End of Content Message --}}
<div id="end-of-content" class="text-muted small d-none text-center mt-4 mb-5">
    Sudah sampai bawah
</div>
</div> {{-- End container-fluid --}}

{{-- Modal "Ambil Foto dari Galeri" --}}
@if($album->user_id == Auth::id())
<div class="modal fade" id="modalAddPhotos" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form method="POST" action="{{ route('album.add_existing', $album->id) }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Pilih Foto dari Galeri Anda</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body bg-light">
        @if(isset($fotoTersedia) && $fotoTersedia->count() > 0)
          <div class="row g-2">
            @foreach($fotoTersedia as $fotoItem)
              <div class="col-6 col-sm-4 col-md-3">
                <label class="img-checkbox-container w-100">
                  <input type="checkbox" name="foto_ids[]" value="{{ $fotoItem->id }}">
                  <img src="{{ Str::startsWith($fotoItem->lokasi_file, ['http', 'data:']) ? $fotoItem->lokasi_file : asset('storage/foto/'.$fotoItem->lokasi_file) }}"
                       alt="{{ $fotoItem->judul_foto }}"
                       loading="lazy">
                </label>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-5">
            <p class="text-muted">Tidak ada foto lain yang tersedia untuk ditambahkan.</p>
            <a href="/createfoto" class="btn btn-sm btn-outline-primary">Upload Foto Baru</a>
          </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary"
          {{ (isset($fotoTersedia) && $fotoTersedia->count() == 0) ? 'disabled' : '' }}>
          Simpan ke Album
        </button>
      </div>
    </form>
  </div>
</div>
@endif

{{-- Modal Edit Album --}}
@if($album->user_id == Auth::id())
<div class="modal fade" id="editAlbumModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="{{ route('albums.update', $album->id) }}" class="modal-content rounded-5 border-0 shadow-lg p-3" enctype="multipart/form-data">
      @csrf
      @method('PATCH')

      <div class="modal-header border-0 pb-0 justify-content-center position-relative">
        <h5 class="modal-title fw-bold fs-3">Edit Album</h5>
        {{-- Tombol Close Absolute di pojok kanan --}}
        <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-3" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="mb-4">
          <label class="form-label fw-bold small ms-1">Nama album</label>
          <input type="text"
                 name="nama_album"
                 class="form-control form-control-lg rounded-pill bg-light border-0 px-4"
                 value="{{ old('nama_album', $album->nama_album) }}"
                 required
                 placeholder="Beri nama yang menarik...">
          @error('nama_album')
            <div class="invalid-feedback d-block ms-1">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-2">
          <label class="form-label fw-bold small ms-1">Deskripsi</label>
          <textarea name="deskripsi"
                    class="form-control rounded-4 bg-light border-0 p-3"
                    rows="4"
                    required
                    placeholder="Ceritakan sedikit tentang album ini...">{{ old('deskripsi', $album->deskripsi) }}</textarea>
          @error('deskripsi')
            <div class="invalid-feedback d-block ms-1">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-2">
           <label class="form-label fw-bold small ms-1">Cover Image (Opsional)</label>
           <input type="file" name="cover_image" class="form-control rounded-pill bg-light border-0" onchange="previewCover(this)">
           <small class="text-muted ms-2" style="font-size:11px;">Maks: 2MB</small>
        </div>
      </div>

      <div class="modal-footer border-0 pt-0 justify-content-end">
        <button type="button" class="btn btn-ghost rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endif

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

                {{-- Status Alert (Shown via JS) --}}
                <div id="modalStatusAlert"></div>

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

@push('scripts')
<script>
    let photosData = @json($foto->items());
    let nextPageUrl = "{{ $foto->nextPageUrl() }}";
    let isLoading = false;

    // Static Strings
    const baseUrlFoto = "{{ asset('storage/foto') }}";
    const baseUrlAvatar = "{{ asset('storage') }}";
    const defaultAvatar = "{{ asset('assets/img/default-profile.png') }}";
    const baseUrlSearch = "{{ url('search') }}";

    const routes = {
       profile: "{{ url('user') }}/000",
       // like & comment handled manually below
    };

    // Init Masonry Global
    const grid = document.querySelector('#album-masonry-grid');
    let msnry;
    if(grid) {
        imagesLoaded(grid, function() {
            msnry = new Masonry(grid, { itemSelector: '.masonry-item', percentPosition: true });
        });
    }

    // Infinite Scroll
    window.addEventListener('scroll', () => {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            loadMoreAlbumImages();
        }
    });

    function loadMoreAlbumImages() {
        if (isLoading || !nextPageUrl) return;

        isLoading = true;
        document.getElementById('loading-spinner').classList.remove('d-none');

        fetch(nextPageUrl, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.json())
        .then(data => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;

            const newItems = Array.from(tempDiv.children);
            grid.append(...newItems);

            if(data.data && Array.isArray(data.data)) {
                photosData.push(...data.data);
            }

            nextPageUrl = data.next_page_url;
            if(!nextPageUrl) {
                document.getElementById('end-of-content').classList.remove('d-none');
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
            document.getElementById('loading-spinner').classList.add('d-none');
        })
        .catch(err => {
            console.error(err);
            isLoading = false;
            document.getElementById('loading-spinner').classList.add('d-none');
        });
    }

    // Logic Single Modal
    function openSingleModal(id) {
        const item = photosData.find(p => p.id == id);
        if(!item) return;

        // Status Alert Logic
        const statusDiv = document.getElementById('modalStatusAlert');
        statusDiv.innerHTML = ''; // Reset

        if (item.status == 'pending') {
            statusDiv.innerHTML = `<div class="alert alert-warning p-2 small mb-3"><strong>Status:</strong> Menunggu Persetujuan Admin</div>`;
        } else if (item.status == 'rejected') {
            statusDiv.innerHTML = `<div class="alert alert-danger p-2 small mb-3"><strong>Status:</strong> Ditolak<br>Alasan: ${item.note || 'Tidak ada alasan'}</div>`;
        }

        // Gambar & Teks
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
        // Profile Link
        const profileUrl = "/user/" + (user.id || 0);
        document.getElementById('modalUserLink').href = profileUrl;
        if(user.avatar) {
             modalAvatarContainer.innerHTML = `<img src="${baseUrlAvatar}/${user.avatar}" class="rounded-circle me-2" width="36" height="36" style="object-fit: cover;">`;
        } else {
             modalAvatarContainer.innerHTML = `<i class="bi bi-person-circle me-2 default-avatar-icon" style="font-size: 36px;"></i>`;
        }

        document.getElementById('modalUsername').innerText = user.username || 'Unknown';
        document.getElementById('modalUsername').href = profileUrl;
        document.getElementById('modalUserLink').href = profileUrl;
        document.getElementById('modalTime').innerText = new Date(item.created_at).toLocaleDateString();

        // Like & Comment Logic (Enable only if approved, or disable if desired)
        // Usually, pending photos shouldn't be liked/commented?
        // Logic in album-grid was: if != approved, disable buttons.
        const likeBtn = document.querySelector('#modalLikeForm button');
        const commentInput = document.querySelector('input[name="isi_komentar"]');
        const commentBtn = document.querySelector('#modalCommentForm button');

        if(item.status !== 'approved') {
            if(likeBtn) likeBtn.disabled = true;
            if(commentInput) { commentInput.disabled = true; commentInput.placeholder = 'Komentar nonaktif (Status Pending/Rejected)'; }
            if(commentBtn) commentBtn.disabled = true;
        } else {
             if(likeBtn) likeBtn.disabled = false;
             if(commentInput) { commentInput.disabled = false; commentInput.placeholder = 'Tulis komentar...'; }
             if(commentBtn) commentBtn.disabled = false;
        }

        // Like Logic
        document.getElementById('modalLikeCount').innerText = item.like ? item.like.length : 0;
        document.getElementById('modalLikeForm').action = "/albums/" + item.id + "/toggle-like";

        // Comment Logic
        const commForm = document.getElementById('modalCommentForm');
        if(commForm) commForm.action = "/photos/" + item.id + "/komentar";

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

               // Delete Button
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

    function toggleMenu(event, menuId) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        document.querySelectorAll('.pin-menu').forEach(m => {
          if (m.id !== menuId) m.classList.remove('show');
        });
        if (menu) menu.classList.toggle('show');
    }

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
@endpush

@endsection

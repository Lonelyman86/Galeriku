@extends('main')

@section('content')
<div class="container-fluid py-4">
    {{-- HERO SECTION (Pinned Style - Discovery) --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="bg-white rounded-5 p-4 shadow-sm d-flex align-items-center justify-content-between position-relative overflow-hidden">
                {{-- Decorative BG (Indigo/Purple Gradient) --}}
                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-25"
                     style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); z-index:0;"></div>

                <div class="position-relative z-1 d-flex align-items-center gap-3">
                    <div class="bg-white p-3 rounded-circle shadow-sm text-info d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-lightbulb-fill fs-3"></i>
                    </div>
                    <div>
                        <h1 class="fw-bold mb-0 fs-4 text-dark">Temukan Inspirasi</h1>
                        <p class="text-muted mb-0 small">Jelajahi ide-ide segar setiap hari.</p>
                    </div>
                </div>

                {{-- Search Bar Integrated or Decoration --}}
                <div class="position-relative z-1 d-none d-md-block">
                     <div class="bg-primary text-white px-4 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-compass-fill"></i>
                        <span class="small text-uppercase fw-bold" style="letter-spacing:1px; font-size: 10px;">Jelajahi</span>
                     </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 d-lg-none">
        <form action="{{ route('search') }}" method="GET">
            <input class="form-control pinterest-search" type="search" name="q" placeholder="Cari foto atau album..." aria-label="Search" value="{{ request('q') }}">
        </form>
    </div>

    {{-- Kategori Grid --}}
    <h4 class="fw-bold mb-4 px-2">Kategori Populer</h4>
    <div class="row px-2 g-3 mb-5">
        @foreach($categories as $cat)
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('search', ['category' => $cat->slug]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 folder-card position-relative overflow-hidden text-white" style="border-radius: 16px;">
                    {{-- Dynamic Category Preview --}}
                    <div style="height: 120px; overflow: hidden; background: #eee;">
                        @php $previews = $cat->preview_photos ?? collect([]); @endphp

                        @if($previews->count() >= 3)
                            {{-- 3 Images (1 Big Left, 2 Small Right Stacked) --}}
                            <div class="d-flex w-100 h-100">
                                <div class="w-65 h-100 border-end border-white position-relative" style="width: 66%;">
                                    <img src="{{ Str::startsWith($previews[0]->lokasi_file, ['http', 'data:']) ? $previews[0]->lokasi_file : asset('storage/foto/' . $previews[0]->lokasi_file) }}" class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="h-100 d-flex flex-column" style="width: 34%;">
                                    <div class="h-50 w-100 border-bottom border-white">
                                        <img src="{{ Str::startsWith($previews[1]->lokasi_file, ['http', 'data:']) ? $previews[1]->lokasi_file : asset('storage/foto/' . $previews[1]->lokasi_file) }}" class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                    <div class="h-50 w-100 position-relative">
                                         <img src="{{ Str::startsWith($previews[2]->lokasi_file, ['http', 'data:']) ? $previews[2]->lokasi_file : asset('storage/foto/' . $previews[2]->lokasi_file) }}" class="w-100 h-100" style="object-fit: cover;">
                                         {{-- Overflow Indicator (if more than 3 total) --}}
                                         @if($cat->fotos_count > 3)
                                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-three-dots text-white fw-bold fs-5"></i>
                                            </div>
                                         @endif
                                    </div>
                                </div>
                            </div>
                        @elseif($previews->count() == 2)
                             {{-- 2 Images Split --}}
                             <div class="d-flex w-100 h-100">
                                <div class="w-50 h-100 border-end border-white">
                                     <img src="{{ Str::startsWith($previews[0]->lokasi_file, ['http', 'data:']) ? $previews[0]->lokasi_file : asset('storage/foto/' . $previews[0]->lokasi_file) }}" class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="w-50 h-100">
                                     <img src="{{ Str::startsWith($previews[1]->lokasi_file, ['http', 'data:']) ? $previews[1]->lokasi_file : asset('storage/foto/' . $previews[1]->lokasi_file) }}" class="w-100 h-100" style="object-fit: cover;">
                                </div>
                             </div>
                        @elseif($previews->count() == 1)
                             {{-- 1 Image Full --}}
                             <img src="{{ Str::startsWith($previews[0]->lokasi_file, ['http', 'data:']) ? $previews[0]->lokasi_file : asset('storage/foto/' . $previews[0]->lokasi_file) }}" class="w-100 h-100" style="object-fit: cover;">
                        @else
                             {{-- 0 Images (Gradient Fallback) --}}
                             <div class="w-100 h-100" style="background: linear-gradient(45deg, #FF6B6B, #4ECDC4);"></div>
                        @endif
                    </div>

                    <div class="card-body bg-white text-dark d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-0 text-truncate">{{ $cat->name }}</h6>
                            <small class="text-secondary">{{ $cat->fotos_count }} Pin</small>
                        </div>
                        <i class="bi bi-arrow-right-circle fs-4 text-primary opacity-50"></i>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    {{-- Random Discovery --}}
    <h4 class="fw-bold mb-4 px-2">Mungkin Anda Suka (Acak)</h4>

    {{-- WAJIB: Tambah class 'row' agar col-* bootstrap berfungsi dengan baik --}}
    <div class="row" id="discovery-masonry-grid">
         @include('partials.photo-grid', ['foto' => $randomPhotos])
    </div>

</div>

{{-- INCLUDE MODAL DETAIL (Sama persis dengan Home) --}}
@include('components.global-modal')

@push('scripts')
<script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>

<script>
    // 1. Data untuk Modal (Dari Controller -> Variable PHP to JS)
    const photosData = @json($randomPhotos);

    // 2. Base URL - Removed (using global from main.blade.php)

    // 3. Init Masonry
    var grid = document.querySelector('#discovery-masonry-grid');
    if(grid) {
        imagesLoaded(grid, function() {
           window.masonryInstance = new Masonry(grid, {
               itemSelector: '.masonry-item',
               percentPosition: true,
               transitionDuration: '0.25s' // Restore animation
           });
        });
    }

    // 4. Logic Buka Modal
    window.openSingleModal = function(id) {
        const item = photosData.find(p => p.id === id);
        if(!item) return;

        // Populate Modal Data
        document.getElementById('modalImg').src = baseUrlFoto(item.lokasi_file);

        // Helper untuk Avatar (Handle absolute URL vs Local)
        // Definisikan root storage di sini untuk akurasi
        const storageRoot = "{{ asset('storage') }}";

        function getAvatarUrl(avatarPath) {
            if (!avatarPath) return '';
            if (avatarPath.startsWith('http') || avatarPath.startsWith('data:')) {
                return avatarPath;
            }

            // Database menyimpan 'avatars/filename.png'
            // Jadi kita hanya perlu append ke storage root
            return storageRoot + '/' + avatarPath;
        }

        // Cek elemen download sebelum assign
        const dlBtn = document.getElementById('modalDownloadBtn');
        if(dlBtn) dlBtn.href = baseUrlFoto(item.lokasi_file);

        // Elements might be optional or different in global-modal, check existence
        const titleEl = document.getElementById('modalTitle');
        if(titleEl) titleEl.innerText = item.judul_foto || '';

        const descEl = document.getElementById('modalDesc');
        if(descEl) descEl.innerText = item.deskripsi_foto || '';

        // Form Actions
        const likeForm = document.getElementById('modalLikeForm');
        if(likeForm) likeForm.action = "{{ url('albums') }}/" + item.id + "/toggle-like";

        const likeCount = document.getElementById('modalLikeCount');
        if(likeCount) likeCount.innerText = item.like ? item.like.length : 0;

        const commForm = document.getElementById('modalCommentForm');
        if(commForm) commForm.action = "{{ url('photos') }}/" + item.id + "/komentar";

        // Render User Info
        const user = item.user || {};
        const avatarContainer = document.getElementById('modalAvatarContainer');
        if(avatarContainer) {
            if(user.avatar) {
                 avatarContainer.innerHTML = `<img src="${getAvatarUrl(user.avatar)}" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">`;
            } else {
                 avatarContainer.innerHTML = `<i class="bi bi-person-circle default-avatar-icon" style="font-size: 40px;"></i>`;
            }
        }
        const usernameEl = document.getElementById('modalUsername');
        if(usernameEl) {
             usernameEl.innerText = user.username || 'Anonim';
             usernameEl.href = "{{ url('/user') }}/" + (user.username || '#');
        }

        const userLink = document.getElementById('modalUserLink');
        if(userLink) userLink.href = "{{ url('/user') }}/" + (user.username || '#');

        // Render Comments
        const listDiv = document.getElementById('modalCommentsList');
        if(listDiv) {
            listDiv.innerHTML = '';
            if (item.komentarfoto && item.komentarfoto.length > 0) {
                item.komentarfoto.forEach(c => {
                    const cUser = c.user || {};
                    let avHtml = cUser.avatar
                        ? `<img src="${getAvatarUrl(cUser.avatar)}" class="rounded-circle" width="28" height="28" style="object-fit:cover;">`
                        : `<i class="bi bi-person-circle default-avatar-icon" style="font-size: 28px;"></i>`;


                    // Delete & Edit & Report Button Logic
                    let actionBtns = '';
                    const currentUserId = {{ Auth::id() ?? 'null' }};

                    if (c.user_id == currentUserId) {
                       // Delete Button
                       actionBtns += `
                         <button type="button" onclick="deleteComment(${c.id}, this)" class="comment-delete-btn btn btn-link text-secondary p-0 ms-2" style="font-size: 14px; text-decoration: none;" title="Hapus">
                           <i class="bi bi-trash-fill"></i>
                         </button>
                       `;

                       // Check 24h Limit for Edit
                       const createdAt = new Date(c.created_at);
                       const now = new Date();
                       const diffHours = (now - createdAt) / (1000 * 60 * 60);

                       if (diffHours <= 24) {
                           actionBtns += `
                             <button type="button" onclick="editComment(${c.id}, this)" class="comment-edit-btn btn btn-link text-secondary p-0 ms-2" style="font-size: 14px; text-decoration: none;" title="Edit">
                               <i class="bi bi-pencil-fill"></i>
                             </button>
                           `;
                       }
                    } else {
                        // Report Button for others' comments
                        actionBtns += `
                             <button type="button" onclick="openReportModal('komentar', ${c.id})" class="btn btn-link text-danger p-0 ms-2" style="font-size: 14px; text-decoration: none;" title="Laporkan">
                               <i class="bi bi-flag"></i>
                             </button>
                        `;
                    }

                    listDiv.innerHTML += `
                        <div class="mb-2 d-flex gap-2 comment-item" id="comment-row-${c.id}">
                            <div class="flex-shrink-0">${avHtml}</div>
                            <div class="bg-white px-3 py-2 rounded-3 shadow-sm border w-100 comment-bubble">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="fw-bold small d-block">${cUser.username || 'Anonim'}</span>
                                    <div>${actionBtns}</div>
                                </div>
                                <div id="comment-text-${c.id}">
                                    <p class="mb-0 small text-dark lh-sm mt-1">${c.isi_komentar}</p>
                                </div>
                                <div id="comment-edit-form-${c.id}" style="display:none;" class="mt-2">
                                    <textarea class="form-control form-control-sm mb-2" id="edit-input-${c.id}">${c.isi_komentar}</textarea>
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button class="btn btn-sm btn-secondary" onclick="cancelEdit(${c.id})">Batal</button>
                                        <button class="btn btn-sm btn-primary" onclick="saveEditComment(${c.id})">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                });
            } else {
                listDiv.innerHTML = '<div class="text-center py-4 text-muted small">Belum ada komentar.</div>';
            }
        }

        // Bind Report Logic for Photo
        const reportBtn = document.getElementById('globalReportBtn');
        if(reportBtn) {
            reportBtn.onclick = function() {
                openReportModal('foto', item.id);
            };
        }

        // Show Modal
        const modalEl = document.getElementById('globalDetailModal');
        if(modalEl) {
            const myModal = new bootstrap.Modal(modalEl);
            myModal.show();
        }
    };

    // Global Edit Functions (must be attached to window)
    window.editComment = function(id) {
        document.getElementById(`comment-text-${id}`).style.display = 'none';
        document.getElementById(`comment-edit-form-${id}`).style.display = 'block';
    };

    window.cancelEdit = function(id) {
         document.getElementById(`comment-text-${id}`).style.display = 'block';
         document.getElementById(`comment-edit-form-${id}`).style.display = 'none';
    };

    window.saveEditComment = function(id) {
        const newText = document.getElementById(`edit-input-${id}`).value;

        fetch(`{{ url('komentar') }}/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ isi_komentar: newText })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(result => {
             if (result.status === 200) {
                 // Update UI
                 const pTag = document.querySelector(`#comment-text-${id} p`);
                 if(pTag) pTag.innerText = result.body.isi_komentar;

                 // Close Form
                 window.cancelEdit(id);
             } else {
                 alert(result.body.message || 'Gagal mengedit komentar.');
             }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
        });
    };

    // 5. Logic Menu Dropdown (untuk tombol titik 3)
    window.toggleMenu = function(event, menuId) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        // Tutup menu lain
        document.querySelectorAll('.pin-menu').forEach(m => {
          if (m.id !== menuId) m.classList.remove('show');
        });
        if (menu) menu.classList.toggle('show');
    };

    document.addEventListener('click', function () {
        document.querySelectorAll('.pin-menu').forEach(m => m.classList.remove('show'));
    });
</script>
@endpush
@endsection

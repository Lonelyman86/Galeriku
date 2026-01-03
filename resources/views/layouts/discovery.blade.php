@extends('main')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-5 text-center">
        <h1 class="fw-bold mb-3 display-5">Temukan Inspirasi</h1>
        <p class="text-secondary" style="max-width: 600px; margin: 0 auto;">
            Jelajahi berbagai kategori menarik atau temukan karya acak yang mungkin Anda sukai.
        </p>
    </div>

    {{-- Kategori Grid --}}
    <h4 class="fw-bold mb-4 px-2">Kategori Populer</h4>
    <div class="row px-2 g-3 mb-5">
        @foreach($categories as $cat)
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('search', ['category' => $cat->slug]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 folder-card position-relative overflow-hidden text-white" style="border-radius: 16px;">
                    {{-- Placeholder Image for Category --}}
                    <div style="height: 120px; background: linear-gradient(45deg, #FF6B6B, #4ECDC4);"></div>
                    
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
    
    // 2. Base URL
    const baseUrlFoto = "{{ asset('storage/foto') }}";
    const baseUrlAvatar = "{{ asset('storage') }}"; 
    
    // 3. Init Masonry
    var grid = document.querySelector('#discovery-masonry-grid');
    if(grid) {
        imagesLoaded(grid, function() {
           new Masonry(grid, { itemSelector: '.masonry-item', percentPosition: true });
        });
    }

    // 4. Logic Buka Modal
    window.openSingleModal = function(id) {
        const item = photosData.find(p => p.id === id);
        if(!item) return;

        // Populate Modal Data
        document.getElementById('modalImg').src = baseUrlFoto + '/' + item.lokasi_file;
        
        // Cek elemen download sebelum assign
        const dlBtn = document.getElementById('modalDownloadBtn');
        if(dlBtn) dlBtn.href = baseUrlFoto + '/' + item.lokasi_file;
        
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
                 avatarContainer.innerHTML = `<img src="${baseUrlAvatar}/${user.avatar}" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">`;
            } else {
                 avatarContainer.innerHTML = `<i class="bi bi-person-circle default-avatar-icon" style="font-size: 40px;"></i>`;
            }
        }
        const usernameEl = document.getElementById('modalUsername');
        if(usernameEl) usernameEl.innerText = user.username || 'Anonim';

        // Render Comments
        const listDiv = document.getElementById('modalCommentsList');
        if(listDiv) {
            listDiv.innerHTML = '';
            if (item.komentarfoto && item.komentarfoto.length > 0) {
                item.komentarfoto.forEach(c => {
                    const cUser = c.user || {};
                    let avHtml = cUser.avatar 
                        ? `<img src="${baseUrlAvatar}/${cUser.avatar}" class="rounded-circle" width="28" height="28" style="object-fit:cover;">`
                        : `<i class="bi bi-person-circle default-avatar-icon" style="font-size: 28px;"></i>`;
                    
                    listDiv.innerHTML += `
                        <div class="mb-2 d-flex gap-2">
                            <div class="flex-shrink-0">${avHtml}</div>
                            <div class="bg-white px-3 py-2 rounded-3 shadow-sm border w-100">
                                <span class="fw-bold small d-block">${cUser.username || 'Anonim'}</span>
                                <p class="mb-0 small text-dark lh-sm mt-1">${c.isi_komentar}</p>
                            </div>
                        </div>`;
                });
            } else {
                listDiv.innerHTML = '<div class="text-center py-4 text-muted small">Belum ada komentar.</div>';
            }
        }

        // Show Modal
        const modalEl = document.getElementById('globalDetailModal');
        if(modalEl) {
            const myModal = new bootstrap.Modal(modalEl);
            myModal.show();
        }
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

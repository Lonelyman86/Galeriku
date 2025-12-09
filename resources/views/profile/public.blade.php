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

    {{-- 2. HEADER PROFIL BARU (DENGAN TOMBOL FOLLOW) --}}
    <div class="card border-0 shadow-sm mb-5 overflow-hidden">
        <div class="card-body text-center p-5 position-relative">
        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/img/default-profile.png') }}" 
     class="rounded-circle mb-3 border border-3 border-white shadow-sm" 
     width="110" height="110" 
     style="object-fit: cover;">
            
            <h3 class="fw-bold text-dark mb-1">{{ $user->username }}</h3>
            <p class="text-muted small mb-3">{{ $user->fullname }}</p>

            {{-- TOMBOL FOLLOW / UNFOLLOW --}}
            @auth
                @if(Auth::id() !== $user->id)
                    <form action="{{ route('user.follow', $user->id) }}" method="POST" class="d-inline-block mb-3">
                        @csrf
                        @if(Auth::user()->isFollowing($user))
                            <button type="submit" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                                <i class="bi bi-person-check-fill me-1"></i> Mengikuti
                            </button>
                        @else
                            <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm">
                                <i class="bi bi-person-plus-fill me-1"></i> Ikuti
                            </button>
                        @endif
                    </form>
                @endif
            @endauth

            {{-- STATISTIK (POST, ALBUM, PENGIKUT) --}}
            <div class="d-flex justify-content-center gap-2 mt-2 flex-wrap">
                <div class="bg-light px-3 py-2 rounded-3 border">
                    <span class="d-block fw-bold text-dark">{{ $foto->total() }}</span>
                    <small class="text-muted" style="font-size: 11px;">Postingan</small>
                </div>
                <div class="bg-light px-3 py-2 rounded-3 border">
                    <span class="d-block fw-bold text-dark">{{ $albums->count() }}</span>
                    <small class="text-muted" style="font-size: 11px;">Album</small>
                </div>
                {{-- Statistik Follower --}}
                <div class="bg-light px-3 py-2 rounded-3 border">
                    <span class="d-block fw-bold text-dark">{{ $user->followers->count() }}</span>
                    <small class="text-muted" style="font-size: 11px;">Pengikut</small>
                </div>
                <div class="bg-light px-3 py-2 rounded-3 border">
                    <span class="d-block fw-bold text-dark">{{ $user->following->count() }}</span>
                    <small class="text-muted" style="font-size: 11px;">Mengikuti</small>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. BAGIAN FOTO --}}
    <h4 class="fw-bold mb-3">Semua Foto</h4>
    
    <div class="row" id="masonry-grid">
        @forelse($foto as $item)
            <div class="col-6 col-md-4 col-lg-3 mb-4 masonry-item">
                <div class="pin-wrapper">
                    {{-- Gambar Utama --}}
                    <img src="{{ asset('storage/foto/'.$item->lokasi_file) }}" alt="{{ $item->judul_foto }}"
                         data-bs-toggle="modal" data-bs-target="#DetailModal{{$item->id}}">

                    {{-- Tombol 3 Titik --}}
                    <button type="button" class="pin-menu-btn" data-menu-target="pin-menu-{{ $item->id }}"
                            onclick="toggleMenu(event, 'pin-menu-{{ $item->id }}')">
                        <i class="bi bi-three-dots"></i>
                    </button>

                    {{-- Menu Dropdown --}}
                    <div id="pin-menu-{{ $item->id }}" class="pin-menu">
                        <a href="{{ asset('storage/foto/'.$item->lokasi_file) }}" download class="pin-menu-item">
                            <i class="bi bi-download"></i> <span>Unduh gambar</span>
                        </a>
                        @if($item->album_id)
                            <a href="{{ route('album.show', $item->album_id) }}" class="pin-menu-item">
                                <i class="bi bi-journal-album"></i> Lihat Album
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- MODAL DETAIL --}}
            <div class="modal fade" id="DetailModal{{$item->id}}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                  <div class="modal-content" style="border-radius: 20px; overflow: hidden; border:none;">
                    <div class="modal-body p-0">
                      <div class="row g-0" style="min-height: 500px;">
                        {{-- Kiri: Gambar Full --}}
                        <div class="col-lg-8 bg-black d-flex align-items-center justify-content-center position-relative">
                          <div class="modal-image-wrapper">
                            <img src="{{ asset('storage/foto/'.$item->lokasi_file) }}" alt="{{ $item->judul_foto }}">
                            
                            <button type="button" class="pin-menu-btn pin-menu-btn-modal"
                                    data-menu-target="pin-menu-modal-{{ $item->id }}"
                                    onclick="toggleMenu(event, 'pin-menu-modal-{{ $item->id }}')">
                                <i class="bi bi-three-dots"></i>
                            </button>

                            <div id="pin-menu-modal-{{ $item->id }}" class="pin-menu pin-menu-modal">
                                <a href="{{ asset('storage/foto/'.$item->lokasi_file) }}" download class="pin-menu-item">
                                    <i class="bi bi-download"></i><span>Unduh gambar</span>
                                </a>
                            </div>
                          </div>
                        </div>

                        {{-- Kanan: Detail --}}
                        <div class="col-lg-4 bg-white d-flex flex-column" style="max-height: 90vh;">
                          <div class="p-3 border-bottom">
                             <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                   <h5 class="fw-bold mb-1 fs-6">{{ $item->judul_foto }}</h5>
                                   <p class="text-secondary mb-0 small">{{ $item->deskripsi_foto }}</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                             </div>
                             <small class="text-muted" style="font-size: 11px;">
                                Uploaded {{ $item->created_at->diffForHumans() }}
                             </small>
                          </div>

                          <div class="flex-grow-1 p-3 overflow-auto custom-scrollbar" style="background: #f9fafb;">
                             <div class="mb-3">
                                <form method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
                                  @csrf
                                  <button type="submit" class="btn btn-danger rounded-pill px-4 btn-sm w-100">
                                    ❤ Like <span class="ms-1 fw-bold">{{ $item->like->count() }}</span>
                                  </button>
                                </form>
                             </div>

                             <h6 class="fw-bold small text-muted mb-2">Komentar</h6>
                             @forelse($item->komentarfoto as $comment)
                                <div class="mb-2 d-flex gap-2">
                                   <div class="flex-shrink-0">
                                      <img src="{{ $comment->user->avatar ? asset('storage/avatars/'.$comment->user->avatar) : asset('assets/img/default-profile.png') }}" 
                                           class="rounded-circle" width="28" height="28" style="object-fit: cover;">
                                   </div>
                                   <div class="bg-white px-3 py-2 rounded-3 shadow-sm border w-100">
                                      <span class="fw-bold small d-block">{{ $comment->user->fullname ?? $comment->user->username }}</span>
                                      <p class="mb-0 small text-dark lh-sm mt-1">{{ $comment->isi_komentar }}</p>
                                   </div>
                                </div>
                             @empty
                                <div class="text-center py-4"><p class="text-muted small">Belum ada komentar.</p></div>
                             @endforelse
                          </div>

                          @auth
                          <div class="p-3 border-top bg-white">
                            <form method="POST" action="{{ route('komentar.store', ['photo' => $item->id]) }}">
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
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">User ini belum memposting foto.</p>
            </div>
        @endforelse
    </div>

    {{-- 4  . BAGIAN ALBUM --}}
    @if($albums->count() > 0)
        <div class="d-flex align-items-center mb-3">
            <h4 class="fw-bold mb-0">Album</h4>
            <span class="badge bg-secondary ms-2 rounded-pill">{{ $albums->count() }}</span>
        </div>

        <div class="row mb-5">
            @foreach($albums as $album)
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <a href="{{ route('album.show', $album->id) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-4 h-100 folder-card text-center position-relative overflow-hidden">
                            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-folder-fill text-warning" style="font-size: 4rem;"></i>
                                <h6 class="fw-bold text-dark mt-2 mb-1 text-truncate w-100">{{ $album->nama_album }}</h6>
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

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4 mb-5">
        {{ $foto->withQueryString()->links() }}
    </div>
</div>

{{-- SCRIPT INIT MASONRY & MENU --}}
<script>
  var grid = document.querySelector('#masonry-grid');
  if(grid) {
    imagesLoaded(grid, function() {
      new Masonry(grid, {
        itemSelector: '.masonry-item',
        percentPosition: true
      });
    });
  }

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

@endsection
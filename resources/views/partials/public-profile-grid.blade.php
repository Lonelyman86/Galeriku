@forelse($foto as $item)
  <div class="col-6 col-md-4 col-lg-3 mb-4 masonry-item">
      <div class="pin-wrapper">
          {{-- Gambar Utama --}}
          <img src="{{ Storage::url('foto/'.$item->lokasi_file) }}" alt="{{ $item->judul_foto }}"
               onclick="openSingleModal({{ $item->id }})">

          {{-- Tombol 3 Titik --}}
          <button type="button" class="pin-menu-btn" data-menu-target="pin-menu-{{ $item->id }}"
                  onclick="toggleMenu(event, 'pin-menu-{{ $item->id }}')">
              <i class="bi bi-three-dots"></i>
          </button>

          {{-- Menu Dropdown --}}
          <div id="pin-menu-{{ $item->id }}" class="pin-menu">
              <a href="{{ Storage::url('foto/'.$item->lokasi_file) }}" download class="pin-menu-item">
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
@empty
  <div class="col-12 text-center py-5">
      <p class="text-muted">User ini belum memposting foto.</p>
  </div>
@endforelse

@foreach ($foto as $item)
  <div class="col-6 col-md-4 col-lg-3 mb-4 masonry-item">
    <div class="pin-wrapper">
        {{-- Gambar Utama --}}
        <img src="{{ Str::startsWith($item->lokasi_file, ['http', 'data:']) ? $item->lokasi_file : asset('storage/foto/'.$item->lokasi_file) }}"
             alt="{{ $item->judul_foto }}"
             onclick="openSingleModal({{ $item->id }})">

        {{-- Tombol 3 Titik --}}
        <button type="button" class="pin-menu-btn" data-menu-target="pin-menu-{{ $item->id }}"
                onclick="toggleMenu(event, 'pin-menu-{{ $item->id }}')">
            <i class="bi bi-three-dots"></i>
        </button>

        {{-- Menu Dropdown --}}
        <div id="pin-menu-{{ $item->id }}" class="pin-menu">
            {{-- Download --}}
            <a href="{{ Str::startsWith($item->lokasi_file, ['http', 'data:']) ? $item->lokasi_file : asset('storage/foto/'.$item->lokasi_file) }}" download class="pin-menu-item">
                <i class="bi bi-download"></i> <span>Unduh gambar</span>
            </a>

            {{-- Remove From Album (Owner Only) --}}
            @if(isset($album) && $album->user_id === Auth::id())
              <form action="{{ route('foto.update.album', $item->id) }}" method="POST" onsubmit="return confirm('Keluarkan foto ini dari album?')">
                @csrf
                <input type="hidden" name="album_id" value="">
                <button type="submit" class="pin-menu-item text-danger w-100 text-start">
                    <i class="bi bi-x-circle"></i> <span>Hapus dari Album</span>
                </button>
              </form>
            @endif
        </div>

        {{-- Status Overlay (Optional, for Pending/Rejected) --}}
        @if($item->status == 'pending')
             <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-75 text-white small text-center">
                ⏳ Menunggu Persetujuan
             </div>
        @elseif($item->status == 'rejected')
             <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-danger bg-opacity-75 text-white small text-center">
                ❌ Ditolak
             </div>
        @endif
    </div>
  </div>
@endforeach

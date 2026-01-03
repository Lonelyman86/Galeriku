@foreach ($foto as $item)
    <div class="col-6 col-md-4 col-lg-3 mb-4 masonry-item">

        {{-- WRAPPER FOTO --}}
        <div class="pin-wrapper">
            {{-- Gambar Utama (Klik Trigger Modal JS) --}}
            <img src="{{ Str::startsWith($item->lokasi_file, 'http') ? $item->lokasi_file : asset('storage/foto/' . $item->lokasi_file) }}"
                alt="{{ $item->judul_foto }}" onclick="openSingleModal({{ $item->id }})">

            {{-- Tombol 3 Titik (Grid) --}}
            <button type="button" class="pin-menu-btn" data-menu-target="pin-menu-{{ $item->id }}"
                onclick="toggleMenu(event, 'pin-menu-{{ $item->id }}')">
                <i class="bi bi-three-dots"></i>
            </button>

            {{-- Menu Dropdown (Grid) --}}
            <div id="pin-menu-{{ $item->id }}" class="pin-menu">
                <a href="{{ Str::startsWith($item->lokasi_file, 'http') ? $item->lokasi_file : asset('storage/foto/' . $item->lokasi_file) }}"
                    download class="pin-menu-item">
                    <i class="bi bi-download"></i> <span>Unduh gambar</span>
                </a>
                <a href="{{ route('profile.public', $item->user->id ?? 0) }}" class="pin-menu-item">
                    <i class="bi bi-person"></i> Lihat Profil
                </a>
            </div>
        </div>
    </div>
@endforeach

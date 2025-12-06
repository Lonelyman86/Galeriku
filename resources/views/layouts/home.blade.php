@extends('main')

@section('content')

<style>
  /* Tambahan kecil aja biar nggak ganggu style lama */

  /* Pastikan pin bisa jadi anchor buat posisi tombol */
  .masonry .pin{
    position: relative;
  }

  /* Tombol 3 titik (muncul pas hover di grid) */
  .pin-menu-btn{
    position:absolute;
    top:8px;
    right:8px;
    width:32px;
    height:32px;
    border-radius:999px;
    border:0;
    background:rgba(0,0,0,.55);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:16px;
    opacity:0;
    transition:opacity .15s ease, transform .15s ease;
    cursor:pointer;
    z-index:10;
  }
  .pin:hover .pin-menu-btn{
    opacity:1;
    transform:translateY(-1px);
  }

  /* Panel menu kecil di bawah tombol 3 titik */
  .pin-menu{
    position:absolute;
    top:44px;
    right:8px;
    min-width:190px;
    background:#fff;
    border-radius:12px;
    box-shadow:0 10px 24px rgba(0,0,0,.18);
    padding:6px 0;
    font-size:13px;
    z-index:20;
    display:none;
  }
  .pin-menu.show{
    display:block;
  }

  .pin-menu-item{
    width:100%;
    padding:8px 14px;
    border:0;
    background:transparent;
    text-align:left;
    font-size:13px;
    color:#111;
    cursor:pointer;
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:8px;
  }
  .pin-menu-item:hover{
    background:#f3f4f6;
  }
  .pin-menu-item i{
    font-size:14px;
  }

  /* ==== VARIAN DI DALAM MODAL ==== */
  .modal-image-wrapper{
    position:relative;
    display:inline-block;
    width:100%;
  }
  .pin-menu-btn-modal{
    /* override posisi default, tapi tetap pakai style tombol yang sama */
    top:12px;
    right:12px;
    opacity:1;               /* di modal selalu kelihatan */
    transform:none;
  }
  .pin-menu-modal{
    top:46px;
    right:12px;
  }
</style>

<div class="container-fluid py-3">
  <h1 class="fs-4 mb-3">Semua</h1>

  <div class="masonry">
    @foreach ($foto as $item)
      <div class="pin">
        {{-- Gambar utama --}}
        <img
          src="{{ asset('storage/foto/'.$item->lokasi_file) }}"
          alt="{{ $item->judul_foto }}"
          loading="lazy"
          data-bs-toggle="modal"
          data-bs-target="#modalDetail-{{ $item->id }}"
        >

        {{-- Tombol 3 titik (opsi pin di grid) --}}
        <button
          type="button"
          class="pin-menu-btn"
          data-menu-target="pin-menu-{{ $item->id }}"
        >
          <i class="bi bi-three-dots"></i>
        </button>

        {{-- Menu kecil: Unduh gambar --}}
        <div id="pin-menu-{{ $item->id }}" class="pin-menu">
          <a
            href="{{ asset('storage/foto/'.$item->lokasi_file) }}"
            download
            class="pin-menu-item"
          >
            <i class="bi bi-download"></i>
            <span>Unduh gambar</span>
          </a>
        </div>
      </div>

      {{-- Modal detail --}}
      <div class="modal fade" id="modalDetail-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-body p-4">
              <div class="row g-3">
                <div class="col-md-7">
                  <div class="modal-image-wrapper">
                    <img
                      src="{{ asset('storage/foto/'.$item->lokasi_file) }}"
                      class="img-fluid rounded"
                      alt="{{ $item->judul_foto }}"
                    >

                    {{-- TOMBOL 3 TITIK DI DALAM MODAL --}}
                    <button
                      type="button"
                      class="pin-menu-btn pin-menu-btn-modal"
                      data-menu-target="pin-menu-modal-{{ $item->id }}"
                    >
                      <i class="bi bi-three-dots"></i>
                    </button>

                    {{-- MENU DI MODAL: Unduh gambar --}}
                    <div id="pin-menu-modal-{{ $item->id }}" class="pin-menu pin-menu-modal">
                      <a
                        href="{{ asset('storage/foto/'.$item->lokasi_file) }}"
                        download
                        class="pin-menu-item"
                      >
                        <i class="bi bi-download"></i>
                        <span>Unduh gambar</span>
                      </a>
                    </div>
                  </div>
                </div>

                <div class="col-md-5">
                  <h5 class="fw-bold mb-1">{{ $item->judul_foto }}</h5>
                  <p class="text-muted mb-2">{{ $item->deskripsi_foto }}</p>
                  <p class="text-muted mb-2"><small>Uploaded by: {{ $item->user->username }}</small></p>
                  
                  <div class="mb-3">
                    <form method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
                      @csrf
                      <button type="submit" class="btn btn-danger rounded-circle btn-sm">❤</button>
                      <span class="ms-2">{{ $item->like->count() }}</span>
                    </form>
                  </div>
                  
                  @auth
                  <form method="POST" action="{{ route('komentar.store', ['photo' => $item->id]) }}">
                    @csrf
                    <div class="d-flex bg-light rounded px-2 py-1 mb-3">
                      <textarea name="isi_komentar" class="form-control border-0 bg-transparent" placeholder="Tambahkan komentar..." rows="1"></textarea>
                      <button class="btn text-danger"><i class="bi bi-send"></i></button>
                    </div>
                  </form>
                  @endauth

                  <div class="mt-3">
                    @foreach($item->komentarfoto as $comment)
                      <p class="mb-1" style="font-size: 0.9rem;">
                        <b>{{ $comment->user->fullname ?? $comment->user->username }}</b>
                        <span class="text-secondary">{{ $comment->isi_komentar }}</span>
                      </p>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Pagination --}}
  <div class="d-flex justify-content-center mt-4 mb-5">
      {{ $foto->withQueryString()->links() }}
  </div>

</div>

{{-- JS kecil buat toggle menu 3 titik (grid + modal) --}}
<script>
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-menu-target]');
    const allMenus = document.querySelectorAll('.pin-menu');

    // Tutup semua menu dulu
    allMenus.forEach(m => m.classList.remove('show'));

    // Kalau klik di tombol 3 titik: toggle menu miliknya
    if (btn) {
      const id = btn.getAttribute('data-menu-target');
      const menu = document.getElementById(id);
      if (menu) {
        menu.classList.toggle('show');
      }
    }
  });
</script>

@endsection

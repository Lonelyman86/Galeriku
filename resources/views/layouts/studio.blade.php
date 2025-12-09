@extends('main')
@section('content')

<style>
  .page-title{ font-size: 22px; margin: 16px 8px 8px 16px; font-weight: 700; }
  /* ... (Style FAB dan Masonry bisa pakai yang lama atau file CSS eksternal) ... */
  
  .pin { cursor: pointer; transition: transform 0.2s; }
  .pin:hover { transform: translateY(-3px); }
  
  /* Status Overlay */
  .status-overlay{ position:absolute; inset:0; display:flex; justify-content:center; align-items:center; 
                   color:#fff; background:rgba(0,0,0,0.5); opacity:0; transition:0.3s; border-radius:16px; font-weight:bold;}
  .pin:hover .status-overlay { opacity:1; }
  .status-rejected { background:rgba(220,53,69,0.8); }
</style>

{{-- Tombol Tambah (FAB) --}}
<div class="fab-create" style="position: fixed; right: 24px; bottom: 24px; z-index: 30;">
  <button class="btn btn-danger rounded-circle p-3 shadow" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-plus-lg fs-4"></i>
  </button>
  <ul class="dropdown-menu mb-2">
    <li><a class="dropdown-item" href="/createalbum">Buat Album Baru</a></li>
    <li><a class="dropdown-item" href="/createfoto">Upload Foto Baru</a></li>
  </ul>
</div>

<h1 class="page-title">Studio Saya</h1>

<div class="masonry">
@foreach ($foto as $item)
  <div class="pin" onclick="openStudioModal({{ $item->id }})">
    <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" alt="{{ $item->judul_foto }}">

    {{-- Overlay Status --}}
    @if($item->status == 'pending')
      <div class="status-overlay">⏳ Menunggu</div>
    @elseif($item->status == 'rejected')
      <div class="status-overlay status-rejected">❌ Ditolak</div>
    @endif
    
    <div class="pin-meta p-2">
      <div class="fw-bold small">{{ $item->judul_foto }}</div>
      <div class="text-muted small" style="font-size:11px;">
        {{ $item->album ? $item->album->nama_album : 'Tanpa Album' }}
      </div>
    </div>
  </div>
@endforeach
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-center mt-4 mb-5">
    {{ $foto->withQueryString()->links() }}
</div>

{{-- List Album --}}
@if ($albums->count() > 0)
  <hr class="my-5">
  <h1 class="page-title">Album Saya</h1>
  <div class="row px-3">
    @foreach ($albums as $album)
      <div class="col-6 col-md-3 mb-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title fw-bold">{{ $album->nama_album }}</h6>
                <p class="card-text small text-muted">{{ $album->deskripsi }}</p>
                <a href="{{ route('album.show', $album->id) }}" class="stretched-link"></a>
            </div>
             <div class="card-footer bg-white border-0 text-end">
                <form action="{{ route('albums.destroy', $album->id) }}" method="POST" onsubmit="return confirm('Hapus album ini?');" style="z-index: 2; position: relative;">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-link text-danger text-decoration-none p-0">Hapus</button>
                </form>
            </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

{{-- ===============================================
     SINGLE STUDIO MODAL
   =============================================== --}}
<div class="modal fade" id="studioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title">Detail Foto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-4">
            <div class="col-md-7 text-center bg-light rounded p-3">
              <img id="studioImg" src="" class="img-fluid" style="max-height: 500px;">
            </div>
            <div class="col-md-5">
              <div id="alertStatus" class="alert alert-warning d-none py-2 small"></div>

              <h5 id="studioTitle" class="fw-bold"></h5>
              <p id="studioDesc" class="text-muted small"></p>

              <div class="d-grid gap-2 mb-3">
                  <form id="formDelete" action="" method="POST" onsubmit="return confirm('Yakin hapus foto ini?')">
                      @csrf @method('delete')
                      <button class="btn btn-outline-danger btn-sm w-100">Hapus Foto</button>
                  </form>
                  
                  <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAlbum">
                    Pindahkan Album
                  </button>
              </div>

              <div class="collapse mb-3" id="collapseAlbum">
                  <div class="card card-body p-2 bg-light border-0">
                      <form id="formAlbum" action="" method="POST">
                          @csrf
                          <label class="small text-muted mb-1">Pilih Album:</label>
                          <select name="album_id" class="form-select form-select-sm mb-2" id="selectAlbum">
                              <option value="">-- Lepas dari Album --</option>
                              @foreach($albums as $alb)
                                <option value="{{ $alb->id }}">{{ $alb->nama_album }}</option>
                              @endforeach
                          </select>
                          <button class="btn btn-primary btn-sm w-100">Simpan</button>
                      </form>
                  </div>
              </div>

              <h6 class="mt-4 border-bottom pb-2">Komentar Masuk</h6>
              <div id="studioComments" style="max-height: 200px; overflow-y: auto;"></div>

            </div>
          </div>
        </div>
      </div>
    </div>
</div>

@push('scripts')
<script>
    const studioPhotos = @json($foto->items());
    const baseUrl = "{{ asset('storage/foto') }}";
    
    // Route Templates
    const routeDelete = "{{ route('photos.destroy', '000') }}";
    const routeAlbum = "{{ route('foto.update.album', ['photo' => '000']) }}";

    function openStudioModal(id) {
        const photo = studioPhotos.find(p => p.id === id);
        if(!photo) return;

        // 1. Populate Data Dasar
        document.getElementById('studioImg').src = baseUrl + '/' + photo.lokasi_file;
        document.getElementById('studioTitle').innerText = photo.judul_foto;
        document.getElementById('studioDesc').innerText = photo.deskripsi_foto;

        // 2. Handle Status Alert
        const alertBox = document.getElementById('alertStatus');
        if(photo.status !== 'approved') {
            alertBox.classList.remove('d-none');
            alertBox.innerText = `Status: ${photo.status.toUpperCase()} ${photo.note ? '('+photo.note+')' : ''}`;
            alertBox.className = `alert py-2 small ${photo.status === 'rejected' ? 'alert-danger' : 'alert-warning'}`;
        } else {
            alertBox.classList.add('d-none');
        }

        // 3. Update Form Actions (Delete & Album)
        document.getElementById('formDelete').action = routeDelete.replace('000', id);
        document.getElementById('formAlbum').action = routeAlbum.replace('000', id);
        
        // 4. Set Selected Album
        const selectAlb = document.getElementById('selectAlbum');
        selectAlb.value = photo.album_id || "";

        // 5. Render Komentar
        const commDiv = document.getElementById('studioComments');
        commDiv.innerHTML = '';
        if(photo.komentarfoto && photo.komentarfoto.length > 0){
            photo.komentarfoto.forEach(c => {
                 const u = c.user ? (c.user.fullname || c.user.username) : 'Anonim';
                 commDiv.innerHTML += `
                    <div class="mb-1">
                        <small class="fw-bold">${u}</small>: 
                        <span class="small text-muted">${c.isi_komentar}</span>
                    </div>`;
            });
        } else {
            commDiv.innerHTML = '<small class="text-muted">Tidak ada komentar.</small>';
        }

        // 6. Show Modal
        const modal = new bootstrap.Modal(document.getElementById('studioModal'));
        modal.show();
    }
</script>
@endpush

@endsection
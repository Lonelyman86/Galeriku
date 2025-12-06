@extends('main')

@section('content')

<style>
  .page-title{
    font-family: ui-sans-serif, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans";
    font-size: 22px; margin: 16px 8px 8px 16px; font-weight: 700;
  }

  /* header judul + tombol edit */
  .album-header{
    position: relative;
  }
  .album-edit-btn{
    position: absolute;
    top: 16px;
    right: 24px;
    z-index: 25;
    font-size: 12px;
    padding: 4px 10px;
  }

  /* FAB */
  .fab-create{ position: fixed; right: 24px; bottom: 24px; z-index: 40; }
  .fab-btn{
    width:56px; height:56px; border-radius:999px; border:0; cursor:pointer;
    box-shadow:0 6px 18px rgba(0,0,0,.2); background:#ef4444; color:#fff; font-size:24px;
    transition: transform 0.2s;
  }
  .fab-btn:hover { transform: scale(1.1); }

  .masonry{
    column-count: 5;
    column-gap: 16px;
    padding: 8px 16px 64px;
  }
  @media (max-width:1200px){ .masonry{ column-count:4; } }
  @media (max-width:992px){ .masonry{ column-count:3; } }
  @media (max-width:768px){ .masonry{ column-count:2; } }
  @media (max-width:480px){ .masonry{ column-count:1; } }

  .pin{
    display:inline-block; width:100%; margin:0 0 16px; break-inside:avoid;
    position:relative; border-radius:16px; overflow:hidden;
    background:#fff; box-shadow:0 1px 0 rgba(0,0,0,.04);
    transition: transform .2s ease;
  }
  .pin:hover{ transform:translateY(-3px); }
  .pin img{ width:100%; display:block; object-fit:cover; background:#f3f4f6; }

  .pin-overlay{
    position:absolute; inset:0; display:flex; flex-direction:column; justify-content:space-between;
    padding:8px; opacity:0; transition:opacity .15s ease;
    pointer-events:none; background:linear-gradient(to bottom, rgba(0,0,0,.08), rgba(0,0,0,.35));
  }
  .pin:hover .pin-overlay{ opacity:1; }
  .pin-actions{ display:flex; gap:8px; justify-content:flex-end; }
  .pin-actions form, .pin-actions button{ pointer-events:auto; }

  .btn-ghost, .btn-danger, .btn-primary{
    border:0; border-radius:999px; padding:8px 12px; font-size:13px; cursor:pointer;
    background:rgba(255,255,255,.9);
  }
  .btn-danger{ background:#ef4444; color:#fff; }
  .btn-primary{ background:#2563eb; color:#fff; }
  .btn-ghost{ background:rgba(255,255,255,.85); }
  .btn-ghost:hover{ background:#fff; }
  .btn-danger:hover, .btn-primary:hover{ filter:brightness(.95); }

  .pin-meta{
    padding:10px 12px 12px; display:flex; flex-direction:column; gap:4px;
    font-size:13px; color:#111; background:#fff;
  }
  .pin-title{ font-weight:700; line-height:1.25; }
  .pin-desc{ color:#6b7280; font-size:12px; line-height:1.3; }
  .pin-sub{ color:#60a5fa; font-size:12px; }

  .status-overlay{
    position:absolute; inset:0;
    display:flex; flex-direction:column; justify-content:center; align-items:center;
    font-weight:600; text-align:center; font-size:14px;
    color:#fff; opacity:0; border-radius:16px;
    transition:opacity .3s ease; padding:10px;
  }
  .pin:hover .status-overlay{ opacity:1; }
  .status-pending{ background:rgba(255,193,7,0.75); }
  .status-rejected{ background:rgba(220,53,69,0.75); }
  .detail-foto{ max-width:100%; height:auto; }

  .img-checkbox-container { position: relative; cursor: pointer; display: block; }
  .img-checkbox-container input[type="checkbox"] {
    position: absolute; top: 10px; right: 10px; z-index: 10;
    width: 20px; height: 20px; cursor: pointer;
  }
  .img-checkbox-container img {
    border: 3px solid transparent; transition: 0.2s; border-radius: 8px;
    width: 100%; height: 120px; object-fit: cover;
  }
  .img-checkbox-container input:checked + img {
    border-color: #2563eb; opacity: 0.8; transform: scale(0.95);
  }
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

<div class="album-header">
  <h1 class="page-title">{{ $album->nama_album }}</h1>

  @if($album->user_id == Auth::id())
    <button class="btn btn-outline-secondary btn-sm album-edit-btn"
            data-bs-toggle="modal"
            data-bs-target="#editAlbumModal">
      Edit
    </button>
  @endif
</div>

<div class="masonry">
@foreach ($foto as $item)
  <div class="pin">
    <a data-bs-toggle="modal" data-bs-target="#ContohModal{{$item->id}}">
      <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" alt="{{ $item->judul_foto }}">
    </a>

    @if($item->status == 'pending')
      <div class="status-overlay status-pending">⏳ Menunggu Persetujuan Admin</div>
    @elseif($item->status == 'rejected')
      <div class="status-overlay status-rejected">
        ❌ Foto Ditolak<br><small>{{ $item->note ?? 'Tanpa alasan' }}</small>
      </div>
    @endif

    <div class="pin-overlay">
      <div class="pin-actions">
        @if($item->status === 'approved')
          <form method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
            @csrf
            <button type="submit" class="btn-ghost">❤ {{ $item->like->count() }}</button>
          </form>
        @else
          <button class="btn-ghost" disabled style="opacity:.5;">❤ {{ $item->like->count() }}</button>
        @endif

        @if($album->user_id === Auth::id())
          <form action="{{ route('foto.update.album', $item->id) }}"
                method="POST"
                onsubmit="return confirm('Keluarkan foto ini dari album?')">
            @csrf
            <input type="hidden" name="album_id" value="">
            <button class="btn-primary" style="background: rgba(0,0,0,0.5); color:white;">✕</button>
          </form>
        @endif
      </div>
    </div>

    <div class="pin-meta">
      <div class="pin-title">{{ $item->judul_foto }}</div>
      @if(!empty($item->deskripsi_foto))
        <div class="pin-desc">{{ $item->deskripsi_foto }}</div>
      @endif
      <div class="pin-sub">{{ optional($item->album)->nama_album ?? 'Tidak Ada Album' }}</div>
      <div style="font-size:12px; color:#374151;"><b>{{ $item->user->username }}</b></div>
    </div>
  </div>

  {{-- Modal Detail --}}
  <div class="modal fade" id="ContohModal{{$item->id}}" tabindex="-1">
    <div class="modal-dialog" style="max-width:920px;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Post</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-7">
              <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" class="detail-foto">
            </div>
            <div class="col-md-5">
              @if($item->status == 'pending')
                <div class="alert alert-warning p-2">
                  <strong>Status:</strong> Menunggu Persetujuan Admin
                </div>
              @elseif($item->status == 'rejected')
                <div class="alert alert-danger p-2">
                  <strong>Status:</strong> Ditolak<br>
                  <small>Alasan: {{ $item->note ?? 'Tidak ada alasan' }}</small>
                </div>
              @endif

              @if($item->status == 'approved')
                <form method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-sm">❤</button>
                  <span>{{ $item->like->count() }}</span>
                </form>
                <form method="POST" action="{{ route('komentar.store', ['photo' => $item->id]) }}">
                  @csrf
                  <div class="input-group mt-2">
                    <textarea name="isi_komentar" rows="1" placeholder="Add a comment" class="form-control"></textarea>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                  </div>
                </form>
              @else
                <button type="button" class="btn btn-danger btn-sm mt-1" disabled style="opacity:0.6;">
                  ❤ {{ $item->like->count() }}
                </button>
                <textarea rows="1" class="form-control mt-2" placeholder="Komentar nonaktif" readonly></textarea>
              @endif

              <div class="list-group list-group-flush mt-3" style="max-height: 300px; overflow-y:auto;">
                @forelse ($item->komentarfoto as $comment)
                  <div class="list-group-item">
                    <h6>
                      <strong>{{ $comment->user->fullname ?? $comment->user->username }}</strong>
                      <span class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</span>
                    </h6>
                    <p class="mb-0">{{ $comment->isi_komentar }}</p>
                  </div>
                @empty
                  <p class="mt-3 text-muted">Belum ada komentar.</p>
                @endforelse
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endforeach
</div>

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
                  <img src="{{ asset('storage/foto/'.$fotoItem->lokasi_file) }}"
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
  <div class="modal-dialog">
    <form method="POST" action="{{ route('albums.update', $album->id) }}" class="modal-content">
      @csrf
      @method('PATCH')

      <div class="modal-header">
        <h5 class="modal-title">Edit Album</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama album</label>
          <input type="text"
                 name="nama_album"
                 class="form-control @error('nama_album') is-invalid @enderror"
                 value="{{ old('nama_album', $album->nama_album) }}"
                 required>
          @error('nama_album')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="deskripsi"
                    class="form-control @error('deskripsi') is-invalid @enderror"
                    rows="3"
                    required>{{ old('deskripsi', $album->deskripsi) }}</textarea>
          @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan perubahan</button>
      </div>
    </form>
  </div>
</div>
@endif

<div class="d-flex justify-content-center mt-4 mb-5">
  {{ $foto->withQueryString()->links() }}
</div>

@endsection

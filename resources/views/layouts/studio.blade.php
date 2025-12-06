@extends('main')
@section('content')

<style>
  .page-title{
    font-family: ui-sans-serif, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans";
    font-size: 22px; margin: 16px 8px 8px 16px; font-weight: 700;
  }

  .fab-create{ position: fixed; right: 24px; bottom: 24px; z-index: 30; }
  .fab-btn{
    width:56px; height:56px; border-radius:999px; border:0; cursor:pointer;
    box-shadow:0 6px 18px rgba(0,0,0,.2); background:#ef4444; color:#fff; font-size:24px;
  }
  .fab-create:hover .fab-menu{ opacity:1; visibility:visible; transform:translateY(0); }
  .fab-menu{
    position:absolute; right:0; bottom:70px; background:#fff; border-radius:12px; padding:8px;
    box-shadow:0 10px 24px rgba(0,0,0,.18); display:flex; flex-direction:column; gap:6px;
    opacity:0; visibility:hidden; transform:translateY(8px); transition:all .18s ease;
  }
  .fab-menu a{ padding:8px 12px; border-radius:8px; text-decoration:none; color:#111; font-size:14px; white-space:nowrap; }
  .fab-menu a:hover{ background:#f3f4f6; }

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
    display:inline-block; width:100%; margin:0 0 16px; break-inside:avoid; position:relative;
    border-radius:16px; overflow:hidden; background:#fff; box-shadow:0 1px 0 rgba(0,0,0,.04);
    transition: transform .2s ease;
  }
  .pin:hover{ transform:translateY(-3px); }
  .pin img{ width:100%; height:auto; display:block; object-fit:cover; background:#f3f4f6; }

  .pin:hover .pin-overlay{ opacity:1; }
  .pin-overlay{
    position:absolute; inset:0; display:flex; flex-direction:column; justify-content:space-between;
    padding:8px; opacity:0; transition:opacity .15s ease; pointer-events:none;
    background:linear-gradient(to bottom, rgba(0,0,0,.08), rgba(0,0,0,.35));
  }
  .pin-actions{ display:flex; gap:8px; justify-content:flex-end; }
  .pin-actions form, .pin-actions button{ pointer-events:auto; }

  .btn-ghost, .btn-danger, .btn-primary{ border:0; border-radius:999px; padding:8px 12px; font-size:13px; cursor:pointer; }
  .btn-danger{ background:#ef4444; color:#fff; }
  .btn-primary{ background:#2563eb; color:#fff; }
  .btn-ghost{ background:rgba(255,255,255,.85); }
  .btn-ghost:hover{ background:#fff; }

  .pin-meta{ padding:10px 12px 12px; display:flex; flex-direction:column; gap:4px; font-size:13px; color:#111; background:#fff; }
  .pin-title{ font-weight:700; line-height:1.25; }
  .pin-desc{ color:#6b7280; font-size:12px; line-height:1.3; }
  .pin-sub{ color:#60a5fa; font-size:12px; }

  .status-overlay{ position:absolute; inset:0; display:flex; flex-direction:column; justify-content:center; align-items:center; font-weight:600; text-align:center; font-size:14px; color:#fff; opacity:0; border-radius:16px; transition:opacity .3s ease; padding:10px; }
  .pin:hover .status-overlay{ opacity:1; }
  .status-pending{ background:rgba(255,193,7,0.75); }
  .status-rejected{ background:rgba(220,53,69,0.75); }
  .disabled-interaction{ opacity:0.5; pointer-events:none; }
  .detail-foto{ max-width:100%; height:auto; }
  
  .album-wrap{ display:grid; grid-template-columns:repeat(auto-fill, minmax(220px,1fr)); gap:16px; padding:8px 16px 24px; }
  .album-card{ display:block; padding:14px; border-radius:14px; background:#fff; text-decoration:none; color:#111; box-shadow:0 1px 0 rgba(0,0,0,.04); }
  .album-card:hover{ background:#f8fafc; }
  .text-judul{ font-weight:700; margin:0 0 4px; }
  .text-dalem-01{ margin:0; font-size:12px; color:#6b7280; }
</style>

<div class="fab-create">
  <button class="fab-btn">＋</button>
  <div class="fab-menu">
    <a href="/createalbum">Create New Album</a>
    <a href="/createfoto">Create New Foto</a>
  </div>
</div>

<h1 class="page-title">Studio Saya</h1>

<div class="masonry">
@foreach ($foto as $item)
  <div class="pin">
    <a data-bs-toggle="modal" data-bs-target="#ContohModal{{$item->id}}">
      <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" alt="{{ $item->judul_foto }}">
    </a>

    {{-- Overlay status (hover) --}}
    @if($item->status == 'pending')
      <div class="status-overlay status-pending">⏳ Menunggu Persetujuan</div>
    @elseif($item->status == 'rejected')
      <div class="status-overlay status-rejected">❌ Foto Ditolak<br><small>{{ $item->note }}</small></div>
    @endif

    {{-- Tombol di hover --}}
    <div class="pin-overlay">
      <div class="pin-actions">
        {{-- LIKE: tetap cuma aktif kalau approved --}}
        @if($item->status == 'approved')
          <form method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
            @csrf
            <button type="submit" class="btn-ghost">
              ❤ {{ $item->like->count() }}
            </button>
          </form>
        @else
          <button class="btn-ghost" disabled style="opacity:0.6;">
            ❤ {{ $item->like->count() }}
          </button>
        @endif

        {{-- DELETE --}}
        @auth
          @if(Auth::id() == $item->user_id)
            <form action="{{ route('photos.destroy', $item->id) }}" method="POST"
                  onsubmit="return confirm('Hapus foto ini?')">
              @csrf
              @method('delete')
              <button type="submit" class="btn-danger">Delete</button>
            </form>
          @endif
        @endauth

        {{-- TOMBOL ALBUM --}}
        @if($item->status == 'approved')
        <button type="button"
              class="btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#Albummodal{{ $item->id }}">
          Album
        </button>
        @else
          <button class="btn-ghost" disabled style="opacity:0.6;">
            Album
          </button>
        @endif
      </div>
    </div>

    <div class="pin-meta">
      <div class="pin-title">{{ $item->judul_foto }}</div>
      <div class="pin-desc">{{ Str::limit($item->deskripsi_foto, 50) }}</div>
      <div class="pin-sub">{{ $item->album->nama_album ?? 'Tanpa Album' }}</div>
    </div>
  </div>

  {{-- Modal Detail --}}
  <div class="modal fade" id="ContohModal{{$item->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 920px;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Post</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-7">
              <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" class="detail-foto" />
            </div>
            <div class="col-md-5">
              @if($item->status != 'approved')
                <div class="alert alert-warning">Status: {{ ucfirst($item->status) }}</div>
              @endif

              <div class="{{ $item->status != 'approved' ? 'disabled-interaction' : '' }}">
                <div class="mb-3">
                   <form method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
                      @csrf
                      <button class="btn btn-danger btn-sm">❤ Like</button>
                      <span class="ms-1 fw-bold">{{ $item->like->count() }} Likes</span>
                   </form>
                </div>

                <form method="POST" action="{{ route('komentar.store', ['photo' => $item->id]) }}">
                  @csrf
                  <div class="input-group mb-3">
                    <input type="text" name="isi_komentar" placeholder="Tulis komentar..." class="form-control">
                    <button class="btn btn-primary">Kirim</button>
                  </div>
                </form>

                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                  @foreach ($item->komentarfoto as $comment)
                    <div class="list-group-item px-0">
                      <strong>{{ $comment->user->fullname ?? $comment->user->username }}</strong> 
                      <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                      <p class="mb-0">{{ $comment->isi_komentar }}</p>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL ALBUM --}}
  <div class="modal fade" id="Albummodal{{$item->id}}" tabindex="-1">
    <div class="modal-dialog" style="max-width: 680px;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pindahkan ke Album Lain</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('foto.update.album', ['photo' => $item->id]) }}">
            @csrf
            <select name="album_id" class="form-control">
              {{-- Perbaikan: gunakan variabel $albums, bukan $albumOption --}}
              @foreach($albums as $alb)
                <option value="{{ $alb->id }}" {{ $item->album_id == $alb->id ? 'selected' : '' }}>
                   {{ $alb->nama_album }}
                </option>
              @endforeach
            </select>
            <button type="submit" class="btn btn-primary mt-2">Simpan</button>
          </form>
        </div>
      </div>
    </div>
  </div>

@endforeach
</div>

{{-- Pagination Links --}}
<div class="d-flex justify-content-center mt-4 mb-5">
    {{ $foto->withQueryString()->links() }}
</div>

{{-- Section List Album --}}
@if ($albums->count() > 0)
  <h1 class="page-title">Album</h1>
  <div class="album-wrap">
    @foreach ($albums as $album)
      <div style="position: relative;">
        <a href="{{ route('album.show', ['album' => $album->id]) }}" class="album-card">
          <p class="text-judul">{{ $album->nama_album }}</p>
          <p class="text-dalem-01">{{ $album->deskripsi }}</p>
        </a>
        <form action="{{ route('albums.destroy', $album->id) }}" method="POST" onsubmit="return confirm('Hapus album?');" style="position:absolute; top:10px; right:12px;">
             @csrf @method('DELETE')
             <button type="submit" style="border:0; padding:4px 8px; font-size:11px; border-radius:999px; background:#ef4444; color:#fff;">Hapus</button>
        </form>
      </div>
    @endforeach
  </div>
@endif
@endsection
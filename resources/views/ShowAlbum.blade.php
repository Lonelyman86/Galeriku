@extends('main')

@section('content')

@if($album->user_id == Auth::user()->id)
<div class="fab-create">
  <button class="fab-btn">＋</button>
  <div class="fab-menu">
    <a href="/createalbum">Create New Album</a>
    <a href="/createfoto">Create New Foto</a>
  </div>
</div>
@endif  

<h1 class="page-title">{{ $album->nama_album }}</h1>

<style>
  .page-title{
    font-family: ui-sans-serif, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans";
    font-size: 22px; margin: 16px 8px 8px 16px; font-weight: 700;
  }

  /* FAB */
  .fab-create{ position: fixed; right: 24px; bottom: 24px; z-index: 40; }
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

  /* Masonry layout */
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
</style>

<div class="masonry">
@foreach ($foto as $item)
  <div class="pin">
    <a data-bs-toggle="modal" data-bs-target="#ContohModal{{$item->id}}">
      <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" alt="{{ $item->judul_foto }}">
    </a>

    {{-- Overlay status --}}
    @if($item->status == 'pending')
      <div class="status-overlay status-pending">
        ⏳ Menunggu Persetujuan Admin
      </div>
    @elseif($item->status == 'rejected')
      <div class="status-overlay status-rejected">
        ❌ Foto Ditolak<br><small>{{ $item->note ?? 'Tanpa alasan' }}</small>
      </div>
    @endif

    <div class="pin-overlay">
      <div class="pin-actions">
        {{-- LIKE --}}
        @if($item->status == 'approved')
          <form method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
            @csrf
            <button type="submit" class="btn-ghost">❤ {{ $item->like->count() }}</button>
          </form>
        @else
          <button type="button" class="btn-ghost" style="opacity:0.6; cursor:not-allowed;" disabled>
            ❤ {{ $item->like->count() }}
          </button>
          {{-- ALBUM --}}
        @if($album->user_id == Auth::user()->id)
        <button type="button" class="btn-primary" data-bs-toggle="modal" data-bs-target="#Albummodal{{$item->id}}" style="opacity:0.6; cursor:not-allowed;" disabled>
          Album
        </button>
        @endif
        @endif

        
      </div>
    </div>

    <div class="pin-meta">
      <div class="pin-title">{{ $item->judul_foto }}</div>
      @if(!empty($item->deskripsi_foto))
        <div class="pin-desc">{{ $item->deskripsi_foto }}</div>
      @endif
      <div class="pin-sub">{{ optional($item->album)->nama_album ?? 'Tidak Ada Album' }}</div>
      <div style="font-size:12px; color:#374151;">
        <b>{{ $item->user->username }}</b>
      </div>
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

              @if ($item->komentarfoto->count() > 0)
                <div class="list-group list-group-flush mt-3">
                  @foreach ($item->komentarfoto as $comment)
                    <div class="list-group-item">
                      <h6><strong>{{ $comment->user->fullname }}</strong>
                        <span class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</span></h6>
                      <p class="mb-0">{{ $comment->isi_komentar }}</p>
                    </div>
                  @endforeach
                </div>
              @else
                <p class="mt-3 text-muted">Belum ada komentar.</p>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Album --}}
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
              @foreach($albumOption as $albums)
                <option value="{{ $albums->id }}">{{ $albums->nama_album }}</option>
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
@endsection

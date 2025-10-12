@extends('main')

@section('content')

{{-- FAB "Create New" ala Pinterest --}}
<div class="fab-create">
  <button class="fab-btn">＋</button>
  <div class="fab-menu">
    <a href="/createalbum">Create New Album</a>
    <a href="/createfoto">Create New Foto</a>
  </div>
</div>

<h1 class="page-title">Semua</h1>

<style>
  /* ====== RESET KECIL ====== */
  .page-title{
    font-family: ui-sans-serif, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans";
    font-size: 22px; margin: 16px 8px 8px 16px; font-weight: 700;
  }

  /* ====== FAB Create New ====== */
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

  /* ====== Masonry ala Pinterest (CSS Columns) ====== */
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
  }
  .pin img{
    width:100%; height:auto; display:block; object-fit:cover;
    background:#f3f4f6;
  }
  .pin:hover .pin-overlay{ opacity:1; }
  .pin-overlay{
    position:absolute; inset:0; display:flex; flex-direction:column; justify-content:space-between;
    padding:8px; opacity:0; transition:opacity .15s ease; pointer-events:none;
    background:linear-gradient(to bottom, rgba(0,0,0,.08), rgba(0,0,0,.35));
  }
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
  .btn-danger:hover{ filter:brightness(.95); }
  .btn-primary:hover{ filter:brightness(.95); }

  .pin-meta{
    padding:10px 12px 12px; display:flex; flex-direction:column; gap:4px;
    font-size:13px; color:#111;
    background:#fff;
  }
  .pin-title{ font-weight:700; line-height:1.25; }
  .pin-desc{ color:#6b7280; font-size:12px; line-height:1.3; }
  .pin-sub{ color:#60a5fa; font-size:12px; }

  /* Modal gambar responsif */
  .detail-foto{ max-width:100%; height:auto; }

  /* Album grid */
  .album-wrap{
    display:grid; grid-template-columns:repeat(auto-fill, minmax(220px,1fr));
    gap:16px; padding:8px 16px 24px;
  }
  .album-card{
    display:block; padding:14px; border-radius:14px; background:#fff; text-decoration:none; color:#111;
    box-shadow:0 1px 0 rgba(0,0,0,.04);
  }
  .album-card:hover{ background:#f8fafc; }
  .text-judul{ font-weight:700; margin:0 0 4px; }
  .text-dalem-01{ margin:0; font-size:12px; color:#6b7280; }
</style>

{{-- ====== LIST FOTO (Masonry) ====== --}}
<div class="masonry">
  @foreach ($foto as $item)
    <div class="pin">
      {{-- Gambar: klik buka modal detail --}}
      <a data-bs-toggle="modal" data-bs-target="#ContohModal{{$item->id}}">
        <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" alt="{{ $item->judul_foto }}">
      </a>

      {{-- Overlay tombol saat hover --}}
      <div class="pin-overlay">
        <div class="pin-actions">
          {{-- LIKE --}}
          <form id="like-form-{{$item->id}}" method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
            @csrf
            <button type="submit" class="btn-ghost">❤ {{ $item->like->count() }}</button>
          </form>

          @auth
            @if(Auth::id() == $item->user_id)
              {{-- DELETE --}}
              <form action="{{ route('photos.destroy', $item->id)}}" method="POST" onsubmit="return confirm('Yakin hapus foto ini?')">
                @csrf @method('delete')
                <button type="submit" class="btn-danger">Delete</button>
              </form>
            @endif
          @endauth

          {{-- MASUKKAN KE ALBUM --}}
          <button type="button" class="btn-primary" data-bs-toggle="modal" data-bs-target="#Albummodal{{$item->id}}">
            Album
          </button>
        </div>
      </div>

      {{-- Meta bawah kartu --}}
      <div class="pin-meta">
        <div class="pin-title">{{ $item->judul_foto }}</div>
        @if(!empty($item->deskripsi_foto))
          <div class="pin-desc">{{ $item->deskripsi_foto }}</div>
        @endif
        <div class="pin-sub">
          @if($item->album)
            {{ $item->album->nama_album }}
          @else
            Didn't have an album
          @endif
        </div>
        <div style="font-size:12px; color:#374151;">
          @if(isset($item->user))
            <b>@auth {{ $item->user->username }} @endauth</b>
          @endif
        </div>
      </div>
    </div>

    {{-- ===== Modal Detail Post (tetap sama, hanya style kecil) ===== --}}
    <div class="modal fade" id="ContohModal{{$item->id}}" tabindex="-1" aria-labelledby="contohModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="max-width: 920px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="contohModalLabel">Detail Post</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-7">
                <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" class="detail-foto" />
              </div>
              <div class="col-md-5">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <form id="like-form-modal-{{$item->id}}" method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">❤</button>
                    <span>{{ $item->like->count() }}</span>
                  </form>
                </div>

                <form class="card-footer-01" method="POST" action="{{ route('komentar.store', ['photo' => $item->id]) }}">
                  @csrf
                  <div class="input-group">
                    <textarea name="isi_komentar" rows="1" placeholder="Add a comment" class="form-control"></textarea>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                  </div>
                </form>

                @if ($item->komentarfoto->count() > 0)
                  <div class="list-group list-group-flush mt-3">
                    @foreach ($item->komentarfoto as $comment)
                      <div class="list-group-item">
                        <h6 class="list-group-item-heading">
                          <strong>{{ $comment->user->fullname }}</strong>
                          <span class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</span>
                        </h6>
                        <p class="list-group-item-text mb-0">{{ $comment->isi_komentar }}</p>
                      </div>
                    @endforeach
                  </div>
                @else
                  <p class="mt-3 mb-0">Tidak ada komentar.</p>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ===== Modal Pilih Album (tidak diubah selain lebar) ===== --}}
    <div class="modal fade" id="Albummodal{{$item->id}}" tabindex="-1" aria-labelledby="AlbummodalLabel" aria-hidden="true">
      <div class="modal-dialog" style="max-width: 680px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Masukan ke dalam Album</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="{{ route('foto.update.album', ['photo' => $item->id]) }}">
              @csrf
              <select name="album_id" class="form-control">
                @foreach($albums as $album)
                  <option value="{{ $album->id }}">{{ $album->nama_album }}</option>
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

{{-- ====== LIST ALBUM (opsional di bawah) ====== --}}
@if ($albums->count() > 0)
  <h1 class="page-title">Album</h1>
  <div class="album-wrap">
    @foreach ($albums as $album)
      <a href="{{ route('album.show', ['album' => $album->id]) }}" class="album-card">
        <p class="text-judul">{{ $album->nama_album }}</p>
        <p class="text-dalem-01">{{ $album->deskripsi }}</p>
      </a>
    @endforeach
  </div>
@else
  <p style="margin: 16px; color: gray;">Anda belum memiliki album.</p>
@endif

@endsection

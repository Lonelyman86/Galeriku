@extends('main')

@section('content')
<div class="container-fluid py-3">
  <h1 class="fs-4 mb-3">Semua</h1>

  <div class="masonry">
    @foreach ($foto as $item)
      <div class="pin">
        <img src="{{ asset('storage/foto/'.$item->lokasi_file) }}" alt="{{ $item->judul_foto }}" loading="lazy" data-bs-toggle="modal" data-bs-target="#modalDetail-{{ $item->id }}">
      </div>

      <!-- Modal Detail -->
      <div class="modal fade" id="modalDetail-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-body p-4">
              <div class="row g-3">
                <div class="col-md-7">
                  <img src="{{ asset('storage/foto/'.$item->lokasi_file) }}" class="img-fluid rounded" alt="{{ $item->judul_foto }}">
                </div>
                <div class="col-md-5">
                  <h5 class="fw-bold mb-1">{{ $item->judul_foto }}</h5>
                  <p class="text-muted mb-2">{{ $item->deskripsi_foto }}</p>
                  <div class="mb-3">
                    <form method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
                      @csrf
                      <button type="submit" class="btn btn-danger rounded-circle">❤</button>
                      <span class="ms-2">{{ $item->like->count() }}</span>
                    </form>
                  </div>
                  @auth
                  <form method="POST" action="{{ route('komentar.store', ['photo' => $item->id]) }}">
                    @csrf
                    <div class="d-flex bg-light rounded px-2 py-1">
                      <textarea name="isi_komentar" class="form-control border-0 bg-transparent" placeholder="Tambahkan komentar..." rows="1"></textarea>
                      <button class="btn text-danger"><i class="bi bi-send"></i></button>
                    </div>
                  </form>
                  @endauth

                  <div class="mt-3">
                    @foreach($item->komentarfoto as $comment)
                      <p class="mb-1"><b>{{ $comment->user->username }}</b> {{ $comment->isi_komentar }}</p>
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
</div>
@endsection

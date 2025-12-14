@extends('main')
@section('content')

<div class="container-fluid py-4">
  <h1 class="fs-4 mb-4 fw-bold px-2">Foto Yang Disukai</h1>

  <div class="masonry">
  @foreach ($foto as $item)
    <div class="pin">
      <a data-bs-toggle="modal" data-bs-target="#Detail{{$item->id}}">
        <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" alt="{{ $item->judul_foto }}">
      </a>
      
      <div class="pin-meta">
        <div style="font-weight:700">{{ $item->judul_foto }}</div>
        <div style="font-size:12px; color:gray">{{ $item->user->username }}</div>
        <div style="margin-top:5px;">
             <form method="POST" action="{{ route('likes.toggle', ['photo' => $item->id]) }}">
                @csrf
                <button class="btn btn-sm btn-danger rounded-pill">❤ {{ $item->like->count() }}</button>
             </form>
        </div>
      </div>
    </div>

    {{-- Modal Detail Sederhana --}}
    <div class="modal fade" id="Detail{{$item->id}}" tabindex="-1">
       <div class="modal-dialog modal-lg">
          <div class="modal-content">
             <div class="modal-body row">
                 <div class="col-md-7">
                    <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" class="w-100 rounded">
                 </div>
                 <div class="col-md-5">
                    <h5>{{ $item->judul_foto }}</h5>
                    <p>{{ $item->deskripsi_foto }}</p>
                 </div>
             </div>
          </div>
       </div>
    </div>
  @endforeach
</div>

{{-- [BARU] Pagination Links --}}
<div class="d-flex justify-content-center mt-4 mb-5">
    {{ $foto->withQueryString()->links() }}
</div>
</div>

@endsection
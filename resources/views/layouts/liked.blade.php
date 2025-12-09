@extends('main')
@section('content')

<h1 class="page-title">Foto Yang Disukai</h1>

<style>
  .page-title{ font-family: ui-sans-serif, system-ui; font-size: 22px; margin: 16px 8px; font-weight: 700; }
  .masonry{ column-count: 5; column-gap: 16px; padding: 8px 16px 64px; }
  @media (max-width:1200px){ .masonry{ column-count:4; } }
  @media (max-width:992px){ .masonry{ column-count:3; } }
  @media (max-width:768px){ .masonry{ column-count:2; } }
  @media (max-width:480px){ .masonry{ column-count:1; } }

  .pin{ display:inline-block; width:100%; margin:0 0 16px; position:relative; border-radius:16px; overflow:hidden; background:#fff; box-shadow:0 1px 0 rgba(0,0,0,.04); }
  .pin img{ width:100%; height:auto; display:block; object-fit:cover; }
  .pin-meta{ padding:10px; }
</style>

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

@endsection
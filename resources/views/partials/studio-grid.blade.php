@foreach ($foto as $item)
  <div class="pin masonry-item position-relative" style="cursor: pointer;">
    <a data-bs-toggle="modal" data-bs-target="#studioModal{{$item->id}}">
        <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" alt="{{ $item->judul_foto }}">
    </a>

    {{-- Overlay Status --}}
    @if($item->status == 'pending')
      <div class="status-overlay text-white fw-bold d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.5); pointer-events: none;">
        ⏳ Menunggu
      </div>
    @elseif($item->status == 'rejected')
      <div class="status-overlay text-white fw-bold d-flex justify-content-center align-items-center" style="background: rgba(220,53,69,0.8); pointer-events: none;">
        ❌ Ditolak
      </div>
    @endif
    
    <div class="pin-meta p-2">
      <div class="fw-bold small">{{ $item->judul_foto }}</div>
      <div class="text-muted small" style="font-size:11px;">
        {{ $item->album ? $item->album->nama_album : 'Tanpa Album' }}
      </div>
    </div>
  </div>

  {{-- MODAL DETAIL PER ITEM --}}
  <div class="modal fade" id="studioModal{{$item->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content" style="border-radius: 20px; overflow: hidden; border:none;">
        <div class="modal-body p-0">
          <div class="row g-0" style="min-height: 500px;">
            
            {{-- Kiri: Gambar Full --}}
            <div class="col-lg-8 bg-light d-flex align-items-center justify-content-center">
              <div class="p-4" style="width:100%; height:100%; display:flex; justify-content:center; align-items:center;">
                 <img src="{{ asset('storage/foto/'.$item->lokasi_file)}}" style="max-height: 85vh; max-width:100%; object-fit: contain; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
              </div>
            </div>

            {{-- Kanan: Detail --}}
            <div class="col-lg-4 bg-white d-flex flex-column" style="max-height: 90vh;">
              
              {{-- Header --}}
              <div class="p-3 border-bottom relative">
                 <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                 
                  <div class="d-flex align-items-center mb-3">
                    @if($item->user->avatar)
                        <img src="{{ asset('storage/'.$item->user->avatar) }}" 
                             class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                    @else
                        <i class="bi bi-person-circle me-2 default-avatar-icon" style="font-size: 40px;"></i>
                    @endif
                    <div>
                        <div class="fw-bold text-dark">{{ $item->user->fullname ?? $item->user->username }}</div>
                        <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                    </div>
                 </div>

                 <h5 class="fw-bold mb-1">{{ $item->judul_foto }}</h5>
                 <p class="text-secondary small mb-2">{{ $item->deskripsi_foto }}</p>

                 {{-- Status Alert --}}
                 @if($item->status == 'pending')
                    <div class="alert alert-warning py-2 small mb-0">Status: Menunggu Persetujuan</div>
                 @elseif($item->status == 'rejected')
                    <div class="alert alert-danger py-2 small mb-0">Status: Ditolak ({{ $item->note ?? '-' }})</div>
                 @endif
              </div>

              {{-- Actions: Delete, Edit & Move Album --}}
              <div class="p-3 bg-light border-bottom">
                     <div class="d-grid gap-2">
                    {{-- Edit Button --}}
                    <a href="{{ route('photos.edit', $item->id) }}" class="btn btn-outline-dark btn-sm rounded-pill fw-bold d-block text-decoration-none text-center">
                        Edit Foto
                    </a>
                    
                    {{-- Form Pindah Album (Clean Pill Style) --}}
                    <form action="{{ route('foto.update.album', ['photo' => $item->id]) }}" method="POST">
                        @csrf
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3 text-secondary">
                                <i class="bi bi-journal-album"></i>
                            </span>
                            <select name="album_id" class="form-select border-start-0 border-end-0 bg-white text-secondary" style="box-shadow: none; cursor: pointer;">
                                <option value="">Pilih Album...</option>
                                @if(isset($albums))
                                    @foreach($albums as $alb)
                                        <option value="{{ $alb->id }}" {{ $item->album_id == $alb->id ? 'selected' : '' }}>{{ $alb->nama_album }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <button class="btn btn-outline-secondary border-start-0 rounded-end-pill pe-3" type="submit" title="Simpan">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                    </form>

                    {{-- Delete Button --}}
                    <form action="{{ route('photos.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus foto ini permanen?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold">Hapus Foto</button>
                    </form>
                  </div>
              </div>

               {{-- Komentar List --}}
               <div class="flex-grow-1 p-3 overflow-auto custom-scrollbar bg-white">
                  <h6 class="fw-bold small text-muted mb-3">Komentar Masuk ({{ $item->komentarfoto->count() }})</h6>
                 
                  @forelse($item->komentarfoto as $komentar)
                     <div class="d-flex gap-2 mb-3">
                         @if($komentar->user->avatar)
                             <img src="{{ asset('storage/'.$komentar->user->avatar) }}" 
                                  class="rounded-circle flex-shrink-0" width="32" height="32" style="object-fit:cover;">
                         @else
                             <i class="bi bi-person-circle flex-shrink-0 default-avatar-icon" style="font-size: 32px;"></i>
                         @endif
                         <div class="bg-light p-2 rounded-3 w-100">
                            <div class="fw-bold small text-dark">{{ $komentar->user->username }}</div>
                            <p class="small mb-0 text-secondary">{{ $komentar->isi_komentar }}</p>
                        </div>
                    </div>
                 @empty
                    <div class="text-center text-muted small py-4">Belum ada komentar</div>
                 @endforelse
              </div>

              {{-- Like Info --}}
              <div class="p-3 border-top">
                  <div class="d-flex align-items-center text-danger fw-bold">
                     <i class="bi bi-heart-fill me-2"></i> {{ $item->like->count() }} Suka
                  </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endforeach

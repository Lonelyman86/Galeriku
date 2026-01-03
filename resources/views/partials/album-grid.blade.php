@foreach ($foto as $item)
  <div class="col-6 col-md-4 col-lg-3 mb-4 masonry-item">
    <div class="pin">
    <a onclick="openSingleModal({{ $item->id }})" style="cursor: pointer;">
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
  </div> 
@endforeach

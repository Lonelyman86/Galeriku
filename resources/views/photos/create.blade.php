@extends('main')

@section('content')
<form class="create-pin" action="{{ route('upload.photo') }}" method="POST" enctype="multipart/form-data">
  @csrf
  <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

  <h1 class="fs-4 mb-4 fw-bold px-2">Upload Foto</h1>

  <div class="create-grid">
    {{-- LEFT: Upload area --}}
    <div class="uploader">
      <label for="file" class="dropzone" id="dropzone">
        <div class="dz-inner" id="dz-inner">
          <div class="dz-icon">↑</div>
          <div class="dz-text">
            <strong>Pilih file</strong> atau seret dan jatuhkan di sini
          </div>
        </div>

        {{-- preview muncul setelah pilih --}}
        <img id="preview" alt="" style="display:none;" />
        <input id="file" type="file" name="lokasi_file" accept="image/*,video/mp4" hidden>
      </label>

      <div class="file-meta" id="file-meta">Belum ada file</div>
    </div>

    {{-- RIGHT: Fields --}}
    <div class="fields">
      <label class="field-label" for="judul_foto">Judul</label>
      <div class="pill-input">
        <input type="text" name="judul_foto" id="judul_foto" placeholder="Tambahkan judul" required>
      </div>

      <label class="field-label" for="caption">Deskripsi</label>
      <div class="pill-textarea">
        <textarea name="deskripsi_foto" id="caption" rows="5" placeholder="Tambahkan deskripsi terperinci"></textarea>
      </div>

      {{-- NEW: Category --}}
      <label class="field-label">Kategori</label>
      <div class="pill-input">
        <select name="category_id" class="form-select border-0 bg-transparent w-100" style="outline:none;">
            <option value="" selected disabled>Pilih Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
      </div>

      {{-- NEW: Tags --}}
      <label class="field-label">Hashtags</label>
      <div class="pill-input">
          <input type="text" name="tags" placeholder="Contoh: anime, sunset, waifu (pisahkan koma)">
      </div>

      <div class="actions">
        <button type="submit" class="btn-submit">Simpan</button>
      </div>
    </div>
  </div>
</form>



<script>
  // Klik area untuk buka file
  const fileInput = document.getElementById('file');
  const dropzone  = document.getElementById('dropzone');
  const preview   = document.getElementById('preview');
  const dzInner   = document.getElementById('dz-inner');
  const fileMeta  = document.getElementById('file-meta');



  // Drag & drop
  ['dragenter','dragover'].forEach(evt =>
    dropzone.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); dropzone.classList.add('dragover'); })
  );
  ['dragleave','drop'].forEach(evt =>
    dropzone.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); dropzone.classList.remove('dragover'); })
  );
  dropzone.addEventListener('drop', e => {
    const f = e.dataTransfer.files && e.dataTransfer.files[0];
    if (f){ fileInput.files = e.dataTransfer.files; handleFile(f); }
  });

  // On choose
  fileInput.addEventListener('change', e => {
    const f = e.target.files && e.target.files[0];
    if (f) handleFile(f);
  });

  function handleFile(file){
    fileMeta.textContent = file.name + ' (' + Math.round(file.size/1024) + ' KB)';
    // preview only images
    if (file.type.startsWith('image/')){
      const url = URL.createObjectURL(file);
      preview.src = url;
      preview.style.display = 'block';
      dzInner.style.display = 'none';
    } else {
      preview.style.display = 'none';
      dzInner.style.display = 'flex';
    }
  }
</script>
@endsection

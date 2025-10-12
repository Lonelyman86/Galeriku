@extends('main')

@section('content')
<form class="create-pin" action="{{ route('upload.photo') }}" method="POST" enctype="multipart/form-data">
  @csrf
  <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

  <h1 class="title">Upload Foto</h1>

  <div class="grid">
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

      <div class="actions">
        <button type="submit" class="btn-submit">Simpan</button>
      </div>
    </div>
  </div>
</form>

<style>
  /* Page */
  .create-pin{ padding:16px 16px 40px; }
  .title{
    font-family: ui-sans-serif, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial;
    font-size:22px; font-weight:800; margin:8px 0 16px 8px; color:#111;
  }

  /* 2-column layout */
  .grid{
    display:grid; grid-template-columns: 1.2fr 1fr; gap:24px; align-items:start;
    border-top:1px solid #eee; padding-top:20px;
  }
  @media (max-width: 992px){ .grid{ grid-template-columns:1fr; } }

  /* Uploader */
  .uploader{ width:100%; }
  .dropzone{
    display:block; width:100%; aspect-ratio: 1 / 1;
    background:#ececea; border-radius:20px; border:2px dashed transparent;
    position:relative; overflow:hidden; cursor:pointer;
  }
  .dropzone.dragover{ border-color:#111; background:#e8e8e6; }
  .dz-inner{ position:absolute; inset:0; display:flex; flex-direction:column;
    justify-content:center; align-items:center; text-align:center; padding:16px; color:#111; }
  .dz-icon{ width:40px; height:40px; line-height:40px; border-radius:999px; border:1px solid #111; text-align:center; font-weight:800; margin-bottom:8px; }
  .dz-text{ font-size:14px; }
  .dz-hint{ font-size:12px; color:#6b7280; margin-top:12px; }
  #preview{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }

  .file-meta{ margin-top:8px; font-size:13px; color:#6b7280; }

  /* Fields right */
  .fields{ display:flex; flex-direction:column; gap:14px; }
  .field-label{ font-size:12px; color:#4b5563; font-weight:700; margin-left:6px; }
  .pill-input, .pill-textarea{
    background:#ececea; border-radius:16px; padding:12px 14px; border:1px solid transparent;
  }
  .pill-input:focus-within, .pill-textarea:focus-within{ border-color:#111; background:#e8e8e6; }
  .pill-input input{
    width:100%; border:0; outline:0; background:transparent; font-size:14px; color:#111;
  }
  .pill-textarea textarea{
    width:100%; border:0; outline:0; background:transparent; font-size:14px; color:#111; resize:vertical;
  }

  .actions{ display:flex; justify-content:flex-end; margin-top:8px; }
  .btn-submit{
    background:#111; color:#fff; border:0; padding:10px 20px; border-radius:24px; font-weight:700; cursor:pointer;
  }
  .btn-submit:hover{ background:#333; }
</style>

<script>
  // Klik area untuk buka file
  const fileInput = document.getElementById('file');
  const dropzone  = document.getElementById('dropzone');
  const preview   = document.getElementById('preview');
  const dzInner   = document.getElementById('dz-inner');
  const fileMeta  = document.getElementById('file-meta');

  dropzone.addEventListener('click', () => fileInput.click());

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

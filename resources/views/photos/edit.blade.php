@extends('main')

@section('content')
<style>
    /* Force font consistency */
    body, p, h1, h2, h3, h4, h5, h6, input, textarea, select, button, .create-title, .file-meta, .field-label {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    }

    /* === DARK MODE OVERRIDES FOR EDIT PAGE === */
    body.dark-mode .create-title,
    body.dark-mode .field-label,
    body.dark-mode .file-meta {
        color: #e0e0e0 !important;
    }

    /* Input & Textarea Backgrounds */
    body.dark-mode .pill-input,
    body.dark-mode .pill-textarea {
        background-color: #2b2b2b !important;
        border: 1px solid #444;
    }
    body.dark-mode .pill-input input,
    body.dark-mode .pill-input select,
    body.dark-mode .pill-textarea textarea {
        background-color: transparent !important;
        color: #e0e0e0 !important;
    }
    body.dark-mode .pill-input input::placeholder,
    body.dark-mode .pill-textarea textarea::placeholder {
        color: #888 !important;
    }

    /* Select Dropdown Options */
    body.dark-mode option {
        background-color: #2b2b2b;
        color: #e0e0e0;
    }

    /* Batal Button */
    body.dark-mode .btn-light {
        background-color: #2b2b2b;
        color: #e0e0e0;
        border-color: #444;
    }
    body.dark-mode .btn-light:hover {
        background-color: #333;
    }
</style>

<form class="create-pin" action="{{ route('photos.update', $photo->id) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PATCH')

  {{-- Header match Studio Font --}}
  <h1 class="fs-4 mb-4 fw-bold">Edit Foto</h1>

  <div class="create-grid">
    {{-- LEFT: Upload area (Preview Only) --}}
    <div class="uploader" style="cursor: default;">
      <label class="dropzone" id="dropzone" style="pointer-events: none; border-style: solid;">
        <div class="dz-inner" style="display: none;"></div>
        {{-- Preview existing image --}}
        <img id="preview" src="{{ Storage::url('foto/'.$photo->lokasi_file) }}" alt="{{ $photo->judul_foto }}" style="display:block;" />
      </label>
      <div class="file-meta text-center mt-2 text-muted">File tidak dapat diubah saat edit</div>
    </div>

    {{-- RIGHT: Fields --}}
    <div class="fields">
      <label class="field-label" for="judul_foto">Judul</label>
      <div class="pill-input">
        <input type="text" name="judul_foto" id="judul_foto" value="{{ old('judul_foto', $photo->judul_foto) }}" required>
      </div>

      <label class="field-label" for="caption">Deskripsi</label>
      <div class="pill-textarea">
        <textarea name="deskripsi_foto" id="caption" rows="5" required>{{ old('deskripsi_foto', $photo->deskripsi_foto) }}</textarea>
      </div>

      {{-- Category --}}
      <label class="field-label">Kategori</label>
      <div class="pill-input">
        <select name="category_id" class="form-select border-0 bg-transparent w-100" style="outline:none;">
            <option value="">Pilih Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $photo->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
      </div>

      {{-- Tags --}}
      <label class="field-label">Hashtags</label>
      <div class="pill-input">
          <input type="text" name="tags"
                 value="{{ old('tags', $photo->tags->pluck('name')->implode(', ')) }}"
                 placeholder="Contoh: anime, sunset, waifu (pisahkan koma)">
      </div>

      <div class="actions d-flex gap-2">
        <a href="{{ url('/studio') }}" class="btn btn-light rounded-pill px-4 fw-bold">Batal</a>
        <button type="submit" class="btn-submit flex-grow-1">Simpan Perubahan</button>
      </div>
    </div>
  </div>
</form>
@endsection

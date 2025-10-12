@extends('main')

@section('content')
<div class="create-wrap">
  <form action="{{ route('album.new') }}" method="POST" class="create-card" id="create-album-form">
    @csrf
    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

    <h1 class="title">Buat album</h1>

    {{-- Input Nama --}}
    <label class="field-label" for="nama_album">Nama</label>
    <div class="pill-input">
      <input
        type="text"
        name="nama_album"
        id="nama_album"
        placeholder='Seperti “Tempat untuk Dikunjungi”'
        required
      />
    </div>

    {{-- Tambahkan caption --}}
    <div class="caption-block">
      <label class="field-label" for="caption">Tambahkan caption</label>
      <div class="searchish-input">
        <input
          type="text"
          name="deskripsi"
          id="caption"
          placeholder="Tulis deskripsi singkat album"
        />
      </div>
    </div>

    {{-- Tombol buat --}}
    <div class="actions">
      <button type="submit" class="btn-create" id="submit-btn" disabled>Buat</button>
    </div>
  </form>
</div>

<style>
  body {
    background-color: #fff;
    color: #111;
    font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  }

  .create-wrap {
    display: flex;
    justify-content: center;
    padding: 40px 16px;
  }

  .create-card {
    width: 100%;
    max-width: 520px;
    background: #ffffff;
    border-radius: 16px;
    padding: 32px 24px 24px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }

  .title {
    font-size: 28px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 24px;
    color: #111;
  }

  .field-label {
    display: block;
    font-size: 14px;
    color: #333;
    margin: 12px 0 6px;
    font-weight: 600;
  }

  /* Input putih ala Pinterest */
  .pill-input {
    border: 1px solid #ddd;
    border-radius: 16px;
    padding: 10px 14px;
    background-color: #fff;
  }

  .pill-input input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 15px;
    color: #111;
    background: transparent;
  }

  .pill-input:focus-within {
    border-color: #000;
  }

  .caption-block {
    margin-top: 16px;
  }

  .searchish-input {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 16px;
    padding: 10px 14px;
    background-color: #fff;
  }

  .searchish-input::before {
    content: "🔍";
    margin-right: 8px;
    opacity: 0.6;
  }

  .searchish-input input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 15px;
    color: #111;
    background: transparent;
  }

  .searchish-input:focus-within {
    border-color: #000;
  }

  .actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 24px;
  }

  .btn-create {
    background-color: #eaeaea;
    color: #999;
    border: none;
    border-radius: 24px;
    padding: 10px 24px;
    font-weight: 600;
    cursor: not-allowed;
    transition: background-color 0.2s ease;
  }

  .btn-create.enabled {
    background-color: #000;
    color: #fff;
    cursor: pointer;
  }

  .btn-create.enabled:hover {
    background-color: #333;
  }
</style>

<script>
  const nameInput = document.getElementById("nama_album");
  const submitBtn = document.getElementById("submit-btn");

  nameInput.addEventListener("input", () => {
    const filled = nameInput.value.trim().length > 0;
    submitBtn.disabled = !filled;
    submitBtn.classList.toggle("enabled", filled);
  });
</script>
@endsection

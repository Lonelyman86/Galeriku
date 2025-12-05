@extends('main')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">
            <i class="bi bi-images text-danger me-2"></i>Kelola Foto User
        </h2>
        <span class="badge bg-danger fs-6 px-3 py-2">Admin Panel</span>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Judul</th>
                            <th>Album</th>
                            <th>Status</th>
                            <th>Note</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fotos as $foto)
                        <tr class="table-row-hover">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Cek avatar user (Optimasi null safety) --}}
                                    <img src="{{ $foto->user->avatar ? asset('storage/' . $foto->user->avatar) : asset('assets/img/default-profile.png') }}"
                                         alt="Profile"
                                         class="rounded-circle border"
                                         width="32"
                                         height="32">
                                    <span class="fw-semibold">{{ $foto->user->username }}</span>
                                </div>
                            </td>

                            <td class="fw-semibold">{{ $foto->judul_foto }}</td> {{-- Pastikan nama kolom sesuai DB (judul_foto) --}}
                            <td>{{ $foto->album->nama_album ?? '-' }}</td>

                            {{-- Status Badge --}}
                            <td>
                                <span class="badge 
                                    @if($foto->status == 'approved') bg-success 
                                    @elseif($foto->status == 'rejected') bg-danger 
                                    @else bg-warning text-dark @endif
                                ">
                                    {{ ucfirst($foto->status) }}
                                </span>
                            </td>

                            {{-- Note --}}
                            <td>{{ $foto->note ?? '-' }}</td>

                            {{-- Tombol Aksi --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($foto->status === 'pending')
                                        <form action="{{ route('admin.foto.approve', $foto->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-success" title="Setujui">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>

                                        {{-- Tombol Tolak buka modal --}}
                                        <button type="button"
                                                class="btn btn-sm btn-warning text-white"
                                                title="Tolak"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectModal"
                                                data-action="{{ route('admin.foto.reject', $foto->id) }}">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif

                                    <form action="{{ route('admin.foto.destroy', $foto->id) }}" method="POST" onsubmit="return confirm('Yakin hapus foto ini secara permanen?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Hapus Permanen">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Jika belum ada foto --}}
            @if($fotos->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-images fs-1"></i>
                    <p class="mt-2">Belum ada foto yang diupload user.</p>
                </div>
            @endif

            {{-- PERBAIKAN 1: Tombol Pagination Wajib Ada --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $fotos->links() }}
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="rejectForm" method="POST">
      @csrf
      @method('PATCH')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rejectModalLabel">Pilih Alasan Penolakan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <p class="mb-3 text-muted">Pilih salah satu alasan kenapa foto ini ditolak:</p>
          
          {{-- Hapus name="note" di sini agar tidak bentrok, kita handle via JS --}}
          <select id="noteSelect" class="form-select mb-3" required>
            <option value="">-- Pilih Alasan --</option>
            <option value="Foto mengandung SARA">Foto mengandung SARA</option>
            <option value="Konten tidak pantas">Konten tidak pantas</option>
            <option value="Kualitas foto rendah">Kualitas foto rendah</option>
            <option value="Plagiat / menyalin karya orang lain">Plagiat / menyalin karya orang lain</option>
            <option value="Spam / upload berulang">Spam / upload berulang</option>
            <option value="Tidak sesuai kategori">Tidak sesuai kategori</option>
            <option value="Mengandung unsur kekerasan">Mengandung unsur kekerasan</option>
            <option value="Gambar tidak relevan">Gambar tidak relevan</option>
            <option value="Mengandung watermark komersial">Mengandung watermark komersial</option>
            <option value="Lainnya">Lainnya</option>
          </select>

          {{-- Input alasan custom --}}
          <input type="text" id="customNote" class="form-control d-none" placeholder="Tulis alasan lainnya...">
          
          {{-- Input Hidden untuk menampung nilai final yang dikirim ke controller --}}
          <input type="hidden" name="note" id="finalNote">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak Foto</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Script untuk modal (PERBAIKAN LOGIKA) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rejectModal = document.getElementById('rejectModal');
    const rejectForm = document.getElementById('rejectForm');
    const noteSelect = document.getElementById('noteSelect');
    const customNote = document.getElementById('customNote');
    const finalNote = document.getElementById('finalNote'); // Input hidden

    // Set action form dinamis pas tombol ditekan
    rejectModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const action = button.getAttribute('data-action');
        rejectForm.setAttribute('action', action);
        
        // Reset form saat modal dibuka
        noteSelect.value = "";
        customNote.value = "";
        customNote.classList.add('d-none');
    });

    // Munculin input custom kalau pilih "Lainnya"
    noteSelect.addEventListener('change', function () {
        if (this.value === 'Lainnya') {
            customNote.classList.remove('d-none');
            customNote.required = true;
            customNote.focus();
        } else {
            customNote.classList.add('d-none');
            customNote.required = false;
        }
    });

    // PERBAIKAN: Logika submit yang benar
    rejectForm.addEventListener('submit', function (e) {
        // Tentukan nilai mana yang dipakai
        if (noteSelect.value === 'Lainnya') {
            finalNote.value = customNote.value; // Pakai teks manual
        } else {
            finalNote.value = noteSelect.value; // Pakai pilihan dropdown
        }
        
        // Cek validasi sederhana
        if (!finalNote.value.trim()) {
            e.preventDefault();
            alert('Harap pilih alasan atau isi alasan lainnya!');
        }
    });
});
</script>

{{-- Styling tambahan --}}
<style>
.table-row-hover:hover {
    background-color: #f9fafb;
    transition: background-color 0.2s ease;
}
.badge {
    font-size: 0.85rem;
    padding: 0.5em 0.75em;
    border-radius: 0.5rem;
}
.btn {
    transition: all 0.2s ease;
}
.btn:hover {
    transform: translateY(-2px);
}
.card {
    border-radius: 12px;
}
/* Styling Pagination agar rapi */
.pagination {
    margin-bottom: 0;
}
</style>
@endsection
@extends('main')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">
            <i class="bi bi-images text-danger me-2"></i>Moderasi Konten
        </h2>
        <span class="badge bg-danger fs-6 px-3 py-2">Admin Panel</span>
    </div>

    {{-- BUTTON ABS (Client-Side) --}}
    <ul class="nav nav-pills mb-4 gap-2" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active bg-danger text-white" id="photos-tab" data-bs-toggle="tab" data-bs-target="#photos" type="button" role="tab" aria-controls="photos" aria-selected="true">
                <i class="bi bi-grid-fill me-2"></i>Semua Foto
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link bg-white text-secondary border" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="false">
                <i class="bi bi-flag-fill me-2"></i>Laporan Masuk
                @if($reports->total() > 0)
                <span class="badge bg-danger ms-2">{{ $reports->total() }}</span>
                @endif
            </button>
        </li>
    </ul>

    <div class="tab-content" id="adminTabsContent">

        {{-- TAB 1: SEMUA FOTO --}}
        <div class="tab-pane fade show active" id="photos" role="tabpanel" aria-labelledby="photos-tab">
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
                                            <img src="{{ Str::startsWith($foto->user->avatar, ['http', 'data:']) ? $foto->user->avatar : ($foto->user->avatar ? asset('storage/' . $foto->user->avatar) : asset('assets/img/default-profile.png')) }}"
                                                 class="rounded-circle border" width="32" height="32" style="object-fit: cover;">
                                            <span class="fw-semibold">{{ $foto->user->username }}</span>
                                        </div>
                                    </td>
                                    <td class="fw-semibold">{{ $foto->judul_foto }}</td>
                                    <td>{{ $foto->album->nama_album ?? '-' }}</td>
                                    <td>
                                        <span class="badge @if($foto->status == 'approved') bg-success @elseif($foto->status == 'rejected') bg-danger @else bg-warning text-dark @endif">
                                            {{ ucfirst($foto->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $foto->note ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            @if($foto->status === 'pending')
                                                <form action="{{ route('admin.foto.approve', $foto->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-success" title="Setujui"><i class="bi bi-check-circle"></i></button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-warning text-white" title="Tolak" data-bs-toggle="modal" data-bs-target="#rejectModal" data-action="{{ route('admin.foto.reject', $foto->id) }}"><i class="bi bi-x-circle"></i></button>
                                            @endif
                                            <form action="{{ route('admin.foto.destroy', $foto->id) }}" method="POST" onsubmit="return confirm('Yakin hapus foto ini secara permanen?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Hapus Permanen"><i class="bi bi-trash3"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($fotos->isEmpty())
                        <div class="text-center py-5 text-muted"><i class="bi bi-images fs-1"></i><p class="mt-2">Belum ada foto yang diupload user.</p></div>
                    @endif
                    <div class="d-flex justify-content-center mt-4">
                        {{ $fotos->appends(['reports_page' => $reports->currentPage()])->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: LAPORAN MASUK --}}
        <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Pelapor</th>
                                    <th>Alasan</th>
                                    <th>Konten Dilaporkan</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                <tr class="table-row-hover">
                                    <td>
                                        {{ $report->user->username ?? 'Unknown' }}
                                        <div class="small text-muted">{{ $report->user->email ?? '-' }}</div>
                                    </td>
                                    <td>{{ $report->reason }}</td>
                                    <td>
                                        @php $target = $report->reportable; @endphp
                                        @if($target)
                                            @if($target instanceof \App\Models\Foto)
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ Str::startsWith($target->lokasi_file, ['http', 'data:']) ? $target->lokasi_file : asset('storage/foto/'.$target->lokasi_file) }}" target="_blank">
                                                        <img src="{{ Str::startsWith($target->lokasi_file, ['http', 'data:']) ? $target->lokasi_file : asset('storage/foto/'.$target->lokasi_file) }}" width="60" class="rounded" style="object-fit:cover;">
                                                    </a>
                                                    <div class="small lh-sm">
                                                        <span class="badge bg-info mb-1">Foto</span><br>
                                                        <strong>{{Str::limit($target->judul_foto, 20)}}</strong><br>
                                                        <span class="text-muted">by {{ $target->user->username ?? '?' }}</span>
                                                    </div>
                                                </div>
                                            @elseif($target instanceof \App\Models\Komentar)
                                                 <div class="p-2 bg-light border rounded small">
                                                    <span class="badge bg-warning text-dark mb-1">Komentar</span><br>
                                                    <i class="bi bi-chat-quote-fill text-secondary"></i> "{{ Str::limit($target->isi_komentar, 50) }}"
                                                    <br><span class="text-muted">by {{ $target->user->username ?? '?' }}</span>
                                                 </div>
                                            @elseif($target instanceof \App\Models\User)
                                                 <div class="d-flex align-items-center gap-2">
                                                     <img src="{{ $target->avatar ? asset('storage/'.$target->avatar) : asset('assets/img/default-profile.png') }}" width="32" height="32" class="rounded-circle" style="object-fit:cover;">
                                                     <div>
                                                         <span class="badge bg-primary mb-1">User</span><br>
                                                         <strong>{{ $target->username }}</strong>
                                                     </div>
                                                 </div>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Konten Terhapus</span>
                                        @endif
                                    </td>
                                    <td>{{ $report->created_at->format('d M Y') }}</td>
                                    <td>
                                        <form action="{{ route('admin.reports.ban', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tindak lanjuti laporan ini? (Konten akan dihapus/di-resolve)')">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-danger btn-sm" title="Resolve & Hapus"><i class="bi bi-check-lg"></i> Tindak</button>
                                        </form>
                                        <form action="{{ route('admin.reports.dismiss', $report->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-secondary btn-sm" title="Abaikan"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada laporan baru.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $reports->appends(['page' => $fotos->currentPage()])->links() }}
                    </div>
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

{{-- Script untuk modal & Tab Persistence --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Tab Persistence & Dynamic Styling
    var triggerTabList = [].slice.call(document.querySelectorAll('#adminTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)

        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()

            // Save state
            localStorage.setItem('activeAdminTab', event.target.id);

            // Update Styling
            updateTabStyles(triggerEl);
        })
    })

    // Load saved tab
    var activeTabId = localStorage.getItem('activeAdminTab');
    if(activeTabId && document.getElementById(activeTabId)){
        var tabToShow = new bootstrap.Tab(document.getElementById(activeTabId));
        tabToShow.show();
        updateTabStyles(document.getElementById(activeTabId));
    }

    function updateTabStyles(activeEl) {
        document.querySelectorAll('#adminTabs button').forEach(btn => {
            if(btn === activeEl) {
                // Active: Merah
                btn.classList.add('bg-danger', 'text-white');
                btn.classList.remove('bg-white', 'text-secondary', 'border');
            } else {
                // Inactive: Abu/Putih
                btn.classList.remove('bg-danger', 'text-white');
                btn.classList.add('bg-white', 'text-secondary', 'border');
            }
        });
    }

    // -------------------------------------------------------------------

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

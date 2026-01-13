@extends('main')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">
            <i class="bi bi-shield-exclamation text-danger me-2"></i>Pusat Laporan
        </h2>
        <span class="badge bg-danger fs-6 px-3 py-2">Admin Panel</span>
    </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
    {{-- NAVIGATION TABS --}}
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a class="nav-link bg-white text-secondary border" href="{{ route('admin.foto.index') }}">
                <i class="bi bi-grid-fill me-2"></i>Semua Foto
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active bg-danger text-white" href="{{ route('admin.reports.index') }}">
                <i class="bi bi-flag-fill me-2"></i>Laporan Masuk
            </a>
        </li>
    </ul>

    <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan Pending</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Pelapor</th>
                                <th>Alasan</th>
                                <th>Foto</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr>
                                <td>
                                    {{ $report->user->username ?? 'Unknown' }}
                                    <br><small class="text-muted">{{ $report->user->email ?? '-' }}</small>
                                </td>
                                <td>{{ $report->reason }}</td>
                                <td>
                                    @php $target = $report->reportable; @endphp
                                    @if($target)
                                        @if($target instanceof \App\Models\Foto)
                                            <a href="{{ asset('storage/foto/'.$target->lokasi_file) }}" target="_blank">
                                                <img src="{{ asset('storage/foto/'.$target->lokasi_file) }}" width="100" class="rounded">
                                            </a>
                                            <div class="small mt-1">{{Str::limit($target->judul_foto, 20)}}</div>
                                            <div class="badge bg-info">Foto</div>
                                        @elseif($target instanceof \App\Models\Komentar)
                                            <div class="p-2 bg-light border rounded">
                                                <i class="fas fa-comment text-secondary"></i> "{{ Str::limit($target->isi_komentar, 50) }}"
                                            </div>
                                            <div class="badge bg-warning text-dark mt-1">Komentar</div>
                                        @elseif($target instanceof \App\Models\User)
                                            <div class="d-flex align-items-center gap-2">
                                                 <img src="{{ $target->avatar ? asset('storage/'.$target->avatar) : asset('assets/img/default-profile.png') }}" width="32" height="32" class="rounded-circle" style="object-fit:cover;">
                                                 <strong>{{ $target->username }}</strong>
                                            </div>
                                            <div class="badge bg-primary mt-1">User</div>
                                        @endif
                                    @else
                                        <span class="text-muted font-italic">Konten Terhapus</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.reports.ban', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus foto ini? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-danger btn-sm mb-1">
                                            <i class="fas fa-trash"></i> Hapus Foto
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reports.dismiss', $report->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-secondary btn-sm mb-1">
                                            <i class="fas fa-times"></i> Abaikan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Tidak ada laporan baru.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $reports->links() }}
            </div>
            </div>
        </div>
    </div>
</div>
@endsection

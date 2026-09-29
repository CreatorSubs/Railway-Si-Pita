@extends('layouts.main')

@section('title', 'Cek / Daftar Sertifikat')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">📜 Daftar Sertifikat Dibuat</h4>
        <a href="{{ route('admin.certificate.create') }}" class="btn btn-primary border-2 border-dark rounded-pill fw-bold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Buat Sertifikat Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-2 border-dark rounded-3 fw-bold" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Search Bar -->
    <form action="{{ route('admin.certificate.show_all') }}" method="GET" class="mb-4">
        <div class="input-group">
            <span class="input-group-text bg-white border-2 border-dark border-end-0 rounded-start-pill ps-3">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control border-2 border-dark border-start-0 rounded-end-pill py-2" 
                   placeholder="Cari berdasarkan nama, NIP/NIK, nomor sertifikat, atau kegiatan..." value="{{ request('search') }}">
        </div>
    </form>

    <div class="card border-2 border-dark rounded-4 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>No. Sertifikat</th>
                        <th>Nama Penerima</th>
                        <th>NIK / NIP</th>
                        <th>Instansi</th>
                        <th>Kegiatan</th>
                        <th>Tanggal Terbit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse($certificates as $index => $cert)
                        <tr>
                            <td>{{ $certificates->firstItem() + $index }}</td>
                            <td><span class="badge bg-light text-dark border border-dark fw-bold">{{ $cert->certificate_number }}</span></td>
                            <td class="fw-bold text-start">{{ $cert->recipient_name }}</td>
                            <td>{{ $cert->recipient_identity ?? '-' }}</td>
                            <td>{{ $cert->institution ?? '-' }}</td>
                            <td>{{ $cert->event_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($cert->issue_date)->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.certificate.download', $cert->id) }}" class="btn btn-sm btn-outline-primary rounded-pill fw-bold" target="_blank">
                                        <i class="bi bi-download"></i> Unduh
                                    </a>

                                    <a href="{{ route('admin.certificate.editor', $cert->id) }}" class="btn btn-sm btn-outline-dark rounded-pill fw-bold">
                                        <i class="bi bi-arrows-move"></i> Posisi
                                    </a>

                                    <!-- Form Hapus -->
                                    <form action="{{ route('admin.certificate.destroy', $cert->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sertifikat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill fw-bold">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted fw-bold">
                                Belum ada data sertifikat yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($certificates->hasPages())
            <div class="card-footer bg-white border-top border-dark d-flex justify-content-center pt-3">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

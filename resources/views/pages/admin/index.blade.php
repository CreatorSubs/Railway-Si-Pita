@extends('layouts.main')

@section('title', 'Cek Sertifikat')

@section('content')
<div class="container">
    <div class="card card-custom p-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark m-0">Rekap Data Sertifikat</h5>
            <a href="{{ route('admin.certificate.create') }}" class="btn btn-primary-custom text-white rounded-pill px-3">+ Buat Sertifikat</a>
        </div>

        <div class="mb-3">
            <input type="text" class="form-control rounded-pill border-0 px-4" placeholder="🔍 Cari berdasarkan Nama / NIP / Kegiatan...">
        </div>

        <div class="table-responsive bg-white rounded-3 p-2">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIP / NIK</th>
                        <th>Instansi</th>
                        <th>Nama Kegiatan</th>
                        <th>Peran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $key => $item)
                        <tr>
                            <td>{{ $certificates->firstItem() + $key }}</td>
                            <td>{{ $item->recipient_name }}</td>
                            <td>{{ $item->recipient_identity }}</td>
                            <td>{{ $item->institution }}</td>
                            <td>{{ $item->event_name }}</td>
                            <td><span class="badge bg-info text-dark">{{ $item->role }}</span></td>
                            <td>
                                <a href="{{ route('admin.certificate.download', $item->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">Download</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted py-4">Belum ada data sertifikat yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $certificates->links() }}
        </div>
    </div>
</div>
@endsection

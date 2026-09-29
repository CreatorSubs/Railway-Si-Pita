@extends('layouts.main')

@section('title', 'History Pembuatan Sertifikat')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">📜 History Pembuatan Sertifikat (Owner View)</h4>
        <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill border border-dark">👑 Admin Owner</span>
    </div>

    <div class="card border-2 border-dark rounded-4 shadow-sm p-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light border-bottom border-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Penerima</th>
                        <th>NIP / Identitas</th>
                        <th>Nama Kegiatan</th>
                        <th>Dibuat Oleh</th>
                        <th>Tanggal Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $index => $cert)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $cert->recipient_name ?? '-' }}</td>
                            <td>{{ $cert->recipient_identity ?? '-' }}</td>
                            <td>{{ $cert->event_name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info text-dark border border-dark">
                                    {{ $cert->created_by ?? 'admin@diskominfo.go.id' }}
                                </span>
                            </td>
                            <td>{{ $cert->created_at ? $cert->created_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat pembuatan sertifikat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.main')

@section('title', 'Kelola Akun Admin')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">👥 Daftar & Kelola Akun Admin</h4>
        <a href="{{ route('admin.user.create') }}" class="btn btn-primary rounded-pill border-2 border-dark fw-bold">
            <i class="bi bi-person-plus me-1"></i> Tambah Admin Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-2 border-dark rounded-3 fw-bold mb-3">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-2 border-dark rounded-3 fw-bold mb-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="card border-2 border-dark rounded-4 shadow-sm p-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light border-bottom border-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center">Aksi (Aktif / Nonaktif)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @if(strtolower($u->email) === 'admin@diskominfo.go.id')
                                    <span class="badge bg-danger">👑 Owner</span>
                                @else
                                    <span class="badge bg-secondary">Admin</span>
                                @endif
                            </td>
                            <td>
                                @if($u->is_active ?? true)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(strtolower($u->email) !== 'admin@diskominfo.go.id')
                                    <form action="{{ route('admin.user.toggle', $u->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($u->is_active ?? true)
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill fw-bold" onclick="return confirm('Nonaktifkan akun ini?')">
                                                <i class="bi bi-person-x me-1"></i> Nonaktifkan
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-outline-success btn-sm rounded-pill fw-bold">
                                                <i class="bi bi-person-check me-1"></i> Aktifkan
                                            </button>
                                        @endif
                                    </form>
                                @else
                                    <span class="text-muted small">Utama (Permanen)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada akun admin terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

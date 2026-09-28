@extends('layouts.main')

@section('title', 'Buat Sertifikat Baru')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom p-4 shadow-sm">
                <h5 class="fw-bold mb-4 text-dark text-center">Buat Sertifikat Baru</h5>
                
                <form action="{{ route('admin.certificate.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Sertifikat</label>
                            <input type="text" name="certificate_number" class="form-control rounded-pill px-3" placeholder="Contoh: SERT/001/2026" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Penerima</label>
                            <input type="text" name="recipient_name" class="form-control rounded-pill px-3" placeholder="Nama Lengkap & Gelar" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIP / NIK / Identitas</label>
                            <input type="text" name="recipient_identity" class="form-control rounded-pill px-3" placeholder="Nomor Identitas" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Instansi</label>
                            <input type="text" name="institution" class="form-control rounded-pill px-3" value="Diskominfo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Kegiatan</label>
                            <input type="text" name="event_name" class="form-control rounded-pill px-3" placeholder="Pelatihan / Workshop..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Peran / Status</label>
                            <input type="text" name="role" class="form-control rounded-pill px-3" value="Peserta">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Terbit</label>
                            <input type="date" name="issue_date" class="form-control rounded-pill px-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Template Background (Gambar)</label>
                            <input type="file" name="template" class="form-control rounded-pill px-3" accept="image/*">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.certificate.create') }}" class="btn btn-secondary rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary-custom text-white rounded-pill px-4">Lanjut Atur Posisi Teks →</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

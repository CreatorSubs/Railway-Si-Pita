@extends('layouts.main')

@section('title', 'Buat Sertifikat')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="card border-2 border-dark rounded-4 bg-white shadow-sm p-4">
        <form action="{{ route('admin.certificate.store') }}" method="POST" enctype="multipart/form-data" id="certForm">
            @csrf

            <div class="row g-4 align-items-center">
                <!-- SISI KIRI: FORM INPUTS -->
                <div class="col-lg-5 col-md-6 d-flex flex-column gap-3">
                    
                    <div>
                        <input type="text" id="certNumberPrefix" name="certificate_number_prefix" class="form-control border-dark rounded-pill py-2 text-center shadow-sm" placeholder="Masukkan Nomor Sertifikat" required>
                    </div>

                    <div>
                        <input type="text" id="eventName" name="event_name" class="form-control border-dark rounded-pill py-2 text-center shadow-sm" placeholder="Masukkan Nama Kegiatan" required>
                    </div>

                    <div>
                        <input type="date" name="issue_date" class="form-control border-dark rounded-pill py-2 text-center shadow-sm" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Custom File Upload Button -->
                    <div>
                        <label for="templateInput" class="btn btn-outline-primary border-2 border-dark rounded-pill w-100 py-2 fw-bold text-truncate shadow-sm">
                            <i class="bi bi-upload me-1"></i> <span id="uploadLabelText">Upload Sertifikat Design</span>
                        </label>
                        <input type="file" name="template" id="templateInput" class="d-none" accept="image/png, image/jpeg" onchange="previewTemplate(event)">
                    </div>

                    <!-- Button Triggers Modal Insert Data -->
                    <div>
                        <button type="button" class="btn btn-outline-dark border-2 border-dark rounded-pill w-100 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#insertDataModal">
                            <i class="bi bi-table me-1"></i> Insert Data (<span id="dataCount">0</span> Data Selected)
                        </button>
                    </div>

                    <!-- Button Atur Posisi Teks & QR -->
                    <div>
                        @if(!empty($sampleCertificateId))
                            <a href="{{ route('admin.certificate.editor', $sampleCertificateId) }}" onclick="saveDraftToSession(event, this.href)" class="btn btn-outline-secondary border-2 border-dark rounded-pill w-100 py-2 fw-bold shadow-sm">
                                <i class="bi bi-arrows-move me-1"></i> Text & QR Positions
                            </a>
                        @else
                            <button type="button" class="btn btn-outline-secondary border-2 border-dark rounded-pill w-100 py-2 fw-bold shadow-sm" onclick="alert('Belum ada sertifikat tersimpan. Buat sertifikat pertama terlebih dahulu untuk dapat mengatur posisi.')">
                                <i class="bi bi-arrows-move me-1"></i> Text & QR Positions
                            </button>
                        @endif
                    </div>

                    <!-- MAIN SUBMIT BUTTON -->
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary border-2 border-dark rounded-pill w-100 py-3 fw-bold fs-5 shadow">
                            Buat Sertifikat
                        </button>
                    </div>

                </div>

                <!-- SISI KANAN: PREVIEW CANVAS & PAGINATION -->
                <div class="col-lg-7 col-md-6 text-center">
                    <div class="border-2 border-dark rounded-4 p-3 bg-light d-flex flex-column align-items-center justify-content-center shadow-inner" style="min-height: 380px;">
                        
                        <!-- Image Canvas Preview -->
                        <div class="position-relative d-inline-block border border-secondary rounded overflow-hidden shadow-sm max-w-100" style="background-color: #d1d5db; min-width: 80%; min-height: 260px;">
                            <img id="certPreview" src="" class="img-fluid d-none" style="max-height: 320px; object-fit: contain;">
                            <div id="previewPlaceholder" class="d-flex align-items-center justify-content-center fw-bold text-secondary" style="height: 260px;">
                                Preview Sertifikat
                            </div>
                        </div>

                        <!-- Pagination Controls Below Preview -->
                        <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-dark btn-sm rounded-circle px-2" disabled>&laquo;</button>
                            <span class="badge bg-primary px-3 py-2 border border-dark rounded-pill fs-7 fw-bold">1</span>
                            <button type="button" class="btn btn-dark btn-sm rounded-circle px-2" disabled>&raquo;</button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- MODAL OVERLAY: INSERT DATA -->
            <div class="modal fade" id="insertDataModal" tabindex="-1" aria-labelledby="insertDataModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-2 border-dark rounded-4">
                        <div class="modal-header border-bottom border-dark bg-light">
                            <h5 class="modal-title fw-bold text-dark" id="insertDataModalLabel">📋 Insert Data Penerima Sertifikat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <ul class="nav nav-pills nav-fill gap-2 mb-3" id="modalDataTab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active fw-bold border border-dark" id="modal-excel-tab" data-bs-toggle="tab" data-bs-target="#modal-excel" type="button">Import CSV / Excel</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link fw-bold border border-dark" id="modal-manual-tab" data-bs-toggle="tab" data-bs-target="#modal-manual" type="button">Input Grid Manual</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="modalDataTabContent">
                                <div class="tab-pane fade show active p-3 bg-light border border-dark rounded-3" id="modal-excel">
                                    <label class="form-label fw-bold">Pilih File CSV Data Peserta</label>
                                    <input type="file" name="csv_file" class="form-control border-dark">
                                    <small class="text-muted d-block mt-2">Format kolom CSV: No Sertifikat, Tanggal, Nama, NIK/NIP, Instansi, Peran</small>
                                </div>

                                <div class="tab-pane fade p-2" id="modal-manual">
                                    <div class="table-responsive" style="max-height: 250px;">
                                        <table class="table table-bordered align-middle" id="modalGridTable">
                                            <thead class="table-dark">
                                                <tr class="small text-center">
                                                    <th>Nama Penerima</th>
                                                    <th>NIK / NIP</th>
                                                    <th>Instansi</th>
                                                    <th>Peran</th>
                                                    <th>#</th>
                                                </tr>
                                            </thead>
                                            <tbody id="modalGridBody">
                                                <tr>
                                                    <td><input type="text" name="participants[0][name]" class="form-control form-control-sm p-name" placeholder="Nama Lengkap" oninput="saveParticipantsToSession()"></td>
                                                    <td><input type="text" name="participants[0][identity_number]" class="form-control form-control-sm p-identity" placeholder="NIK/NIP" oninput="saveParticipantsToSession()"></td>
                                                    <td><input type="text" name="participants[0][agency]" class="form-control form-control-sm p-agency" value="Diskominfo" oninput="saveParticipantsToSession()"></td>
                                                    <td><input type="text" name="participants[0][role]" class="form-control form-control-sm p-role" value="Peserta" oninput="saveParticipantsToSession()"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm rounded-circle px-2" onclick="removeModalRow(this)">✕</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold rounded-pill mt-2" onclick="addModalRow()">
                                        <i class="bi bi-plus-lg me-1"></i> Tambah Baris
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top border-dark">
                            <button type="button" class="btn btn-primary fw-bold rounded-pill px-4 border border-dark" data-bs-dismiss="modal">Simpan Data & Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    let mRowIndex = 0;

    document.addEventListener('DOMContentLoaded', function() {
        // Restore input dasar
        const savedImg = sessionStorage.getItem('draft_cert_image');
        const savedEvent = sessionStorage.getItem('draft_event_name');
        const savedPrefix = sessionStorage.getItem('draft_cert_prefix');

        if (savedEvent) document.getElementById('eventName').value = savedEvent;
        if (savedPrefix) document.getElementById('certNumberPrefix').value = savedPrefix;
        if (savedImg) {
            const img = document.getElementById('certPreview');
            img.src = savedImg;
            img.classList.remove('d-none');
            document.getElementById('previewPlaceholder').classList.add('d-none');
            document.getElementById('uploadLabelText').textContent = "Gambar Terpilih (Draft)";
        }

        // Restore tabel data peserta
        restoreParticipantsFromSession();

        // Listener event input
        document.getElementById('eventName').addEventListener('input', function(e) {
            sessionStorage.setItem('draft_event_name', e.target.value);
        });
        document.getElementById('certNumberPrefix').addEventListener('input', function(e) {
            sessionStorage.setItem('draft_cert_prefix', e.target.value);
        });
    });

    function previewTemplate(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('uploadLabelText').textContent = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('certPreview');
                img.src = e.target.result;
                img.classList.remove('d-none');
                document.getElementById('previewPlaceholder').classList.add('d-none');
                sessionStorage.setItem('draft_cert_image', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    }

    function saveDraftToSession(e, targetUrl) {
        e.preventDefault();
        saveParticipantsToSession();
        const eventName = document.getElementById('eventName').value;
        const certPrefix = document.getElementById('certNumberPrefix').value;
        
        if (eventName) sessionStorage.setItem('draft_event_name', eventName);
        if (certPrefix) sessionStorage.setItem('draft_cert_prefix', certPrefix);

        window.location.href = targetUrl;
    }

    function saveParticipantsToSession() {
        const rows = document.querySelectorAll("#modalGridTable tbody tr");
        const list = [];
        rows.forEach(tr => {
            const name = tr.querySelector('.p-name')?.value || '';
            const identity = tr.querySelector('.p-identity')?.value || '';
            const agency = tr.querySelector('.p-agency')?.value || '';
            const role = tr.querySelector('.p-role')?.value || '';
            if (name || identity) {
                list.push({ name, identity, agency, role });
            }
        });
        sessionStorage.setItem('draft_participants', JSON.stringify(list));
        updateDataCount();
    }

    function restoreParticipantsFromSession() {
        const raw = sessionStorage.getItem('draft_participants');
        if (!raw) {
            updateDataCount();
            return;
        }

        const list = JSON.parse(raw);
        if (!list || list.length === 0) {
            updateDataCount();
            return;
        }

        const tbody = document.getElementById("modalGridBody");
        tbody.innerHTML = '';
        mRowIndex = 0;

        list.forEach((item, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td><input type="text" name="participants[${index}][name]" class="form-control form-control-sm p-name" value="${item.name || ''}" placeholder="Nama Lengkap" oninput="saveParticipantsToSession()"></td>
                <td><input type="text" name="participants[${index}][identity_number]" class="form-control form-control-sm p-identity" value="${item.identity || ''}" placeholder="NIK/NIP" oninput="saveParticipantsToSession()"></td>
                <td><input type="text" name="participants[${index}][agency]" class="form-control form-control-sm p-agency" value="${item.agency || 'Diskominfo'}" oninput="saveParticipantsToSession()"></td>
                <td><input type="text" name="participants[${index}][role]" class="form-control form-control-sm p-role" value="${item.role || 'Peserta'}" oninput="saveParticipantsToSession()"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm rounded-circle px-2" onclick="removeModalRow(this)">✕</button></td>
            `;
            tbody.appendChild(tr);
            mRowIndex = index;
        });

        updateDataCount();
    }

    function addModalRow() {
        mRowIndex++;
        const tbody = document.getElementById("modalGridBody");
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><input type="text" name="participants[${mRowIndex}][name]" class="form-control form-control-sm p-name" placeholder="Nama Lengkap" oninput="saveParticipantsToSession()"></td>
            <td><input type="text" name="participants[${mRowIndex}][identity_number]" class="form-control form-control-sm p-identity" placeholder="NIK/NIP" oninput="saveParticipantsToSession()"></td>
            <td><input type="text" name="participants[${mRowIndex}][agency]" class="form-control form-control-sm p-agency" value="Diskominfo" oninput="saveParticipantsToSession()"></td>
            <td><input type="text" name="participants[${mRowIndex}][role]" class="form-control form-control-sm p-role" value="Peserta" oninput="saveParticipantsToSession()"></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm rounded-circle px-2" onclick="removeModalRow(this)">✕</button></td>
        `;
        tbody.appendChild(tr);
        saveParticipantsToSession();
    }

    function removeModalRow(btn) {
        const rows = document.querySelectorAll("#modalGridTable tbody tr");
        if (rows.length > 1) {
            btn.closest("tr").remove();
            saveParticipantsToSession();
        }
    }

    function updateDataCount() {
        const rows = document.querySelectorAll("#modalGridTable tbody tr");
        let validCount = 0;
        rows.forEach(tr => {
            const name = tr.querySelector('.p-name')?.value.trim();
            if (name) validCount++;
        });
        document.getElementById('dataCount').textContent = validCount;
    }
</script>
@endsection

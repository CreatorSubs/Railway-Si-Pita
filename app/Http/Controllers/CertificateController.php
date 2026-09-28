<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function create()
    {
        return view('pages.admin.create_certificate');
    }

    public function store(Request $request)
    {
        // Handle upload template dengan aman: 
        // Jika ada file baru di-upload, simpan. Jika tidak, ambil dari input hidden atau gunakan default.
        $templatePath = null;
        if ($request->hasFile('template')) {
            $templatePath = $request->file('template')->store('certificates/templates', $this->templateDisk());
        } else {
            $templatePath = $request->input('existing_template_path') ?? $request->input('template_path') ?? 'default-template.png';
        }

        $prefix = $request->certificate_number_prefix ?? 'SERT/';
        $eventName = $request->event_name ?? 'Kegiatan';
        $issueDate = $request->issue_date ?? date('Y-m-d');

        // CASE 1: JIKA INPUT BANYAK DATA (VIA MODAL GRID / CSV)
        if ($request->has('participants') && is_array($request->participants) && count($request->participants) > 0) {
            foreach ($request->participants as $index => $p) {
                if (!empty($p['name'])) {
                    Certificate::create([
                        'certificate_number' => $prefix . strtoupper(Str::random(5)) . '-' . ($index + 1),
                        'recipient_name'     => $p['name'],
                        'recipient_identity' => !empty($p['identity_number']) ? $p['identity_number'] : '-',
                        'institution'        => $p['agency'] ?? 'Diskominfo',
                        'event_name'         => $eventName,
                        'role'               => $p['role'] ?? 'Peserta',
                        'issue_date'         => $issueDate,
                        'template_path'      => $templatePath,
                        'qr_token'           => Str::uuid()->toString(),
                    ]);
                }
            }
        } 
        // CASE 2: JIKA INPUT SATUAN
        else {
            Certificate::create([
                'certificate_number' => $prefix . strtoupper(Str::random(6)),
                'recipient_name'     => $request->recipient_name ?? 'Peserta',
                'recipient_identity' => $request->recipient_identity ?? '-',
                'institution'        => $request->institution ?? 'Diskominfo',
                'event_name'         => $eventName,
                'role'               => $request->role ?? 'Peserta',
                'issue_date'         => $issueDate,
                'template_path'      => $templatePath,
                'qr_token'           => Str::uuid()->toString(),
            ]);
        }

        // REDIRECT LANGSUNG KE HALAMAN CEK SERTIFIKAT
        return redirect()->route('admin.certificate.check')->with('success', 'Sertifikat berhasil dibuat!');
    }

    public function editor(int $id)
    {
        $certificate = Certificate::findOrFail($id);
        $templateUrl = $certificate->template_path
            ? Storage::disk($this->templateDisk())->url($certificate->template_path)
            : null;

        return view('pages.admin.editor', compact('certificate', 'templateUrl'));
    }

    public function updatePositions(Request $request, int $id)
    {
        $positions = $request->validate([
            'pos_number_x' => ['required', 'integer', 'between:0,800'],
            'pos_number_y' => ['required', 'integer', 'between:0,565'],
            'pos_name_x' => ['required', 'integer', 'between:0,800'],
            'pos_name_y' => ['required', 'integer', 'between:0,565'],
            'pos_qr_x' => ['required', 'integer', 'between:0,800'],
            'pos_qr_y' => ['required', 'integer', 'between:0,565'],
        ]);

        $certificate = Certificate::findOrFail($id);
        $certificate->update($positions);

        return redirect()->route('admin.certificate.editor', $id)
            ->with('success', 'Posisi sertifikat berhasil disimpan.');
    }

    public function showAll(Request $request)
    {
        $certificates = Certificate::query()
            ->when($request->query('token'), fn ($query, $token) => $query->where('qr_token', $token))
            ->latest()
            ->paginate(10);

        return view('pages.admin.check_certificate', compact('certificates'));
    }

    public function download(int $id)
    {
        $certificate = Certificate::findOrFail($id);
        $disk = Storage::disk($this->templateDisk());
        $templateDataUri = null;

        if ($certificate->template_path && $disk->exists($certificate->template_path)) {
            $mimeType = $disk->mimeType($certificate->template_path) ?: 'image/png';
            $templateDataUri = 'data:'.$mimeType.';base64,'.base64_encode($disk->get($certificate->template_path));
        }

        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
        $qrCode = base64_encode((new Writer($renderer))->writeString(
            route('public.certificate.check', ['token' => $certificate->qr_token]),
        ));

        return Pdf::loadView('pages.pdf_template', compact('certificate', 'templateDataUri', 'qrCode'))
            ->setPaper('a4', 'landscape')
            ->download('certificate-'.$certificate->id.'.pdf');
    }

    // METHOD HAPUS SERTIFIKAT
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        // Hapus file template jika ada
        $disk = Storage::disk($this->templateDisk());
        if ($certificate->template_path && $disk->exists($certificate->template_path)) {
            $disk->delete($certificate->template_path);
        }

        $certificate->delete();

        return redirect()->route('admin.certificate.check')->with('success', 'Sertifikat berhasil dihapus!');
    }

    private function templateDisk(): string
    {
        return config('filesystems.default') === 'local'
            ? 'public'
            : config('filesystems.default');
    }
}
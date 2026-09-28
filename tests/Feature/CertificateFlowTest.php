<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CertificateFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_editor_positions_persist_and_certificate_downloads_as_pdf(): void
    {
        $this->actingAs(User::factory()->create());

        $certificate = Certificate::create([
            'certificate_number' => 'SERT/001/2026',
            'recipient_name' => 'Test Recipient',
            'event_name' => 'Test Event',
            'issue_date' => '2026-09-28',
            'template_path' => 'template.png',
            'qr_token' => (string) Str::uuid(),
        ]);

        $this->get(route('admin.certificate.editor', $certificate))
            ->assertOk();

        $positions = [
            'pos_number_x' => 210,
            'pos_number_y' => 45,
            'pos_name_x' => 260,
            'pos_name_y' => 230,
            'pos_qr_x' => 680,
            'pos_qr_y' => 440,
        ];

        $this->post(route('admin.certificate.update_positions', $certificate), $positions)
            ->assertRedirect(route('admin.certificate.editor', $certificate));

        $this->assertDatabaseHas('certificates', $positions + ['id' => $certificate->id]);

        $this->get(route('admin.certificate.download', $certificate))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
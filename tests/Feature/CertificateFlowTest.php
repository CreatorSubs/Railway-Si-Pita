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

    public function test_user_toggle_status_via_patch(): void
    {
        $owner = User::factory()->create(['email' => 'admin@diskominfo.go.id']);
        $admin = User::factory()->create(['email' => 'staff@diskominfo.go.id', 'is_active' => true]);

        $this->actingAs($owner);

        $response = $this->patch(route('admin.user.toggle', $admin->id));
        $response->assertRedirect();

        $this->assertFalse((bool) $admin->fresh()->is_active);
    }

    public function test_certificate_store_persists_created_by(): void
    {
        $user = User::factory()->create(['email' => 'creator@diskominfo.go.id']);
        $this->actingAs($user);

        $response = $this->post(route('admin.certificate.store'), [
            'certificate_number_prefix' => 'TEST/',
            'event_name' => 'Workshop IT',
            'issue_date' => '2026-09-29',
            'recipient_name' => 'Budi Santoso',
            'recipient_identity' => '3201234567890001',
            'institution' => 'Diskominfo',
            'role' => 'Peserta',
        ]);

        $response->assertRedirect(route('admin.certificate.check'));

        $this->assertDatabaseHas('certificates', [
            'recipient_name' => 'Budi Santoso',
            'recipient_identity' => '3201234567890001',
            'created_by' => 'creator@diskominfo.go.id',
        ]);
    }

    public function test_owner_history_renders_recipient_details(): void
    {
        $owner = User::factory()->create(['email' => 'admin@diskominfo.go.id']);
        $this->actingAs($owner);

        Certificate::create([
            'certificate_number' => 'CERT/HIST/01',
            'recipient_name' => 'Siti Nurhaliza',
            'recipient_identity' => '1987654321',
            'event_name' => 'Seminar Keamanan Informasi',
            'issue_date' => '2026-09-29',
            'template_path' => 'template.png',
            'qr_token' => (string) Str::uuid(),
            'created_by' => 'admin@diskominfo.go.id',
        ]);

        $response = $this->get(route('admin.certificate.history'));
        $response->assertOk();
        $response->assertSee('Siti Nurhaliza');
        $response->assertSee('1987654321');
        $response->assertSee('Seminar Keamanan Informasi');
    }

    public function test_public_certificate_search_by_name(): void
    {
        Certificate::create([
            'certificate_number' => 'CERT/PUB/01',
            'recipient_name' => 'Ahmad Dahlan',
            'recipient_identity' => '1122334455',
            'event_name' => 'Pelatihan AI',
            'issue_date' => '2026-09-29',
            'template_path' => 'template.png',
            'qr_token' => (string) Str::uuid(),
        ]);

        $response = $this->get(route('certificate.search', ['name' => 'Ahmad Dahlan']));
        $response->assertOk();
        $response->assertSee('Ahmad Dahlan');
        $response->assertSee('CERT/PUB/01');
    }
}

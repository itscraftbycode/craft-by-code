<?php

namespace Tests\Feature;

use App\Models\Agreement;
use App\Models\AgreementAuditLog;
use App\Models\AgreementTemplate;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAgreementManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = new User([
            'name' => 'Legal Admin',
            'email' => 'legal-admin@craftbycode.test',
            'password' => Hash::make('AdminSec#2026!'),
        ]);
        $this->admin->role = 'admin';
        $this->admin->is_active = true;
        $this->admin->save();

        $this->regularUser = new User([
            'name' => 'Public Visitor',
            'email' => 'client@external.test',
            'password' => Hash::make('ClientPass#2026'),
        ]);
        $this->regularUser->role = 'user';
        $this->regularUser->is_active = true;
        $this->regularUser->save();
    }

    public function test_guest_and_client_are_strictly_blocked_from_all_agreement_routes(): void
    {
        $agreement = Agreement::firstOrFail();

        $protectedRoutes = [
            route('admin.agreements.index'),
            route('admin.agreements.archive'),
            route('admin.agreements.audit-logs'),
            route('admin.agreements.settings'),
            route('admin.agreements.create'),
            route('admin.agreements.show', $agreement->id),
            route('admin.agreements.edit', $agreement->id),
            route('admin.agreements.download', $agreement->id),
            route('admin.agreements.stream', $agreement->id),
            route('admin.agreements.templates.index'),
        ];

        // 1. Unauthenticated Guests must be redirected to admin login
        foreach ($protectedRoutes as $url) {
            $response = $this->get($url);
            $response->assertRedirect(route('admin.login'));
        }

        // 2. Authenticated non-admin clients must be blocked
        foreach ($protectedRoutes as $url) {
            $response = $this->actingAs($this->regularUser)->get($url);
            $this->assertTrue(
                $response->isRedirect(route('admin.login')) || $response->status() === 403,
                "Failed asserting that client is blocked from {$url}"
            );
        }
    }

    public function test_public_frontend_pages_do_not_expose_any_agreement_links_or_actions(): void
    {
        $frontendRoutes = ['/', '/services', '/contact', '/portfolio', '/about', '/blog'];

        foreach ($frontendRoutes as $uri) {
            $response = $this->get($uri);
            $response->assertOk();
            $response->assertDontSee('/admin/agreements', false);
            $response->assertDontSee('agreement_number', false);
            $response->assertDontSee('e-Stamp', false);
        }
    }

    public function test_admin_can_access_agreement_dashboard_and_view_metrics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.agreements.index'));
        $response->assertOk();
        $response->assertSee('Digital Agreements &amp; Contracts', false);
        $response->assertSee('Strict Admin-Only', false);
        $response->assertSee('Total', false);
        $response->assertSee('Drafts', false);
        $response->assertSee('Pending Sign', false);
        $response->assertSee('Fully Signed', false);
        $response->assertSee('e-Stamp Pending', false);
    }

    public function test_admin_can_load_agreement_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.agreements.create'));
        $response->assertOk();
        $response->assertSee('Generate New Agreement');
        $response->assertSee('Legal Terms, Clauses &amp; Covenants', false);
        $response->assertDontSee('Undefined constant');

        $directResponse = $this->actingAs($this->admin)->get('/admin/agreements/create');
        $directResponse->assertOk();
        $directResponse->assertSee('Generate New Agreement');
    }

    public function test_admin_can_create_new_agreement_with_audit_trail_and_pdf(): void
    {
        Storage::fake('local');

        $template = AgreementTemplate::where('is_active', true)->firstOrFail();

        $payload = [
            'template_id' => $template->id,
            'title' => 'Master Custom Application Agreement',
            'client_name' => 'Victoria Sterling',
            'client_email' => 'vsterling@sterlingventures.com',
            'client_company' => 'Sterling Ventures LLC',
            'client_phone' => '+1 (555) 789-0123',
            'client_address' => '700 California St, San Francisco, CA',
            'contract_value' => '35000.00',
            'currency' => 'USD',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(60)->toDateString(),
            'scope_of_work' => "1. Cloud SaaS MVP\n2. Real-time billing engine\n3. DevOps automation",
            'payment_terms' => "• 50% Upfront\n• 50% On Handover",
            'content' => "Agreement between CraftByCode and Sterling Ventures LLC.",
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.agreements.store'), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('agreements', [
            'client_email' => 'vsterling@sterlingventures.com',
            'client_company' => 'Sterling Ventures LLC',
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        $agreement = Agreement::where('client_email', 'vsterling@sterlingventures.com')->firstOrFail();

        // Check Audit Log
        $this->assertDatabaseHas('agreement_audit_logs', [
            'agreement_id' => $agreement->id,
            'action' => 'created',
            'user_id' => $this->admin->id,
        ]);

        // Check PDF was saved to private storage
        $this->assertNotNull($agreement->pdf_path);
        $this->assertNotNull($agreement->pdf_hash);
        $this->assertStringContainsString('private/agreements/pdfs', $agreement->pdf_path);
    }

    public function test_admin_can_apply_cryptographic_digital_signature(): void
    {
        Storage::fake('local');

        $agreement = Agreement::where('status', 'draft')->firstOrFail();

        $signPayload = [
            'signatory_role' => 'Managing Director & Legal Officer',
            'notes' => 'Reviewed against master corporate legal guidelines.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.agreements.sign', $agreement->id), $signPayload);
        $response->assertSessionHas('success');

        $agreement->refresh();
        $this->assertNotNull($agreement->digital_signature_hash);
        $this->assertNotNull($agreement->signed_at);
        $this->assertEquals('Managing Director & Legal Officer', $agreement->digital_signature_metadata['signatory_role']);
        $this->assertTrue(in_array($agreement->status, ['fully_signed', 'active']));

        // Check Audit Log
        $this->assertDatabaseHas('agreement_audit_logs', [
            'agreement_id' => $agreement->id,
            'action' => 'digitally_signed',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_attach_estamp_duty_certificate(): void
    {
        Storage::fake('local');

        $agreement = Agreement::firstOrFail();

        $estampPayload = [
            'estamp_number' => 'IN-DL109827364501Z',
            'estamp_state' => 'Delaware',
            'estamp_amount' => '750.00',
            'estamp_issued_at' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.agreements.estamp', $agreement->id), $estampPayload);
        $response->assertSessionHas('success');

        $agreement->refresh();
        $this->assertEquals('IN-DL109827364501Z', $agreement->estamp_number);
        $this->assertEquals('Delaware', $agreement->estamp_state);
        $this->assertEquals('750.00', (string) $agreement->estamp_amount);

        // Check Audit Log
        $this->assertDatabaseHas('agreement_audit_logs', [
            'agreement_id' => $agreement->id,
            'action' => 'estamp_attached',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_download_and_stream_private_agreement_pdf(): void
    {
        Storage::fake('local');

        $agreement = Agreement::firstOrFail();

        // 1. Download
        $downloadResponse = $this->actingAs($this->admin)->get(route('admin.agreements.download', $agreement->id));
        $downloadResponse->assertOk();
        $this->assertEquals('application/pdf', $downloadResponse->headers->get('content-type'));
        $this->assertStringContainsString('attachment', (string) $downloadResponse->headers->get('content-disposition'));

        // 2. Stream
        $streamResponse = $this->actingAs($this->admin)->get(route('admin.agreements.stream', $agreement->id));
        $streamResponse->assertOk();
        $this->assertEquals('application/pdf', $streamResponse->headers->get('content-type'));
        $this->assertStringContainsString('inline', (string) $streamResponse->headers->get('content-disposition'));

        // Check Audit Logs
        $this->assertDatabaseHas('agreement_audit_logs', [
            'agreement_id' => $agreement->id,
            'action' => 'pdf_downloaded',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_view_archive_audit_trail_and_templates(): void
    {
        // Archive
        $archiveResponse = $this->actingAs($this->admin)->get(route('admin.agreements.archive'));
        $archiveResponse->assertOk();
        $archiveResponse->assertSee('Legal Document Archive', false);

        // Audit Trail
        $auditResponse = $this->actingAs($this->admin)->get(route('admin.agreements.audit-logs'));
        $auditResponse->assertOk();
        $auditResponse->assertSee('Security &amp; Legal Audit Trail', false);

        // Templates List
        $templatesResponse = $this->actingAs($this->admin)->get(route('admin.agreements.templates.index'));
        $templatesResponse->assertOk();
        $templatesResponse->assertSee('Contract &amp; Agreement Templates', false);
        $templatesResponse->assertSee('Master Software Development Agreement', false);

        // Legal Settings
        $settingsResponse = $this->actingAs($this->admin)->get(route('admin.agreements.settings'));
        $settingsResponse->assertOk();
        $settingsResponse->assertSee('Legal Entity &amp; Contract Settings', false);
    }

    public function test_admin_can_delete_agreement(): void
    {
        $agreement = Agreement::firstOrFail();
        $agrId = $agreement->id;

        $response = $this->actingAs($this->admin)->delete(route('admin.agreements.destroy', $agrId));
        $response->assertRedirect(route('admin.agreements.index'));

        $this->assertDatabaseMissing('agreements', ['id' => $agrId]);
    }

    public function test_admin_can_purge_dummy_agreements(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.agreements.clear-dummy'));
        $response->assertRedirect(route('admin.agreements.index'));

        $this->assertDatabaseMissing('agreements', ['agreement_number' => 'AGR-2026-0001']);
        $this->assertDatabaseMissing('agreements', ['agreement_number' => 'AGR-2026-0002']);
    }
}


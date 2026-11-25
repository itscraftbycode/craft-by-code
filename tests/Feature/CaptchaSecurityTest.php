<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CaptchaSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_submits_successfully_when_captcha_not_configured(): void
    {
        config(['services.captcha.secret_key' => '']);

        $response = $this->post('/contact', [
            'name' => 'Alice Walker',
            'email' => 'alice@company.com',
            'subject' => 'Project Inquiry',
            'message' => 'Hello, we would like to discuss building an MVP application with your team.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_inquiries', [
            'email' => 'alice@company.com',
        ]);
    }

    public function test_contact_form_blocks_submission_when_captcha_is_enforced_and_token_missing(): void
    {
        config([
            'services.captcha.secret_key' => 'test_secret_key_123',
            'services.captcha.test_enforcement' => true,
        ]);

        $response = $this->post('/contact', [
            'name' => 'John Doe',
            'email' => 'john@company.com',
            'message' => 'Inquiry message here that meets the minimum length requirements.',
        ]);

        $response->assertSessionHasErrors(['captcha']);
        $this->assertDatabaseMissing('contact_inquiries', [
            'email' => 'john@company.com',
        ]);
    }

    public function test_contact_form_succeeds_when_captcha_verification_passes(): void
    {
        config([
            'services.captcha.driver' => 'recaptcha',
            'services.captcha.secret_key' => 'test_secret_key_123',
            'services.captcha.test_enforcement' => true,
        ]);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
            ], 200),
        ]);

        $response = $this->post('/contact', [
            'name' => 'Verified User',
            'email' => 'verified@company.com',
            'message' => 'Valid message meeting length requirements for enterprise projects.',
            'g-recaptcha-response' => 'valid_token_xyz',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_inquiries', [
            'email' => 'verified@company.com',
        ]);
    }

    public function test_contact_form_blocks_submission_when_upstream_captcha_rejects_token(): void
    {
        config([
            'services.captcha.driver' => 'turnstile',
            'services.captcha.secret_key' => 'test_turnstile_secret_456',
            'services.captcha.test_enforcement' => true,
        ]);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $response = $this->post('/contact', [
            'name' => 'Suspicious User',
            'email' => 'bad@company.com',
            'message' => 'Valid message meeting length requirements for enterprise projects.',
            'cf-turnstile-response' => 'fake_invalid_token',
        ]);

        $response->assertSessionHasErrors(['captcha']);
        $this->assertDatabaseMissing('contact_inquiries', [
            'email' => 'bad@company.com',
        ]);
    }

    public function test_contact_form_blocks_submission_when_recaptcha_v3_score_is_below_threshold(): void
    {
        config([
            'services.captcha.driver' => 'recaptcha_v3',
            'services.captcha.secret_key' => 'test_v3_secret_789',
            'services.captcha.min_score' => 0.5,
            'services.captcha.test_enforcement' => true,
        ]);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.1, // Bot-like low score
                'action' => 'contact_submit',
            ], 200),
        ]);

        $response = $this->post('/contact', [
            'name' => 'Bot User',
            'email' => 'bot@spammer.com',
            'message' => 'Spam message meeting length requirements for test verification.',
            'g-recaptcha-response' => 'low_score_token',
        ]);

        $response->assertSessionHasErrors(['captcha']);
        $this->assertDatabaseMissing('contact_inquiries', [
            'email' => 'bot@spammer.com',
        ]);
    }
}


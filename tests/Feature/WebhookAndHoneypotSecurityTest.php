<?php

namespace Tests\Feature;

use App\Security\WebhookVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookAndHoneypotSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_verifier_validates_correct_hmac_and_rejects_tampered_payload(): void
    {
        $payload = json_encode(['event' => 'payment.succeeded', 'amount' => 5000]);
        $secret = 'super_secret_webhook_key_12345';
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue(WebhookVerifier::verify($payload, $signature, $secret));
        $this->assertTrue(WebhookVerifier::verify($payload, 'sha256='.$signature, $secret));

        // Tampered payload must fail
        $tamperedPayload = json_encode(['event' => 'payment.succeeded', 'amount' => 50]);
        $this->assertFalse(WebhookVerifier::verify($tamperedPayload, $signature, $secret));

        // Wrong secret must fail
        $this->assertFalse(WebhookVerifier::verify($payload, $signature, 'wrong_key'));
    }

    public function test_webhook_verifier_detects_replay_attacks_via_timestamp_drift(): void
    {
        $payload = json_encode(['event' => 'invoice.paid']);
        $secret = 'webhook_secret_key';
        $currentTime = time();
        $payloadToSign = $currentTime.'.'.$payload;
        $signature = hash_hmac('sha256', $payloadToSign, $secret);

        // Valid timestamp
        $this->assertTrue(WebhookVerifier::verify($payload, $signature, $secret, $currentTime, 300));

        // Expired timestamp (older than tolerance)
        $expiredTime = $currentTime - 400;
        $oldPayloadToSign = $expiredTime.'.'.$payload;
        $oldSignature = hash_hmac('sha256', $oldPayloadToSign, $secret);
        $this->assertFalse(WebhookVerifier::verify($payload, $oldSignature, $secret, $expiredTime, 300));
    }

    public function test_contact_form_honeypot_silently_drops_spambot_submissions(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Spam Bot',
            'email' => 'bot@spammer.org',
            'message' => 'Buy cheap pills now http://spam.com',
            'contact_hp_website' => 'http://spam.com', // Filled honeypot
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('contact_inquiries', [
            'email' => 'bot@spammer.org',
        ]);
    }
}


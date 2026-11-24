<?php

namespace Database\Seeders;

use App\Models\Agreement;
use App\Models\AgreementAuditLog;
use App\Models\AgreementTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class AgreementSeeder extends Seeder
{
    public function run(): void
    {
        // Don't auto-seed sample agreements into database in production/local unless explicitly requested or running tests
        if (! app()->environment('testing') && ! env('SEED_DUMMY_AGREEMENTS', false)) {
            return;
        }

        $admin = User::where('role', 'admin')->first() ?? User::first();
        if (! $admin) {
            return;
        }

        $templates = AgreementTemplate::all()->keyBy('slug');

        $sampleAgreements = [
            [
                'agreement_number' => 'AGR-2026-0001',
                'template_id' => $templates['master-software-development-agreement']?->id,
                'title' => 'Master SaaS Development Agreement',
                'client_name' => 'Sarah Jenkins',
                'client_email' => 'sjenkins@nexushealth.io',
                'client_company' => 'Nexus Health Solutions Inc',
                'client_phone' => '+1 (415) 555-0142',
                'client_address' => '500 Howard Street, Suite 300, San Francisco, CA 94105',
                'contract_value' => 45000.00,
                'currency' => 'USD',
                'status' => 'active',
                'scope_of_work' => "1. Architecture & HIPAA Compliance Blueprint\n2. Patient Care Portal & Telehealth Video API\n3. Real-time Scheduling Engine & Stripe Invoicing\n4. AWS ECS Cloud Hardening & CI/CD Pipeline",
                'payment_terms' => "• 30% Upon Signing\n• 30% Architecture & Alpha Build\n• 30% UAT & Staging Handover\n• 10% Production Launch Handover",
                'content' => "This Master Software Development Agreement is entered into between CraftByCode and Nexus Health Solutions Inc. CraftByCode will deliver the complete telehealth portal in compliance with HIPAA specifications.\n\nAll custom source code and architectural documentation will transfer unconditionally to Nexus Health Solutions Inc upon completion.",
                'start_date' => now()->subDays(30)->toDateString(),
                'end_date' => now()->addDays(90)->toDateString(),
                'signed_at' => now()->subDays(28),
                'finalized_at' => now()->subDays(25),
                'digital_signature_hash' => hash('sha256', 'AGR-2026-0001-NEXUS-SIGNED-' . config('app.key')),
                'digital_signature_metadata' => [
                    'signed_by' => 'Admin Craft By Code',
                    'signatory_role' => 'Managing Director, CraftByCode',
                    'signer_ip' => '127.0.0.1',
                    'timestamp' => now()->subDays(28)->toIso8601String(),
                    'algorithm' => 'HMAC-SHA256',
                ],
                'estamp_number' => 'IN-DL982348712903H',
                'estamp_state' => 'Delaware',
                'estamp_amount' => 500.00,
                'estamp_issued_at' => now()->subDays(27),
                'created_by' => $admin->id,
            ],
            [
                'agreement_number' => 'AGR-2026-0002',
                'template_id' => $templates['startup-mvp-sprint-contract']?->id,
                'title' => 'Startup MVP Sprint Agreement',
                'client_name' => 'Arthur Vance',
                'client_email' => 'arthur@pulsepay.global',
                'client_company' => 'PulsePay Global FinTech',
                'client_phone' => '+1 (212) 555-8821',
                'client_address' => '120 Broadway, 18th Floor, New York, NY 10271',
                'contract_value' => 28000.00,
                'currency' => 'USD',
                'status' => 'estamp_pending',
                'scope_of_work' => "1. Cross-border FX payment wireframing & UX\n2. Multi-currency wallet microservices\n3. KYC / AML identity verification gateway integration\n4. SOC2 readiness audit & test suite",
                'payment_terms' => "• 50% Kickoff Mobilization Deposit\n• 50% Production Handover & Repository Transfer",
                'content' => "Sprint engagement agreement for PulsePay Global. CraftByCode guarantees a rapid 6-week turnaround for the MVP fintech wallet engine.",
                'start_date' => now()->subDays(10)->toDateString(),
                'end_date' => now()->addDays(32)->toDateString(),
                'signed_at' => now()->subDays(2),
                'digital_signature_hash' => hash('sha256', 'AGR-2026-0002-PULSEPAY-SIGNED-' . config('app.key')),
                'digital_signature_metadata' => [
                    'signed_by' => 'Lead Engineer',
                    'signatory_role' => 'Principal Architect, CraftByCode',
                    'signer_ip' => '127.0.0.1',
                    'timestamp' => now()->subDays(2)->toIso8601String(),
                    'algorithm' => 'HMAC-SHA256',
                ],
                'created_by' => $admin->id,
            ],
            [
                'agreement_number' => 'AGR-2026-0003',
                'template_id' => $templates['website-maintenance-sla-retainer']?->id,
                'title' => 'Website Maintenance & SLA Retainer',
                'client_name' => 'Elena Rostova',
                'client_email' => 'elena@auraliving.com',
                'client_company' => 'Aura Living Designs',
                'client_phone' => '+44 20 7946 0912',
                'client_address' => '25 Baker Street, Marylebone, London, W1U 8EQ',
                'contract_value' => 18000.00,
                'currency' => 'USD',
                'status' => 'pending_signature',
                'scope_of_work' => "1. 24/7 Automated Uptime Monitoring (< 1 min ping)\n2. Weekly framework security patching & dependency upgrades\n3. Off-site encrypted hourly cloud database backups\n4. 20 hours/month dedicated engineering feature enhancements",
                'payment_terms' => "Monthly invoice of $1,500 due on the 1st of each month (12 months commitment).",
                'content' => "Service Level Agreement (SLA) contract guaranteeing 99.9% uptime and < 1 hour incident response SLA for Aura Living Designs.",
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'created_by' => $admin->id,
            ],
            [
                'agreement_number' => 'AGR-2026-0004',
                'template_id' => $templates['master-software-development-agreement']?->id,
                'title' => 'Custom Enterprise Supply-Chain Platform',
                'client_name' => 'Marcus Brody',
                'client_email' => 'mbrody@dataflowtech.com',
                'client_company' => 'DataFlow Technologies Ltd',
                'client_phone' => '+1 (312) 555-7733',
                'client_address' => '233 S Wacker Dr, Chicago, IL 60606',
                'contract_value' => 62000.00,
                'currency' => 'USD',
                'status' => 'draft',
                'scope_of_work' => "Enterprise logistics route optimization, IoT sensor telemetry tracking, multi-tenant inventory control, and automated billing portal.",
                'payment_terms' => "Milestone-based progress billing across 4 equal sprint phases.",
                'content' => "Draft custom software development agreement prepared for review by DataFlow Technologies Ltd legal counsel.",
                'start_date' => now()->addDays(15)->toDateString(),
                'end_date' => now()->addDays(180)->toDateString(),
                'created_by' => $admin->id,
            ],
        ];

        foreach ($sampleAgreements as $agrData) {
            $agreement = Agreement::updateOrCreate(
                ['agreement_number' => $agrData['agreement_number']],
                $agrData
            );

            // Seed initial audit log if none exist
            if ($agreement->auditLogs()->count() === 0) {
                AgreementAuditLog::create([
                    'agreement_id' => $agreement->id,
                    'user_id' => $admin->id,
                    'action' => 'created',
                    'details' => "Agreement {$agreement->agreement_number} created in status {$agreement->status}.",
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Admin Seed Initializer',
                    'created_at' => $agreement->created_at ?? now(),
                ]);

                if ($agreement->isFullySigned() || $agreement->status === 'estamp_pending') {
                    AgreementAuditLog::create([
                        'agreement_id' => $agreement->id,
                        'user_id' => $admin->id,
                        'action' => 'digitally_signed',
                        'details' => "Cryptographic digital signature applied by {$admin->name}.",
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'Admin Seed Initializer',
                        'created_at' => $agreement->signed_at ?? now(),
                    ]);
                }

                if ($agreement->hasEstamp()) {
                    AgreementAuditLog::create([
                        'agreement_id' => $agreement->id,
                        'user_id' => $admin->id,
                        'action' => 'estamp_attached',
                        'details' => "E-Stamp certificate {$agreement->estamp_number} attached for state {$agreement->estamp_state}.",
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'Admin Seed Initializer',
                        'created_at' => $agreement->estamp_issued_at ?? now(),
                    ]);
                }
            }
        }
    }
}


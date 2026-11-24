<?php

namespace App\Console\Commands;

use App\Models\Agreement;
use App\Models\AgreementAuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearDummyAgreementsCommand extends Command
{
    protected $signature = 'agreements:clear-dummy {--all : Delete all agreements, not just sample/dummy ones}';

    protected $description = 'Delete dummy and sample agreements and their audit logs from the database';

    public function handle(): int
    {
        $this->info('Searching for sample/dummy agreements...');

        if ($this->option('all')) {
            $query = Agreement::query();
        } else {
            $query = Agreement::whereIn('agreement_number', [
                'AGR-2026-0001',
                'AGR-2026-0002',
                'AGR-2026-0003',
                'AGR-2026-0004',
            ])->orWhereIn('id', [1, 2, 3, 4]);
        }

        $agreements = $query->get();
        $count = $agreements->count();

        if ($count === 0) {
            $this->info('No dummy agreements found.');
            return self::SUCCESS;
        }

        foreach ($agreements as $agr) {
            $num = $agr->agreement_number;
            if ($agr->pdf_path && Storage::disk('local')->exists($agr->pdf_path)) {
                Storage::disk('local')->delete($agr->pdf_path);
            }
            if ($agr->estamp_certificate_path && Storage::disk('local')->exists($agr->estamp_certificate_path)) {
                Storage::disk('local')->delete($agr->estamp_certificate_path);
            }
            $agr->auditLogs()->delete();
            $agr->delete();
            $this->line(" - Deleted {$num} (ID #{$agr->id})");
        }

        // Clean any orphaned audit logs
        AgreementAuditLog::whereNull('agreement_id')
            ->orWhereNotIn('agreement_id', Agreement::pluck('id'))
            ->delete();

        $this->info("Successfully deleted {$count} dummy agreement(s) and cleared audit logs.");

        return self::SUCCESS;
    }
}


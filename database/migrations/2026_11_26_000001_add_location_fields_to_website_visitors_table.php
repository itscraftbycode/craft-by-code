<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('website_visitors')) {
            Schema::table('website_visitors', function (Blueprint $table) {
                if (! Schema::hasColumn('website_visitors', 'country')) {
                    $table->string('country', 100)->nullable()->after('referer')->index();
                }
                if (! Schema::hasColumn('website_visitors', 'country_code')) {
                    $table->string('country_code', 10)->nullable()->after('country')->index();
                }
                if (! Schema::hasColumn('website_visitors', 'city')) {
                    $table->string('city', 100)->nullable()->after('country_code')->index();
                }
                if (! Schema::hasColumn('website_visitors', 'region')) {
                    $table->string('region', 100)->nullable()->after('city');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('website_visitors')) {
            Schema::table('website_visitors', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('website_visitors', 'region')) {
                    $columns[] = 'region';
                }
                if (Schema::hasColumn('website_visitors', 'city')) {
                    $columns[] = 'city';
                }
                if (Schema::hasColumn('website_visitors', 'country_code')) {
                    $columns[] = 'country_code';
                }
                if (Schema::hasColumn('website_visitors', 'country')) {
                    $columns[] = 'country';
                }
                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};

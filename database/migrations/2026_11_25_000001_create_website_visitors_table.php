<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('website_visitors')) {
            Schema::create('website_visitors', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_hash', 64)->unique()->index();
                $table->string('ip_address', 45)->nullable()->index();
                $table->string('user_agent', 255)->nullable();
                $table->string('landing_page', 255)->nullable();
                $table->string('referer', 255)->nullable();
                $table->unsignedInteger('hits')->default(1);
                $table->timestamp('first_visited_at')->useCurrent()->index();
                $table->timestamp('last_visited_at')->useCurrent()->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('website_visitors');
    }
};


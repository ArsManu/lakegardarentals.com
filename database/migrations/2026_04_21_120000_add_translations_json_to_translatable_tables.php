<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pages', 'apartments', 'faqs', 'testimonials', 'amenities'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->json('translations')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['pages', 'apartments', 'faqs', 'testimonials', 'amenities'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('translations');
            });
        }
    }
};

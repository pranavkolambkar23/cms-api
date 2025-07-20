<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // If slug and summary columns do not exist, you can add them like this:
            if (!Schema::hasColumn('articles', 'slug')) {
                $table->string('slug')->nullable()->after('title');
            } else {
                $table->string('slug')->nullable()->change();
            }

            if (!Schema::hasColumn('articles', 'summary')) {
                $table->text('summary')->nullable()->after('content');
            } else {
                $table->text('summary')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'slug')) {
                $table->dropColumn('slug');
            }

            if (Schema::hasColumn('articles', 'summary')) {
                $table->dropColumn('summary');
            }
        });
    }
};
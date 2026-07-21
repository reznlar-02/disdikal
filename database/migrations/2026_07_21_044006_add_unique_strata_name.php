<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('strata', function (Blueprint $table) {
            $table->unique('strata');
        });
    }

    public function down(): void
    {
        Schema::table('strata', function (Blueprint $table) {
            $table->dropUnique(['strata']);
        });
    }
};

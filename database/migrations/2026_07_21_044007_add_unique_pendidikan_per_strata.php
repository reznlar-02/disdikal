<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendidikan', function (Blueprint $table) {
            $table->unique(['idstrata', 'pendidikan']);
        });
    }

    public function down(): void
    {
        Schema::table('pendidikan', function (Blueprint $table) {
            $table->dropUnique(['idstrata', 'pendidikan']);
        });
    }
};

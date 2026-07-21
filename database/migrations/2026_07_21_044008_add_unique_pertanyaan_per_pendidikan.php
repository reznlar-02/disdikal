<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // `pertanyaan` is a TEXT column, so MySQL needs an explicit key prefix
    // length to index it — Laravel's Blueprint::unique() doesn't support
    // that, hence the raw statement here.
    public function up(): void
    {
        DB::statement('ALTER TABLE pertanyaansurvei ADD UNIQUE unique_pendidikan_pertanyaan (idpendidikan, pertanyaan(255))');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pertanyaansurvei DROP INDEX unique_pendidikan_pertanyaan');
    }
};

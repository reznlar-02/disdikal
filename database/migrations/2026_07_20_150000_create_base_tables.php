<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel inti aplikasi (strata, pendidikan, pertanyaansurvei, kunjungan,
 * jawabansurvei) sebelumnya hanya ada lewat import manual db_sql/*.sql,
 * bukan lewat migration Laravel. Migration ini menyamakan skemanya supaya
 * `php artisan migrate` bisa membangun database dari nol di server manapun.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Guard tiap tabel: di server baru (Railway dkk) tabel ini belum
        // ada sama sekali, tapi di lokal sudah ada dari import db_sql/*.sql
        // lebih dulu — jadi migration ini harus aman dijalankan di kedua kondisi.
        if (! Schema::hasTable('strata')) {
            Schema::create('strata', function (Blueprint $table) {
                $table->increments('idstrata');
                $table->string('strata', 250);
            });
        }

        if (! Schema::hasTable('pendidikan')) {
            Schema::create('pendidikan', function (Blueprint $table) {
                $table->increments('idpendidikan');
                $table->unsignedInteger('idstrata');
                $table->string('pendidikan', 250);
            });
        }

        if (! Schema::hasTable('pertanyaansurvei')) {
            Schema::create('pertanyaansurvei', function (Blueprint $table) {
                $table->increments('idpertanyaansurvei');
                $table->unsignedInteger('idpendidikan');
                $table->text('pertanyaan');
            });
        }

        if (! Schema::hasTable('kunjungan')) {
            Schema::create('kunjungan', function (Blueprint $table) {
                $table->increments('idkunjungan');
                $table->string('nama', 250);
                $table->string('nrp', 250);
                $table->string('email', 250);
                $table->unsignedInteger('idpendidikan');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('jawabansurvei')) {
            Schema::create('jawabansurvei', function (Blueprint $table) {
                $table->increments('idjawabansurvei');
                $table->unsignedInteger('idpertanyaansurvei');
                $table->string('jawaban', 250);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jawabansurvei');
        Schema::dropIfExists('kunjungan');
        Schema::dropIfExists('pertanyaansurvei');
        Schema::dropIfExists('pendidikan');
        Schema::dropIfExists('strata');
    }
};

<?php

namespace Database\Seeders;

use App\Models\StrataModel;
use Illuminate\Database\Seeder;

class StrataSeeder extends Seeder
{
    /**
     * Strata jenjang pendidikan TNI AL — nilai baku, tidak berubah per angkatan.
     * Pendidikan dan Pertanyaan Survei ditambahkan manual lewat panel admin
     * sesuai kebutuhan tiap kali ada angkatan yang menyelesaikan pendidikan.
     */
    public function run(): void
    {
        foreach (['TAMTAMA', 'BINTARA', 'PERWIRA'] as $strata) {
            StrataModel::firstOrCreate(['strata' => $strata]);
        }
    }
}

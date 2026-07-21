<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;



Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('login', 'login');
    Route::post('loginproses', 'loginproses');
    Route::get('logout', 'logout');

    // surveikepuasan
    Route::get('surveikepuasan', 'surveikepuasan');
    Route::post('surveikepuasansimpan', 'surveikepuasansimpan');

    Route::get('strukturorganisasi', 'strukturorganisasi');
});

Route::middleware(['auth'])->controller(AdminController::class)->group(function () {
    Route::get('admin', 'dashboard');

    // strata 
    Route::get('admin/strata', 'strata');
    Route::post('admin/stratasimpan', 'stratasimpan');
    Route::get('admin/strataedit/{id}', 'strataedit');
    Route::put('admin/strataupdate/{id}', 'strataupdate');
    Route::delete('admin/stratahapus/{id}', 'stratahapus');

    // pendidikan
    Route::get('admin/pendidikan', 'pendidikan');
    Route::post('admin/pendidikansimpan', 'pendidikansimpan');
    Route::get('admin/pendidikanedit/{id}', 'pendidikanedit');
    Route::put('admin/pendidikanupdate/{id}', 'pendidikanupdate');
    Route::delete('admin/pendidikanhapus/{id}', 'pendidikanhapus');

    // pertanyaansurvei
    Route::get('admin/pertanyaansurvei', 'pertanyaansurvei');
    Route::post('admin/pertanyaansurveisimpan', 'pertanyaansurveisimpan');
    Route::get('admin/pertanyaansurveiedit/{id}', 'pertanyaansurveiedit');
    Route::put('admin/pertanyaansurveiupdate/{id}', 'pertanyaansurveiupdate');
    Route::delete('admin/pertanyaansurveihapus/{id}', 'pertanyaansurveihapus');

    // hasilsurvei
    Route::get('admin/hasilsurvei', 'hasilsurvei');
    Route::get('admin/hasilsurvei/download-pdf', 'downloadHasilSurveiPdf');
    Route::get('admin/hasilsurvei/download-csv', 'downloadHasilSurveiCsv');
});

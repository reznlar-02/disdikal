<?php

namespace App\Http\Controllers;

use App\Models\JawabansurveiModel;
use App\Models\KunjunganModel;
use App\Models\PendidikanModel;
use App\Models\PertanyaansurveiModel;
use App\Models\StrataModel;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class HomeController extends Controller
{

    public function index()
    {
        return view('home');
    }

    public function login()
    {
        return view('login');
    }

    public function loginproses(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.");
        }

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect('admin')->with('success', 'Login berhasil');
        }

        RateLimiter::hit($throttleKey, 60);
        return back()->with('error', 'Email atau password salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logout berhasil');
    }

    // strukturorganisasi
    public function strukturorganisasi()
    {
        return view('strukturorganisasi');
    }

    // survei kepuasan
    public function surveikepuasan()
    {
        $data['strata'] = StrataModel::all();
        return view('surveikepuasan', $data);
    }

    public function getpendidikan($id)
    {
        $data = PendidikanModel::where('idstrata', $id)->get();
        return response()->json($data);
    }

    public function getpertanyaan($id)
    {
        $question = PertanyaansurveiModel::where('idpendidikan', $id)->get();
        return response()->json($question);
    }

    public function surveikepuasansimpan(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'angkatan' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:kunjungan,email'],
            'idpendidikan' => ['required', 'integer', 'exists:pendidikan,idpendidikan'],
            'cf-turnstile-response' => ['required', new Turnstile($request->ip())],
        ]);

        $answers = $request->input('jawaban');

        $pertanyaanValid = PertanyaansurveiModel::where('idpendidikan', $validated['idpendidikan'])
            ->pluck('idpertanyaansurvei')->all();

        if (
            !is_array($answers)
            || count($answers) !== count($pertanyaanValid)
        ) {
            return back()->withInput()->withErrors([
                'jawaban' => 'Mohon jawab seluruh pertanyaan survei.',
            ]);
        }

        foreach ($answers as $idpertanyaansurvei => $jawaban) {
            $isPertanyaanValid = in_array((int) $idpertanyaansurvei, $pertanyaanValid, true);
            $isJawabanValid    = in_array($jawaban, AdminController::KATEGORI_JAWABAN, true);
            if (!$isPertanyaanValid || !$isJawabanValid) {
                return back()->withInput()->withErrors([
                    'jawaban' => 'Data jawaban tidak valid.',
                ]);
            }
        }

        try {
            $duplicate = DB::transaction(function () use ($validated, $answers) {
                // Re-check uniqueness inside the transaction, with a locking
                // read, to close the race window between the validation check
                // above and this write: two concurrent submissions with the
                // same email must not both pass.
                if (KunjunganModel::where('email', $validated['email'])->lockForUpdate()->exists()) {
                    return true;
                }

                KunjunganModel::create([
                    'nama' => $validated['nama'],
                    'angkatan' => $validated['angkatan'],
                    'email' => $validated['email'],
                    'idpendidikan' => $validated['idpendidikan'],
                ]);

                foreach ($answers as $idpertanyaansurvei => $jawaban) {
                    JawabansurveiModel::create([
                        'idpertanyaansurvei' => $idpertanyaansurvei,
                        'jawaban' => $jawaban,
                    ]);
                }

                return false;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Pengaman terakhir: kalau dua request benar-benar bersamaan
            // (mis. "send group in parallel" di Burp) lolos bareng dari
            // pengecekan lockForUpdate() di atas karena baris emailnya
            // belum ada sama sekali saat keduanya membaca, unique index
            // di kolom kunjungan.email tetap akan menolak salah satunya
            // di level database. Tangkap itu sebagai duplikat, bukan crash.
            if ($e->getCode() === '23000') {
                $duplicate = true;
            } else {
                throw $e;
            }
        }

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'email' => 'Email ini sudah pernah digunakan untuk mengisi survey.',
            ]);
        }

        return redirect('/')->with('success', 'Survey berhasil disimpan');
    }
}

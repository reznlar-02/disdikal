# TODO - Survey: Email hanya bisa isi 1 kali

- [x] Update validasi backend agar email unik (1x pengisian) — `unique:kunjungan,email` di request validation, plus unique index di DB (migration `2026_07_20_184124_add_unique_email_to_kunjungan_table`)
- [x] Tambah guard backend jika email sudah pernah isi — recheck dengan `lockForUpdate()` di dalam `DB::transaction()` supaya submission bersamaan (race condition) tidak lolos berbarengan; jawaban & kunjungan hanya disimpan kalau email belum ada, keduanya atomic dalam satu transaksi (tidak ada lagi jawaban tersimpan sebelum email dicek)
- [x] Pastikan feedback error email duplicate tampil di frontend — sudah ada `@error('email')` di `surveikepuasan.blade.php`
- [x] Hapus endpoint API test sementara dari routes/api.php — stub `/api/user` (sanctum default) dihapus, tidak dipakai di mana pun
- [ ] Uji sintaks PHP — PHP CLI tidak tersedia di environment ini untuk `php -l`; sudah direview manual, perlu diverifikasi di environment dengan PHP
- [ ] Uji submit pertama berhasil, submit kedua email sama ditolak — perlu dijalankan manual (butuh DB + `php artisan migrate` untuk menerapkan unique index baru)

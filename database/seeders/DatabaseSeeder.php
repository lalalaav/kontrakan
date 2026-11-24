<?php

namespace Database\Seeders;

use App\Models\BiayaUpgrade;
use App\Models\KontrakSewa;
use App\Models\LaporanKerusakan;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\UnitKontrakan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create(['name' => 'Admin Pemilik', 'email' => 'admin@kontrakan.test', 'phone' => '081200000001', 'password' => Hash::make('password'), 'role' => 'admin', 'is_active' => true]);
        User::create(['name' => 'Petugas Operasional', 'email' => 'petugas@kontrakan.test', 'phone' => '081200000002', 'password' => Hash::make('password'), 'role' => 'petugas', 'is_active' => true]);

        $penyewa = [];
        foreach (range(1, 12) as $i) {
            $penyewa[] = User::create(['name' => 'Penyewa Contoh '.$i, 'email' => "penyewa{$i}@kontrakan.test", 'phone' => '0812000'.str_pad((string) (1000 + $i), 5, '0', STR_PAD_LEFT), 'password' => Hash::make('password'), 'role' => 'penyewa', 'is_active' => true]);
        }

        $lokasi = [];
        foreach (range(1, 9) as $i) {
            $lokasi[] = Lokasi::create(['nama' => 'Lokasi '.$i, 'alamat' => 'Jl. Contoh Nomor '.$i.', Karawang', 'maps_url' => 'https://maps.google.com/?q=Karawang']);
        }

        // 11 unit tersebar di 9 lokasi
        $units = [];
        foreach (range(1, 11) as $i) {
            $units[] = UnitKontrakan::create([
                'lokasi_id' => $lokasi[($i - 1) % 9]->id,
                'nomor_unit' => 'U-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'tarif_sewa' => 1500000 + ($i * 100000),
                'jumlah_kamar' => 2,
                'luas_bangunan' => 36,
                'fasilitas' => 'Air, listrik, kamar mandi, parkir',
                'status' => 'kosong',
                'izin_hewan' => false,
            ]);
        }

        // 30 transaksi: bulan berurutan, 1 kontrak aktif per unit, sisanya riwayat selesai
        foreach (range(1, 30) as $i) {
            $unit = $units[($i - 1) % 11];
            $mulai = now()->subMonths(12)->startOfMonth()->addMonths($i - 1);
            $selesai = $i <= 19;

            $kontrak = KontrakSewa::create([
                'unit_kontrakan_id' => $unit->id,
                'user_id' => $penyewa[($i - 1) % count($penyewa)]->id,
                'tanggal_mulai' => $mulai->toDateString(),
                'tanggal_selesai' => $mulai->copy()->addYear()->toDateString(),
                'nominal_sewa' => $unit->tarif_sewa,
                'status' => $selesai ? 'selesai' : 'aktif',
                'disetujui_oleh' => $admin->id,
            ]);

            Pembayaran::create([
                'kontrak_sewa_id' => $kontrak->id,
                'periode' => $mulai->format('Y-m'),
                'jumlah' => $unit->tarif_sewa,
                'tanggal_jatuh_tempo' => $mulai->copy()->day(10)->toDateString(),
                'tanggal_bayar' => $i % 3 ? $mulai->copy()->day(5)->toDateString() : null,
                'status' => $i % 3 ? 'lunas' : 'menunggu',
                'diverifikasi_oleh' => $i % 3 ? $admin->id : null,
            ]);
        }

        // unit yang punya kontrak aktif ditandai terisi
        UnitKontrakan::whereHas('kontrak', fn ($q) => $q->where('status', 'aktif'))->update(['status' => 'terisi']);
        UnitKontrakan::where('id', 5)->update(['status' => 'perbaikan']);

        foreach (range(1, 8) as $i) {
            $status = ['baru', 'diproses', 'selesai'][$i % 3];
            LaporanKerusakan::create([
                'unit_kontrakan_id' => $units[($i - 1) % 11]->id,
                'user_id' => $penyewa[($i - 1) % count($penyewa)]->id,
                'judul' => 'Kerusakan '.$i,
                'deskripsi' => 'Kendala fasilitas unit pada pemeriksaan rutin.',
                'status' => $status,
                'biaya_material' => $status === 'selesai' ? 150000 * $i : 0,
                'biaya_upah' => $status === 'selesai' ? 100000 * $i : 0,
            ]);
        }

        foreach (range(1, 5) as $i) {
            BiayaUpgrade::create([
                'unit_kontrakan_id' => $units[$i - 1]->id,
                'judul' => 'Upgrade fasilitas '.$i,
                'keterangan' => 'Perbaikan dan peningkatan fasilitas unit.',
                'jumlah' => 750000 * $i,
                'tanggal' => now()->subDays($i * 10)->toDateString(),
            ]);
        }
    }
}

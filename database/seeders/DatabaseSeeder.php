<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::create([
            'name' => 'Admin BantuIn',
            'email' => 'admin@bantuin.org',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $fundraiser = User::create([
            'name' => 'Rina Sari',
            'email' => 'rina@mail.com',
            'password' => Hash::make('rina123'),
            'role' => 'fundraiser',
        ]);

        $budi = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@mail.com',
            'password' => Hash::make('budi123'),
            'role' => 'fundraiser',
        ]);

        $siti = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@mail.com',
            'password' => Hash::make('siti123'),
            'role' => 'fundraiser',
        ]);

        $donatur = User::create([
            'name' => 'Donatur Peduli',
            'email' => 'donatur@mail.com',
            'password' => Hash::make('donatur123'),
            'role' => 'donatur',
        ]);

        // Additional Donatur
        $ayu = User::create([
            'name' => 'Ayu Pramesti',
            'email' => 'ayu@mail.com',
            'password' => Hash::make('ayu123'),
            'role' => 'donatur',
        ]);

        $rizal = User::create([
            'name' => 'Rizal H.',
            'email' => 'rizal@mail.com',
            'password' => Hash::make('rizal123'),
            'role' => 'donatur',
        ]);

        // 2. Seed Campaigns
        $campaignData = [
            [
                'title' => 'Sekolah Impian Anak Papua',
                'cat' => 'pendidikan',
                'img' => 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?auto=format&fit=crop&w=800&q=60',
                'location' => 'Jayapura, Papua',
                'fundraiser' => 'Yayasan Harapan Timur',
                'desc' => 'Membangun ruang kelas layak di daerah terpencil Papua agar anak-anak bisa belajar dengan nyaman dan semangat.',
                'target' => 300000000,
                'collected' => 180000000,
                'days' => 42,
                'status' => 'active',
                'created_by' => $fundraiser->id,
            ],
            [
                'title' => 'Air Bersih untuk Desa Nusa Penida',
                'cat' => 'air bersih',
                'img' => 'https://images.unsplash.com/photo-1509099836639-18ba1795216d?auto=format&fit=crop&w=800&q=60',
                'location' => 'Nusa Penida, Bali',
                'fundraiser' => 'Relawan Air Indonesia',
                'desc' => 'Membangun sumur bor dan instalasi air bersih untuk 5 desa di Nusa Penida yang kekurangan air bersih sepanjang tahun.',
                'target' => 500000000,
                'collected' => 300000000,
                'days' => 28,
                'status' => 'active',
                'created_by' => $budi->id,
            ],
            [
                'title' => 'Operasi Bibir Sumbing Gratis',
                'cat' => 'kesehatan',
                'img' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?auto=format&fit=crop&w=800&q=60',
                'location' => 'Kupang, NTT',
                'fundraiser' => 'Dokter Peduli Nusantara',
                'desc' => 'Membiayai operasi bibir sumbing gratis bagi anak-anak dari keluarga kurang mampu agar dapat tumbuh dengan kepercayaan diri.',
                'target' => 150000000,
                'collected' => 95000000,
                'days' => 14,
                'status' => 'active',
                'created_by' => $siti->id,
            ],
            [
                'title' => 'Tanaman 10.000 Mangrove',
                'cat' => 'lingkungan',
                'img' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=800&q=60',
                'location' => 'Semarang, Jawa Tengah',
                'fundraiser' => 'Komunitas Hijau Indonesia',
                'desc' => 'Menghijaukan kembali pesisir pantai yang rusak dengan menanam 10.000 pohon mangrove demi ekosistem yang sehat.',
                'target' => 80000000,
                'collected' => 48000000,
                'days' => 60,
                'status' => 'active',
                'created_by' => $fundraiser->id,
            ],
            [
                'title' => 'Dapur Umum Lansia Terpadu',
                'cat' => 'komunitas',
                'img' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=800&q=60',
                'location' => 'Jakarta Pusat, DKI Jakarta',
                'fundraiser' => 'Peduli Lansia Jakarta',
                'desc' => 'Menyediakan makanan bergizi setiap hari bagi ratusan lansia terlantar di Jakarta yang tidak memiliki keluarga.',
                'target' => 100000000,
                'collected' => 62000000,
                'days' => 21,
                'status' => 'active',
                'created_by' => $budi->id,
            ],
            [
                'title' => 'Rumah Singgah Anak Kanker',
                'cat' => 'kesehatan',
                'img' => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=800&q=60',
                'location' => 'Bandung, Jawa Barat',
                'fundraiser' => 'Rumah Kasih Anak Bangsa',
                'desc' => 'Menyediakan tempat tinggal sementara yang nyaman bagi anak penderita kanker yang datang dari luar kota untuk berobat.',
                'target' => 600000000,
                'collected' => 420000000,
                'days' => 33,
                'status' => 'active',
                'created_by' => $siti->id,
            ],
            // Pending Campaigns
            [
                'title' => 'Beasiswa Mahasiswa Berprestasi',
                'cat' => 'pendidikan',
                'img' => 'https://images.unsplash.com/photo-1523050854-01023f1fc119?auto=format&fit=crop&w=800&q=60',
                'location' => 'Surabaya, Jawa Timur',
                'fundraiser' => 'Yayasan Pendidikan Bangsa',
                'desc' => 'Program beasiswa untuk 10 mahasiswa berprestasi dari keluarga kurang mampu.',
                'target' => 60000000,
                'collected' => 0,
                'days' => 60,
                'status' => 'pending',
                'created_by' => $fundraiser->id,
            ],
            [
                'title' => 'Kebun Gizi Komunitas',
                'cat' => 'kesehatan',
                'img' => 'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=800&q=60',
                'location' => 'Malang, Jawa Timur',
                'fundraiser' => 'Gizi Sehat Nusantara',
                'desc' => 'Pembuatan kebun gizi komunitas untuk mengatasi stunting di kelurahan.',
                'target' => 20000000,
                'collected' => 0,
                'days' => 45,
                'status' => 'pending',
                'created_by' => $budi->id,
            ],
        ];

        $campaigns = [];
        foreach ($campaignData as $c) {
            $campaigns[] = Campaign::create(array_merge($c, [
                'start_date' => now(),
                'pic_name' => 'Penanggung Jawab ' . $c['title'],
                'pic_phone' => '081234567890',
                'social' => '@bantuin_id',
                'budget' => [
                    ['label' => 'Logistik Operasional', 'amount' => $c['target'] * 0.7],
                    ['label' => 'Publikasi & Administrasi', 'amount' => $c['target'] * 0.3],
                ],
            ]));
        }

        // 3. Seed Donations
        // Donatur Peduli donations
        Donation::create([
            'user_id' => $donatur->id,
            'campaign_id' => $campaigns[0]->id, // Sekolah Impian Anak Papua
            'amount' => 500000,
            'payment_method' => 'bca',
            'message' => 'Semoga pembangunan berjalan lancar dan bermanfaat!',
            'is_anonymous' => false,
            'status' => 'success',
        ]);

        Donation::create([
            'user_id' => $donatur->id,
            'campaign_id' => $campaigns[1]->id, // Nusa Penida
            'amount' => 250000,
            'payment_method' => 'gopay',
            'message' => 'Semoga air bersih segera mengalir di Nusa Penida.',
            'is_anonymous' => false,
            'status' => 'success',
        ]);

        Donation::create([
            'user_id' => $donatur->id,
            'campaign_id' => $campaigns[5]->id, // Rumah Singgah
            'amount' => 1000000,
            'payment_method' => 'dana',
            'message' => 'Tetap semangat adik-adik pejuang kanker!',
            'is_anonymous' => false,
            'status' => 'success',
        ]);

        // Ayu Pramesti donations
        Donation::create([
            'user_id' => $ayu->id,
            'campaign_id' => $campaigns[0]->id,
            'amount' => 350000,
            'payment_method' => 'qris',
            'message' => 'Semoga bisa lekas belajar dengan nyaman.',
            'is_anonymous' => false,
            'status' => 'success',
        ]);

        // Rizal H. donations
        Donation::create([
            'user_id' => $rizal->id,
            'campaign_id' => $campaigns[1]->id,
            'amount' => 1200000,
            'payment_method' => 'mandiri',
            'message' => 'Bantuan untuk air bersih.',
            'is_anonymous' => false,
            'status' => 'success',
        ]);
    }
}

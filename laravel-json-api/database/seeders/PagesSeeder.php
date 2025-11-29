<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Page;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Tentang Kami',
                'slug' => 'tentang-kami',
                'content' => '<section style="margin-bottom:24px"><h2>Profil RSIZ Muhammadiyah Jatibarang</h2><p>Rumah Sakit Islam Zam Zam Muhammadiyah Jatibarang (RSIZ) merupakan institusi layanan kesehatan yang berkomitmen menghadirkan pelayanan berkualitas, ramah, dan berkesinambungan bagi masyarakat. Berawal dari klinik layanan dasar, RSIZ berkembang menjadi rumah sakit modern dengan dukungan dokter spesialis, tenaga kesehatan profesional, dan teknologi penunjang yang memadai.</p></section><section style="margin-bottom:24px"><h3>Sejarah Singkat</h3><p>Didirikan oleh Persyarikatan Muhammadiyah dengan semangat dakwah kemanusiaan, RSIZ tumbuh melalui kolaborasi berbagai pihak dan amanah masyarakat. Sejak awal berdiri hingga kini, RSIZ konsisten meningkatkan mutu pelayanan, memperluas fasilitas, dan menguatkan budaya keselamatan pasien.</p></section><section style="margin-bottom:24px"><h3>Visi</h3><p><em>Menjadi rumah sakit islami yang terpercaya, unggul dalam mutu, dan berorientasi pada keselamatan pasien.</em></p></section><section style="margin-bottom:24px"><h3>Misi</h3><ul><li>Menyelenggarakan pelayanan kesehatan profesional, cepat, dan beretika.</li><li>Mengembangkan SDM kompeten berlandaskan nilai-nilai Islami.</li><li>Menerapkan manajemen mutu dan keselamatan pasien secara berkesinambungan.</li><li>Memberikan layanan yang inklusif, empatik, serta berorientasi pada kebutuhan pasien dan keluarga.</li></ul></section><section style="margin-bottom:24px"><h3>Nilai-Nilai</h3><ul><li>Integritas</li><li>Empati</li><li>Profesional</li><li>Akuntabel</li><li>Kolaboratif</li></ul></section>',
                'is_published' => true,
            ],
            [
                'title' => 'Visi & Misi',
                'slug' => 'visi-misi',
                'content' => '<h2>Visi</h2><p>Menjadi rumah sakit rujukan terpercaya.</p><h2>Misi</h2><ul><li>Pelayanan prima</li><li>Inovasi</li></ul>',
                'is_published' => true,
            ],
            [
                'title' => 'Kebijakan Privasi',
                'slug' => 'kebijakan-privasi',
                'content' => '<p>Kami menjaga privasi data pasien.</p>',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $p) {
            Page::updateOrCreate(['slug' => $p['slug']], $p);
        }
    }
}

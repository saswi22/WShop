<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                'name' => 'General Consultation',
                'description' => 'Konsultasi umum dengan dokter profesional untuk evaluasi kesehatan',
                'details' => 'Layanan konsultasi komprehensif untuk berbagai keluhan kesehatan',
                'icon' => 'fa-stethoscope',
            ],
            [
                'name' => 'Emergency Care',
                'description' => 'Layanan darurat 24/7 untuk penanganan kasus-kasus mendesak',
                'details' => 'Tim medis siap 24 jam untuk kasus-kasus darurat dan life-threatening',
                'icon' => 'fa-ambulance',
            ],
            [
                'name' => 'Laboratory Test',
                'description' => 'Tes laboratorium lengkap dengan peralatan modern',
                'details' => 'Pemeriksaan lab darah, urine, kultur dan tes diagnostik lainnya',
                'icon' => 'fa-flask',
            ],
            [
                'name' => 'Medical Imaging',
                'description' => 'Pemeriksaan imaging termasuk X-Ray, CT Scan, dan MRI',
                'details' => 'Fasilitas imaging canggih dengan radiolog berpengalaman',
                'icon' => 'fa-x-ray',
            ],
            [
                'name' => 'Surgery Service',
                'description' => 'Layanan operasi dengan dokter spesialis berpengalaman',
                'details' => 'Operasi dengan teknologi terkini dan anestesi aman',
                'icon' => 'fa-surgical',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}

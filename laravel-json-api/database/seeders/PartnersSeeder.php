<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            [
                'name' => 'BPJS Kesehatan',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/84/BPJS_Kesehatan_logo.svg/200px-BPJS_Kesehatan_logo.svg.png',
                'website' => 'https://bpjs-kesehatan.go.id',
                'description' => 'Badan Penyelenggara Jaminan Sosial Kesehatan',
                'is_active' => true,
                'order' => 1
            ],
            [
                'name' => 'Allianz',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Allianz_logo.svg/200px-Allianz_logo.svg.png',
                'website' => 'https://www.allianz.co.id',
                'description' => 'Asuransi Kesehatan Internasional',
                'is_active' => true,
                'order' => 2
            ],
            [
                'name' => 'Prudential',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f5/Prudential_Financial_logo.svg/200px-Prudential_Financial_logo.svg.png',
                'website' => 'https://www.prudential.co.id',
                'description' => 'Asuransi Jiwa dan Kesehatan',
                'is_active' => true,
                'order' => 3
            ],
            [
                'name' => 'AXA Mandiri',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/AXA_Logo.svg/200px-AXA_Logo.svg.png',
                'website' => 'https://www.axa-mandiri.co.id',
                'description' => 'Asuransi Kesehatan',
                'is_active' => true,
                'order' => 4
            ],
            [
                'name' => 'Manulife',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7d/Manulife_logo.svg/200px-Manulife_logo.svg.png',
                'website' => 'https://www.manulife.co.id',
                'description' => 'Asuransi Kesehatan dan Jiwa',
                'is_active' => true,
                'order' => 5
            ],
            [
                'name' => 'Sinarmas',
                'logo' => 'https://via.placeholder.com/200x80?text=Sinarmas',
                'website' => 'https://www.sinarmas.co.id',
                'description' => 'Asuransi Kesehatan',
                'is_active' => true,
                'order' => 6
            ]
        ];

        foreach ($partners as $partner) {
            Partner::create($partner);
        }
    }
}

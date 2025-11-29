<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Bed;
use Illuminate\Database\Seeder;

class FacilitiesSeeder extends Seeder
{
    public function run()
    {
        $fac1 = Facility::create([
            'name' => 'Kelas VIP',
            'description' => 'Ruang perawatan kelas VIP dengan fasilitas lengkap',
            'image' => 'https://via.placeholder.com/800x400?text=VIP+Ward'
        ]);

        Bed::create([ 'facility_id' => $fac1->id, 'name' => 'Tempat Tidur A1', 'class' => 'VIP', 'quantity' => 2, 'image' => 'https://via.placeholder.com/300?text=Bed+A1' ]);
        Bed::create([ 'facility_id' => $fac1->id, 'name' => 'Tempat Tidur A2', 'class' => 'VIP', 'quantity' => 1, 'image' => 'https://via.placeholder.com/300?text=Bed+A2' ]);

        $fac2 = Facility::create([
            'name' => 'Kelas Standard',
            'description' => 'Ruang perawatan standar nyaman dan bersih',
            'image' => 'https://via.placeholder.com/800x400?text=Standard+Ward'
        ]);

        Bed::create([ 'facility_id' => $fac2->id, 'name' => 'Tempat Tidur B1', 'class' => 'Standard', 'quantity' => 4, 'image' => 'https://via.placeholder.com/300?text=Bed+B1' ]);
        Bed::create([ 'facility_id' => $fac2->id, 'name' => 'Tempat Tidur B2', 'class' => 'Standard', 'quantity' => 6, 'image' => 'https://via.placeholder.com/300?text=Bed+B2' ]);
    }
}

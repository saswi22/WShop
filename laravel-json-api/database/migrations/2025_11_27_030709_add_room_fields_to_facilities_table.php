<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('class')->nullable()->after('name'); // kelas_3, kelas_2, kelas_1, vip
            $table->integer('capacity')->nullable()->after('class');
            $table->text('facilities')->nullable()->after('image'); // daftar fasilitas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['class', 'capacity', 'facilities']);
        });
    }
};

<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

$email = $argv[1] ?? 'rspkujtb@gmail.com';
$plain = $argv[2] ?? 'rsiz1231';
$hash = Hash::make($plain);
DB::table('users')->where('email', $email)->update(['password' => $hash, 'email_verified_at' => now()]);

echo "Updated via DB for $email\n";

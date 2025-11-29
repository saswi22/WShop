<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = $argv[1] ?? 'rspkujtb@gmail.com';
$plain = $argv[2] ?? 'rsiz1231';
$user = User::where('email', $email)->first();
if (! $user) { echo "User not found\n"; exit(1); }
$ok = Hash::check($plain, $user->password) ? 'YES' : 'NO';

echo "Hash matches? $ok\n";

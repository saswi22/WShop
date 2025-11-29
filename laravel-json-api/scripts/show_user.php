<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = $argv[1] ?? 'rspkujtb@gmail.com';
$user = User::where('email', $email)->first();
if (! $user) {
    echo "User not found: $email\n";
    exit(1);
}

echo "id: {$user->id}\n";
echo "email: {$user->email}\n";
echo "password hash: {$user->password}\n";
echo "email_verified_at: {$user->email_verified_at}\n";
echo "created_at: {$user->created_at}\n";

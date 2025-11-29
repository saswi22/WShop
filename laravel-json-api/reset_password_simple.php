<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$email = 'rspkujtb@gmail.com';
$password = 'rsiz1231';

echo "Updating password for $email...\n";

$user = User::where('email', $email)->firstOrFail();
$user->password = bcrypt($password);
$user->save();

echo "✅ Password updated successfully!\n";
echo "Email: {$user->email}\n";
echo "Password: $password\n";
echo "Hash: {$user->password}\n";

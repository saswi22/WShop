<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

$email = 'rspkujtb@gmail.com';
$password = 'rsiz1231';

echo "Updating password for $email...\n";
echo "Password (plaintext): $password\n\n";

// Use DB::table to bypass the mutator
$user = User::where('email', $email)->firstOrFail();

// Because User model has setPasswordAttribute mutator that auto-hashes,
// we just pass the plaintext password and let the mutator handle it
$user->password = $password;  // This will be auto-hashed by the mutator
$user->save();

echo "✅ Password updated successfully!\n";
echo "Email: {$user->email}\n";
echo "Password (plaintext): $password\n";
echo "Hash in DB: {$user->password}\n";

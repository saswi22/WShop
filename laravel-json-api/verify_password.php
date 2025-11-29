<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'rspkujtb@gmail.com';
$password = 'rsiz1231';

$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found!\n";
    exit(1);
}

echo "User found:\n";
echo "ID: {$user->id}\n";
echo "Name: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Password hash in DB: {$user->password}\n";
echo "\nTesting password: $password\n";

if (Hash::check($password, $user->password)) {
    echo "✅ Password is CORRECT!\n";
} else {
    echo "❌ Password is WRONG!\n";
    echo "\nCreating new hash for password '$password':\n";
    $newHash = Hash::make($password);
    echo "New hash: $newHash\n";
    
    // Update password
    $user->password = $newHash;
    $user->save();
    echo "\n✅ Password updated successfully!\n";
}

<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

$email = 'rspkujtb@gmail.com';
$password = 'rsiz1231';

echo "=== Testing Login ===\n\n";

// 1. Check if user exists
$user = User::where('email', $email)->first();
if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found:\n";
echo "   ID: {$user->id}\n";
echo "   Name: {$user->name}\n";
echo "   Email: {$user->email}\n";
echo "   Password Hash: {$user->password}\n\n";

// 2. Check password
echo "Testing password: '$password'\n";
if (Hash::check($password, $user->password)) {
    echo "✅ Password matches!\n\n";
} else {
    echo "❌ Password does NOT match!\n";
    echo "Regenerating hash...\n";
    $user->password = Hash::make($password);
    $user->save();
    echo "✅ Password updated!\n\n";
}

// 3. Test Auth attempt
echo "Testing Auth::attempt()...\n";
if (Auth::attempt(['email' => $email, 'password' => $password])) {
    echo "✅ Auth::attempt() SUCCESS!\n";
    $authenticatedUser = Auth::user();
    echo "   Authenticated as: {$authenticatedUser->name}\n";
} else {
    echo "❌ Auth::attempt() FAILED!\n";
    echo "This is the issue causing login to fail.\n";
}

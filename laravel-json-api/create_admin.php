<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Creating New Admin User ===\n\n";

// Delete old user if exists
$oldUser = App\Models\User::where('email', 'rspkujtb@gmail.com')->first();
if ($oldUser) {
    $oldUser->delete();
    echo "Deleted old user with email rspkujtb@gmail.com\n";
}

// Create new user
$user = App\Models\User::create([
    'name' => 'Admin RSIZ',
    'email' => 'rspkujtb@gmail.com',
    'password' => bcrypt('rsiz1231'),
    'email_verified_at' => now(),
]);

echo "User created successfully!\n\n";
echo "Email: " . $user->email . "\n";
echo "Password: rsiz1231\n";
echo "ID: " . $user->id . "\n";

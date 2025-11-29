<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Users in Database ===\n\n";

$users = App\Models\User::all();

if ($users->count() === 0) {
    echo "No users found in database!\n";
} else {
    foreach ($users as $user) {
        echo "ID: " . $user->id . "\n";
        echo "Name: " . $user->name . "\n";
        echo "Email: " . $user->email . "\n";
        echo "Created: " . $user->created_at . "\n";
        echo "------------------------\n";
    }
}

echo "\nTotal users: " . $users->count() . "\n";

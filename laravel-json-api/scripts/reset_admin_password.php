<?php
// scripts/reset_admin_password.php
// Usage: php scripts/reset_admin_password.php email newpassword

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if ($argc < 3) {
    echo "Usage: php reset_admin_password.php email newpassword\n";
    exit(1);
}

$email = $argv[1];
$new = $argv[2];

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// find user
$user = User::where('email', $email)->first();
if (! $user) {
    echo "User not found: $email\n";
    exit(2);
}

$user->password = Hash::make($new);
$user->email_verified_at = $user->email_verified_at ?? now();
$user->save();

echo "Password updated for $email\n";

return 0;

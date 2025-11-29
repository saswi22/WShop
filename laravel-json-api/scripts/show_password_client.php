<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
$client = DB::table('oauth_clients')->where('password_client',1)->first();
if ($client) {
    echo "ID: {$client->id}\n";
    echo "Secret: {$client->secret}\n";
    echo "Name: {$client->name}\n";
} else {
    echo "No password client found\n";
}

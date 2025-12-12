<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
$u = User::where('email', env('ADMIN_EMAIL', 'admin@example.com'))->first();
if (!$u) {
    echo "NO_USER\n";
    exit(0);
}
print_r($u->toArray());

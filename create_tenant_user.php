<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use App\Models\User;

$tenant = Tenant::find('pk1');
tenancy()->initialize($tenant);

$user = User::create([
    'name' => 'Admin PK1',
    'email' => 'admin@pk1.local',
    'password' => bcrypt('password'),
]);

$user->assignRole('admin');

echo "User created successfully!\n";
echo "Email: admin@pk1.local\n";
echo "Password: password\n";

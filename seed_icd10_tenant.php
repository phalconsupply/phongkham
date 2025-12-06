<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Lấy tenant pk1
$tenant = Tenant::find('pk1');

if (!$tenant) {
    echo "Tenant pk1 không tồn tại!\n";
    exit(1);
}

echo "Khởi tạo tenancy cho: {$tenant->id}\n";
tenancy()->initialize($tenant);

echo "Đang chạy ICD10Seeder...\n";
Artisan::call('db:seed', ['--class' => 'ICD10Seeder']);

echo Artisan::output();
echo "\nHoàn tất!\n";

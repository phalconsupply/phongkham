<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tenant = App\Models\Tenant::find('pk1');
$tenant->data = ['tenancy_db_name' => 'tenantpk1'];
$tenant->save();

echo "✅ Updated tenant pk1 with database name: tenantpk1\n";
echo "Tenant data: " . json_encode($tenant->data) . "\n";

<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create tenant
$tenant = App\Models\Tenant::create([
    'id' => 'pk1',
]);

$tenant->domains()->create([
    'domain' => 'pk1.localhost',
]);

echo "✅ Created tenant: {$tenant->id}\n";
echo "   Domain: {$tenant->domains->first()->domain}\n";
echo "   Data: " . json_encode($tenant->data) . "\n";

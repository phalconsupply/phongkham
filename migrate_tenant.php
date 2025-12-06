<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tenant = App\Models\Tenant::find('pk1');

if (!$tenant) {
    echo "Tenant not found!\n";
    exit(1);
}

echo "Running migrations for tenant: {$tenant->id}\n";

$migrateJob = new \Stancl\Tenancy\Jobs\MigrateDatabase($tenant);
app()->call([$migrateJob, 'handle']);

echo "✅ Migrations completed!\n";

// Verify
tenancy()->initialize($tenant);
$tables = DB::select("SHOW TABLES");
echo "Total tables: " . count($tables) . "\n";

foreach ($tables as $table) {
    $values = (array) $table;
    echo "  - " . reset($values) . "\n";
}

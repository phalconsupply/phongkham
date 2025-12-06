<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tenant = App\Models\Tenant::find('pk1');

if (!$tenant) {
    echo "Tenant not found!\n";
    exit(1);
}

// Use Jobs directly with app container
$createDbJob = new \Stancl\Tenancy\Jobs\CreateDatabase($tenant);
app()->call([$createDbJob, 'handle']);

echo "✅ Created database for tenant: {$tenant->id}\n";

$migrateJob = new \Stancl\Tenancy\Jobs\MigrateDatabase($tenant);
app()->call([$migrateJob, 'handle']);

echo "✅ Ran migrations for tenant: {$tenant->id}\n";

// Verify
tenancy()->initialize($tenant);
$tables = DB::select("SHOW TABLES");
echo "Tables created: " . count($tables) . "\n";

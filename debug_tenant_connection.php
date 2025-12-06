<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tenant = App\Models\Tenant::find('pk1');

if (!$tenant) {
    echo "Tenant not found!\n";
    exit(1);
}

echo "Before initialize:\n";
echo "  Connection: " . config('database.default') . "\n";
echo "  Database: " . config('database.connections.mysql.database') . "\n\n";

tenancy()->initialize($tenant);

echo "After initialize:\n";
echo "  Connection: " . config('database.default') . "\n";
echo "  Database: " . config('database.connections.mysql.database') . "\n";

// Check if database exists
$pdo = DB::connection()->getPdo();
echo "\n✅ Connected to: " . $pdo->query('SELECT DATABASE()')->fetchColumn() . "\n";

// Check if migrations table exists
try {
    $tables = DB::select("SHOW TABLES");
    echo "Tables: " . count($tables) . "\n";
    if (count($tables) > 0) {
        foreach ($tables as $table) {
            $values = (array) $table;
            echo "  - " . reset($values) . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

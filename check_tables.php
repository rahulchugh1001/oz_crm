<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== sf001_stock_transfers ===\n";
$cols = DB::select('SHOW COLUMNS FROM sf001_stock_transfers');
foreach ($cols as $c) {
    echo $c->Field . ' | ' . $c->Type . ' | Null:' . $c->Null . ' | Default:' . $c->Default . "\n";
}

echo "\n=== sf2_self_transfers ===\n";
$cols2 = DB::select('SHOW COLUMNS FROM sf2_self_transfers');
foreach ($cols2 as $c) {
    echo $c->Field . ' | ' . $c->Type . ' | Null:' . $c->Null . ' | Default:' . $c->Default . "\n";
}

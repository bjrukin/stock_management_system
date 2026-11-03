<?php
// tools/test_khalti.php
// Bootstraps the app and simulates a Khalti POST callback for debugging.

require __DIR__ . '/..' . '/vendor/autoload.php';
$app = require __DIR__ . '/..' . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

try {
    $req = Request::create('/payment/callback', 'POST', [
        'pidx' => 'P123',
        'status' => 'Completed',
        'purchase_order_id' => 'EXAM-1',
    ]);

    $result = app()->call('App\\Http\\Controllers\\Professional\\KhaltiController@callback', ['request' => $req]);

    echo "Callback invoked.\n";
    if ($result instanceof \Illuminate\Http\RedirectResponse) {
        echo "Redirected to: " . $result->getTargetUrl() . "\n";
    }
} catch (\Throwable $e) {
    echo "Error invoking callback: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

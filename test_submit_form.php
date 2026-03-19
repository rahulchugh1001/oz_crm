<?php
require 'vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Get authenticated user or use first user
$user = \App\Models\User::first();
if (!$user) {
    die("❌ No users found in database. Please create a user first.\n");
}

echo "========== SF3 PRODUCTION REPORT FORM SUBMISSION TEST ==========\n\n";

// Check transfer data
$transfer = \Illuminate\Support\Facades\DB::table('sf002_stock_transfers')
    ->where('id', 2)
    ->first();

if (!$transfer) {
    die("❌ Transfer ID 2 not found\n");
}

echo "✓ Transfer found:\n";
echo "  - ID: " . $transfer->id . "\n";
echo "  - Item ID: " . $transfer->item_id . "\n";
echo "  - Quantity: " . $transfer->quantity . "\n";
echo "  - SF3 Process: " . $transfer->sf3_process . "\n";
echo "  - Is Accepted: " . ($transfer->is_accept ? "Yes" : "No") . "\n\n";

// Check if this transfer qualifies for creating a production report
if (!$transfer->is_accept) {
    die("❌ Transfer is not accepted (is_accept = 0)\n");
}

if ($transfer->sf3_process !== 'line_2') {
    die("❌ Transfer is for a different SF3 process: " . $transfer->sf3_process . "\n");
}

// Generate valid form data
$maxQuantity = $transfer->quantity;
$validHourlyTotal = min(16, floor($maxQuantity * 0.9)); // Use 90% to be safe

echo "========== GENERATING VALID FORM DATA ==========\n";
$formData = [
    'selected_transfer_id' => $transfer->id,
    'sf3_report_date' => date('Y-m-d'),
    'sf3_shift' => 'morning',
    'sf3_set_per_hour' => round($validHourlyTotal / 12, 2),
    'sf3_total_set_shift' => $validHourlyTotal,
    'sf3_hour_8_9' => 2,
    'sf3_hour_9_10' => 2,
    'sf3_hour_10_11' => 2,
    'sf3_hour_11_12' => 2,
    'sf3_hour_12_1' => 2,
    'sf3_hour_1_2' => 1,
    'sf3_hour_2_3' => 1,
    'sf3_hour_3_4' => 1,
    'sf3_hour_4_5' => 1,
    'sf3_hour_5_6' => 1,
    'sf3_hour_6_7' => 1,
    'sf3_hour_7_8' => 1,
    'sf3_manpower' => 5,
    'sf3_staff_count' => 2,
];

$hourlyTotal = array_sum([
    $formData['sf3_hour_8_9'] ?? 0,
    $formData['sf3_hour_9_10'] ?? 0,
    $formData['sf3_hour_10_11'] ?? 0,
    $formData['sf3_hour_11_12'] ?? 0,
    $formData['sf3_hour_12_1'] ?? 0,
    $formData['sf3_hour_1_2'] ?? 0,
    $formData['sf3_hour_2_3'] ?? 0,
    $formData['sf3_hour_3_4'] ?? 0,
    $formData['sf3_hour_4_5'] ?? 0,
    $formData['sf3_hour_5_6'] ?? 0,
    $formData['sf3_hour_6_7'] ?? 0,
    $formData['sf3_hour_7_8'] ?? 0,
]);

// Add the actual set shift (auto-calculated from hourly sum)
$formData['sf3_actual_set_shift'] = $hourlyTotal;

echo "\nForm Data Generated:\n";
echo "  Transfer ID: " . $formData['selected_transfer_id'] . "\n";
echo "  Report Date: " . $formData['sf3_report_date'] . "\n";
echo "  Shift: " . $formData['sf3_shift'] . "\n";
echo "  Set/Hour: " . $formData['sf3_set_per_hour'] . "\n";
echo "  Total Set/Shift: " . $formData['sf3_total_set_shift'] . "\n";
echo "  Hourly Total: " . $hourlyTotal . " (Max allowed: " . $maxQuantity . ")\n";
echo "  Manpower: " . $formData['sf3_manpower'] . "\n";
echo "  Staff Count: " . $formData['sf3_staff_count'] . "\n\n";

// Validate
echo "========== VALIDATION ==========\n";
$valid = true;

if ($hourlyTotal > $maxQuantity) {
    echo "❌ Hourly total (" . $hourlyTotal . ") exceeds max allowed (" . $maxQuantity . ")\n";
    $valid = false;
} else {
    echo "✓ Hourly total (" . $hourlyTotal . ") is valid\n";
}

if (!$valid) {
    die("\n❌ Form validation failed\n");
}

echo "\n========== SUBMITTING FORM ==========\n";

// Authenticate the user
\Illuminate\Support\Facades\Auth::loginUsingId($user->id);

echo "✓ Authenticated as: " . $user->email . " (ID: " . $user->id . ")\n\n";

// Create a request object
$request = new \Illuminate\Http\Request();
$request->setMethod('POST');
$request->merge($formData);
$request->request->add($formData);
$request->setUserResolver(function () use ($user) {
    return $user;
});

try {
    // Get the controller
    $controller = app('App\Http\Controllers\Admin\SF003Controller');
    
    // Call the store method
    $response = $controller->storeProductionReport($request, $transfer->id);
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        echo "Response Status: " . $response->status() . "\n";
        echo "Response Message: " . ($data['message'] ?? 'No message') . "\n";
    } else {
        echo "✓ Form submitted successfully\n";
    }
    
    // Check if data was saved
    $savedReport = \Illuminate\Support\Facades\DB::table('sf3_production_reports')
        ->where('transfered_id', $transfer->id)
        ->where('sf3_process', 'line_2')
        ->latest()
        ->first();
    
    if ($savedReport) {
        echo "\n✓ Production Report Saved to Database:\n";
        echo "  - ID: " . $savedReport->id . "\n";
        echo "  - Transfer ID: " . $savedReport->transfered_id . "\n";
        echo "  - Item ID: " . $savedReport->item_id . "\n";
        echo "  - Shift: " . $savedReport->shift . "\n";
        echo "  - Report Date: " . $savedReport->report_date . "\n";
        echo "  - Actual Set/Shift: " . $savedReport->actual_set_shift . "\n";
        echo "  - Manpower: " . $savedReport->manpower_workman . "\n";
        echo "  - Staff Count: " . $savedReport->staff_count . "\n";
        echo "  - Created By: " . $savedReport->created_by . "\n";
        echo "  - Created At: " . $savedReport->created_at . "\n";
    } else {
        echo "\n⚠ Could not find saved report in database\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
}

echo "\n========== TEST COMPLETE ==========\n";

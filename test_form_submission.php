<?php
require 'vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mysqli = new mysqli(
    '127.0.0.1',
    'root',
    '',
    'oz_crm'
);

if ($mysqli->connect_error) {
    die('DB Connection failed: ' . $mysqli->connect_error);
}

echo "========== SF3 PRODUCTION REPORT FORM TEST ==========\n\n";

// Check table structure first
$columnsResult = $mysqli->query('SHOW COLUMNS FROM sf002_stock_transfers');
echo "Available columns in sf002_stock_transfers:\n";
while ($col = $columnsResult->fetch_assoc()) {
    echo "  - " . $col['Field'] . "\n";
}
echo "\n";

// Check what data exists in sf002_stock_transfers with id=2
$result = $mysqli->query('SELECT id, item_id, quantity, sf3_process, created_at FROM sf002_stock_transfers WHERE id = 2');
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Transfer ID 2 found:\n";
    echo "  Item ID: " . $row['item_id'] . "\n";
    echo "  Quantity: " . $row['quantity'] . "\n";
    echo "  SF3 Process: " . $row['sf3_process'] . "\n\n";
    
    $itemId = $row['item_id'];
    $transferId = 2;
    $pendingQty = (int) $row['quantity']; // Use quantity instead
} else {
    die("✗ No transfer found with ID 2\n");
}

// Check if any production reports exist
$countResult = $mysqli->query('SELECT COUNT(*) as cnt FROM sf3_production_reports');
$countRow = $countResult->fetch_assoc();
echo "Current SF3 Production Reports: " . $countRow['cnt'] . "\n\n";

// Check form requirements from controller validation
echo "========== FORM SUBMISSION TEST ==========\n";
echo "\nForm Fields Required:\n";
echo "- selected_transfer_id (integer, min 1)\n";
echo "- sf3_report_date (date)\n";
echo "- sf3_shift (morning|night)\n";
echo "- sf3_set_per_hour (numeric, min 0)\n";
echo "- sf3_total_set_shift (numeric, min 0)\n";
echo "- sf3_actual_set_shift (numeric, min 0) [auto-calculated]\n";
echo "- sf3_manpower (numeric, min 0)\n";
echo "- sf3_staff_count (integer, min 0)\n";
echo "- sf3_hour_8_9 through sf3_hour_7_8 (12 fields, numeric, min 0)\n";
echo "\nConstraints:\n";
echo "- All hour fields must be filled\n";
echo "- Actual Set/Shift = sum of all 12 hourly values\n";
echo "- Actual Set/Shift must NOT exceed pending quantity: " . $pendingQty . "\n\n";

// Simulate form data
echo "========== TEST SUBMISSION DATA ==========\n";
$formData = [
    'selected_transfer_id' => $transferId,
    'sf3_report_date' => date('Y-m-d'),
    'sf3_shift' => 'morning',
    'sf3_set_per_hour' => 5.00,
    'sf3_total_set_shift' => 40,
    'sf3_hour_8_9' => 3,
    'sf3_hour_9_10' => 4,
    'sf3_hour_10_11' => 4,
    'sf3_hour_11_12' => 4,
    'sf3_hour_12_1' => 3,
    'sf3_hour_1_2' => 3,
    'sf3_hour_2_3' => 2,
    'sf3_hour_3_4' => 2,
    'sf3_hour_4_5' => 2,
    'sf3_hour_5_6' => 3,
    'sf3_hour_6_7' => 2,
    'sf3_hour_7_8' => 3,
    'sf3_manpower' => 5,
    'sf3_staff_count' => 2,
];

$hourlyTotal = 0;
for ($i = 8; $i < 20; $i++) {
    $nextHour = $i + 1;
    if ($nextHour > 12) {
        $timeStr = 'sf3_hour_' . ($nextHour - 12) . '_' . ($nextHour - 11);
    } else {
        $timeStr = 'sf3_hour_' . $i . '_' . $nextHour;
    }
    $hourlyTotal += (float) ($formData[$timeStr] ?? 0);
}

echo "Generated Form Data:\n";
echo "- Transfer ID: " . $formData['selected_transfer_id'] . "\n";
echo "- Report Date: " . $formData['sf3_report_date'] . "\n";
echo "- Shift: " . $formData['sf3_shift'] . "\n";
echo "- Set Per Hour: " . $formData['sf3_set_per_hour'] . "\n";
echo "- Total Set/Shift: " . $formData['sf3_total_set_shift'] . "\n";
echo "- Hourly values sum: " . $hourlyTotal . "\n";
echo "- Manpower: " . $formData['sf3_manpower'] . "\n";
echo "- Staff Count: " . $formData['sf3_staff_count'] . "\n\n";

// Validation checks
echo "========== VALIDATION CHECKS ==========\n";
$errors = [];

if ($hourlyTotal > $pendingQty) {
    $errors[] = "✗ Hourly total (" . $hourlyTotal . ") exceeds pending quantity (" . $pendingQty . ")";
} else {
    echo "✓ Hourly total (" . $hourlyTotal . ") is within pending quantity (" . $pendingQty . ")\n";
}

if (empty($errors)) {
    echo "\n========== FORM STATUS ==========\n";
    echo "✓ All validations passed!\n";
    echo "✓ Form is ready for submission\n";
    echo "\nTo submit via browser:\n";
    echo "1. Navigate to: http://localhost:8000/admin/production-reports/sf003/production-report/2?line=l2\n";
    echo "2. Fill the form with the above values\n";
    echo "3. Click 'Save Report' button\n\n";
    
    echo "Expected database record:\n";
    echo "- Table: sf3_production_reports\n";
    echo "- created_by: (logged-in user ID)\n";
    echo "- transfered_id: " . $transferId . "\n";
    echo "- item_id: " . $itemId . "\n";
    echo "- actual_set_shift: " . round($hourlyTotal) . "\n";
} else {
    echo "\nValidation Errors:\n";
    foreach ($errors as $error) {
        echo $error . "\n";
    }
}

$mysqli->close();

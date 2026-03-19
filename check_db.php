<?php
require 'vendor/autoload.php';

$mysqli = new mysqli('127.0.0.1', 'root', '', 'oz_crm');

echo "========== CHECKING SF3 PRODUCTION REPORTS ==========\n\n";

// Get all reports
$result = $mysqli->query('SELECT * FROM sf3_production_reports ORDER BY id DESC LIMIT 10');

if ($result->num_rows === 0) {
    echo "❌ No production reports found in database\n";
} else {
    echo "✓ Found " . $result->num_rows . " production reports:\n\n";
    
    while ($row = $result->fetch_assoc()) {
        echo "Report ID: " . $row['id'] . "\n";
        echo "  - Transfer ID: " . $row['transfered_id'] . "\n";
        echo "  - Item ID: " . $row['item_id'] . "\n";
        echo "  - SF3 Process: " . $row['sf3_process'] . "\n";
        echo "  - Report Date: " . $row['report_date'] . "\n";
        echo "  - Shift: " . $row['shift'] . "\n";
        echo "  - Actual Set/Shift: " . $row['actual_set_shift'] . "\n";
        echo "  - Manpower: " . $row['manpower_workman'] . "\n";
        echo "  - Staff Count: " . $row['staff_count'] . "\n";
        echo "  - Created By: " . $row['created_by'] . "\n";
        echo "  - Created At: " . $row['created_at'] . "\n";
        echo "  - Status: " . ($row['status'] ? 'Active' : 'Inactive') . "\n";
        echo "  - Is Deleted: " . ($row['is_deleted'] ? 'Yes' : 'No') . "\n\n";
    }
}

// Count total reports
$countResult = $mysqli->query('SELECT COUNT(*) as total FROM sf3_production_reports');
$countRow = $countResult->fetch_assoc();
echo "Total records in sf3_production_reports: " . $countRow['total'] . "\n";

$mysqli->close();

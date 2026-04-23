<?php

include "../../connectionG5i.php";

$account = $_POST['account'] ?? '';

if (empty($account)) {
    echo json_encode(['error' => 'Trust is required']);
    exit;
}

$sql_genone_core = "SELECT gmvid, venue, code FROM genone_core WHERE account = ? AND versionID IN ('5.0.5', '5.0.8') AND saffire = 'false' AND OS IN ('Windows 10', 'Windows 11')";
$stmt_venues = $con->prepare($sql_genone_core);
$stmt_venues->bind_param("s", $account);
$stmt_venues->execute();
$result_venues = $stmt_venues->get_result();

$venues = [];
if ($result_venues && $result_venues->num_rows > 0) {
    while ($row = $result_venues->fetch_assoc()) {
        $venues[] = $row; // Store each venue in the array
    }
}


if ($result_venues && $result_venues->num_rows > 0) {
    $row = $result_venues->fetch_assoc();
    if ($row['code'] !== $accessCode) {
        die("Invalid access code!");
    }
}

// Return venues as JSON
header('Content-Type: application/json');
echo json_encode(['venues' => $venues]);
?>


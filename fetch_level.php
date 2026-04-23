<?php

session_start();
include "../../connectionG5i.php";


$venue = $_SESSION['venue'];


// Fetch the current levelSet
$sql = "SELECT levelSet FROM genone_core WHERE  gmvid = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $venue);
$stmt->execute();
$result = $stmt->get_result();



if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['levelSet' => $row['levelSet']]);
} else {
    echo json_encode(['error' => 'Level not found']);
}


?>
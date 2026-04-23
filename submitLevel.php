<?php
session_start();
date_default_timezone_set("Pacific/Auckland");

include "../../connectionG5i.php";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['level'])){
    $newLevel = strval(intval($_POST['level']));


    if ($newLevel < 1 || $newLevel > 472) {
        echo "Error: Invalid level number. Must be between 1-472";
        exit;
    }
    try {
        $stmt = $con->prepare("UPDATE genone_core SET levelSet = ? WHERE gmvid = ?");
        $stmt->bind_param("ss", $newLevel, $_SESSION['venue']);
        $stmt->execute();

        if($stmt->affected_rows > 0){
            header("Location: success.php");
            exit;
        } else {
            echo "No changes made. Please check the venue ID.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    } else {
    echo "Error: Level not submitted.";
}

?>
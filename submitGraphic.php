<?php
session_start();
include "../../connectionG5i.php";

/**Update "themeFile and "backgroundVideo in the database when user clicks submit */

// Check to see if the submit was pressed and venue is in the session
if (isset($_POST['selectedImage']) && isset($_SESSION['venue'])) {
    $selectedImage = trim($_POST['selectedImage']);
    $selectedVenue = trim($_SESSION['venue']);

    // Remove the .png extension
    $imageName = pathinfo($selectedImage, PATHINFO_FILENAME);
    // Sanitize input to prevent SQL injection
    $imageName = htmlspecialchars($imageName, ENT_QUOTES, 'UTF-8');

    // Set background filename based on prefix
    if (strpos($imageName, 'vegaslights') === 0) {
        $backgroundFile = 'vegaslights_background_AIR4';
    } elseif ($imageName === 'lightningcash_2L_FIXED_GRAND_AIR4'){
        $backgroundFile = 'lightningcash_2L_background_AIR4';
    } elseif ($imageName === 'lightningcash_3L_FIXED_GRAND_AIR4'){
        $backgroundFile = 'lightningcash_3L_background_jackpot_AIR4';
    } elseif ($imageName === 'lightningcash_4L_FIXED_GRAND_AIR4'){
        $backgroundFile = 'lightningcash_4L_background_jackpot_AIR4';
    } else {
        // Default background filename logic
        $backgroundFile = preg_replace('/(.{4})$/', 'background_$1', $imageName);
    }

    $stmt = $con->prepare("UPDATE genone_core SET themeFile = ?, backgroundVideo = ? WHERE gmvid = ?");
    $stmt->bind_param("sss", $imageName, $backgroundFile, $selectedVenue);
    
    if ($stmt->execute()) {
        header("Location: success.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    
} else {  
    echo "Error: Missing required data.";
}
?>
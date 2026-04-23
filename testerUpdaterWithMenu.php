<?php
session_start();
date_default_timezone_set("Pacific/Auckland");

include "../../connectionG5i.php";
//include('session_timeout.php');


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve the username from the session (username stored as 'account')
$username = isset($_SESSION['account']) ? $_SESSION['account'] : '';
$venueID = isset($_SESSION['venue']) ? $_SESSION['venue'] : '';


$sql_genone_core = "SELECT gmvid, dateAndTime, venue, themeFile, levelSet FROM genone_core WHERE account = ? AND gmvid = ? AND versionID IN ('5.0.5', '5.0.8') AND saffire = 'false'";
$stmt_venues = $con->prepare($sql_genone_core);
$stmt_venues->bind_param("ss", $username, $venueID);
$stmt_venues->execute();
$result_venues = $stmt_venues->get_result();

$gmvid = '';
$date = '';
$venue = '';
$themeFile = '';
$jackpotLevel = '';
if ($row = $result_venues->fetch_assoc()){
    $venue = $row['venue'];
    $themeFile = $row['themeFile'];
    $date = $row['dateAndTime'];
    $jackpotLevel = $row['levelSet'];
    $gmvid = $row['gmvid'];

    $_SESSION['gmvid'] = $gmvid;
}

$_SESSION['levelSet'] = $jackpotLevel;

// Restructuring the date string from the DB
$dateWithoutDigit = substr($date, 0, -5);
$formattedDate = substr($dateWithoutDigit, 0, 4) . '/' . substr($dateWithoutDigit, 4, 2) . '/' . substr($dateWithoutDigit, 6);
$time = substr($date, -4);
$formattedTime = substr($time, 0, 2) . ':' . substr($time, 2);



// Define the fileSystem and web Paths for image folders
$fileSystemPath = __DIR__ . "/$username/graphics/"; 
$webPath = "$username/graphics/";

// Function to recursively scan subfolders and find the matching file
function findMatchingGraphic($folderPath, $themeFile) {
    $files = array_diff(scandir($folderPath), array('.', '..'));
    foreach ($files as $file) {
        $filePath = $folderPath . '/' . $file;
        if (is_dir($filePath)) {
            // Recursively search subfolders
            $found = findMatchingGraphic($filePath, $themeFile);
            if ($found) {
                return $found; // Return the matching file path if found
            }
        } else{
            $fileName = is_string($filePath) ? $file : basename($filePath);
            if (strpos($fileName, $themeFile) !== false) {
                return $filePath;
        }
        }
    }
    return false; // Return false if no matching file is found
}

// Check if the graphics folder exists
if (is_dir($fileSystemPath)) {

    // Search for the matching file in all subfolders
    $matchingGraphicPath = findMatchingGraphic($fileSystemPath, $themeFile);

    if ($matchingGraphicPath) {
        // Convert the file system path to a web path for display
        $relativePath = str_replace(__DIR__, '', $matchingGraphicPath);
        $webGraphicPath = $webPath . ltrim(str_replace($fileSystemPath, '', $matchingGraphicPath), '/');
        $matchingGraphicPathSub = substr($webGraphicPath, 0, -9);
    } 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGL GRAPHICS</title>
    <style>
        .banner {
            background-color: green;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
            transform: perspective(1000px) rotateX(3deg);
        }
        .banner img {
            max-height: 80px; /* Adjust this value as needed */
            height: auto;
        }
        .menu {
            display: flex;
            justify-content: center;
            background-color: white;
            position: fixed;
            top: calc(100px + 20px); /* Adjust based on .banner height + padding */
            width: 100%;
            z-index: 999;
        }
        .menu button {
            background-color: green;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 5px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 20px; /* Makes the buttons rounded */
            transition: all 0.2s ease-in-out; /* Smooth hover effect */

            /* 3D Effect */
            box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.2); /* Soft shadow */
            transform: translateY(2px);
        }
        .menu button:hover {
            background-color: darkgreen;
            transform: translateY(0px); /* Lifts button on hover */
            box-shadow: 5px 5px 8px rgba(0, 0, 0, 0.3); /* Slightly deeper shadow */
        }
        .bottom-banner {
            background-color: green;
            color: white;
            padding: 10px;
            text-align: center;
            position: fixed; 
            bottom: 0;
            width: 100%; /* Stretches across the full width */
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2); /* Adds a subtle shadow */
            margin-top: auto;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            height: 70vh;
            background-color: #f4f4f4;
            padding: 10px;
        }

        .container {
            width: 90%;
            max-width: 400px;
            margin-top: 60px;
            display: inline-block;
        }

        .layer {
            background: white;
            padding: 20px;
            margin: 10px 0;
            text-align: center;
            border-radius: 10px;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
            transform: perspective(1000px) rotateX(3deg);
        }

        .graphic img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .warning {
            color: red;
            font-weight: bold;
        }

        .level {
            font-size: 1.5em;
            font-weight: bold;
            background:green;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }
        .main-content {
            padding-top: 160px;
            text-align: center;
        }
    </style>
    </style>
    <script>
        function navigateTo(page) {
            window.location.href = page;
        }
    </script>
</head>
<div class="banner">
        <img src="AGLBannerLogo.png" alt="AGL Banner Logo">
</div>
<div class="menu">
    <button onclick="navigateTo('testerUpdaterWithMenu.php')">Home</button>
    <button onclick="navigateTo('updateGraphics.php')">Update Graphics</button>
    <button onclick="navigateTo('updateLevel.php')">Jackpot Level Set</button>
</div>
<div class="main-content">
    <div class ="content"></div>
        <div class="container">
            <!-- Venue Name -->
            <div class="layer">
                <h2><?php echo htmlspecialchars($venue); ?></h2>
            </div>

            <!-- Current Graphic -->
            <div class="layer graphic">
                <strong>Current Graphic</strong>
                <?php if (!empty($webGraphicPath) && file_exists($webGraphicPath)): ?>
                    <img src="<?php echo htmlspecialchars($webGraphicPath); ?>" alt="Current Graphic">
                    <p><?php echo htmlspecialchars(basename($matchingGraphicPathSub)); ?></p>
                <?php else: ?>
                    <p>The current graphic running is not part of our standard graphics package.</p>
                <?php endif; ?>
            </div>

            <!-- Date & Time -->
            <div class="layer">
                <h3>Date and Time last communicated</h3>
                <p><?php echo htmlspecialchars($formattedDate); ?></p>
                <p>Time: <?php echo htmlspecialchars($formattedTime); ?></p>
                <p class="warning">⚠ WARNING: If time is not recent, updates will not process!</p>
            </div>

            <!-- Current Level -->
            <div class="layer">
                <label for="current-level">Current Jackpot Setting Level:</label>
                <div class="level"><?php echo htmlspecialchars($jackpotLevel); ?></div>
            </div>
        </div>
</div>
<div class="bottom-banner">
            <p>Advance Gaming - Copyright © 2025</p>
    </div>
</html>

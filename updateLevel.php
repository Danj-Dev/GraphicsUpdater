<?php
session_start();
date_default_timezone_set("Pacific/Auckland");

include "../../connectionG5i.php";
include('session_timeout.php');

$username = isset($_SESSION['account']) ? $_SESSION['account'] : '';
$venue = isset($_SESSION['venue']) ? $_SESSION['venue'] : '';
$jackpotLevel = isset($_SESSION['levelSet']) ? $_SESSION['levelSet'] : '';



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
            max-height: 80px;
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
        .content {
            margin-top: calc(110px + 10px + 50px); /* Match .banner height + padding + .menu height */
            padding: 20px;
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
        .level {
            font-size: 1.5em;
            font-weight: bold;
            background:green;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }
        .form-actions {
            margin-top: 20px;
            text-align: center;
        }
        .form-button {
            background-color: green;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 20px; /* Makes the button oval */
            transition: background-color 0.3s;
            display: inline-block;
            outline: none; /* Removes the default focus outline */
            appearance: none; /* Resets default browser styles */
            box-shadow: none; /* Removes any default shadow */
        }
        .form-button:hover{
            background-color: #575757;
        }
        .bottom-banner {
            background-color: green;
            color: white;
            padding: -4px;
            text-align: center;
            position: fixed; 
            bottom: 0;
            width: 100%; /* Stretches across the full width */
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2); /* Adds a subtle shadow */
            margin-top: auto;
        }
        input[type="number"] {
            width: 60px;
            padding: 5px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            text-align: center;
        }
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
<div class="content">
    <Form method="post" action="submitLevel.php" id="levelPost">
    <div class="layer">
        <label for="current-level"><b>Current Jackpot Setting Level:</b></label>
        <div class="level"><?php echo htmlspecialchars($jackpotLevel); ?></div>
    </div>
    <div class="layer">
            <label for="new-level"><b>Enter New Level:</b></label>
            <input type="number" id="new-level" name="level" min="1" max="472" required>
    </div>
    <div class="form-actions">
        <button type="submit" class="form-button">Submit</button>
    </div>
    </Form>
</div>
<div class="bottom-banner">
        <p>Advance Gaming - Copyright © 2025</p>
</div>
</html>
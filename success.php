<?php
session_start();
include "../../connectionG5i.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Successfull</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: green;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color:rgb(74, 76, 78);
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Your updates have been procesed successfully!</h2>
        <p><a href="updateGraphics.php" class="btn">Change the Graphics</a></p>
        <p><a href="updateLevel.php" class="btn">Change the Jackpot Level</a></p>
        <p><a href="testerUpdaterWithMenu.php" class="btn">Go to Home Page</a></p>
    </div>

</body>
</html>
<?php
session_start();
date_default_timezone_set("Pacific/Auckland");

include "../../connectionG5i.php";
include('session_timeout.php');


$username = isset($_SESSION['account']) ? $_SESSION['account'] : '';
$venue = isset($_SESSION['venue']) ? $_SESSION['venue'] : '';


$sql_genone_core = "SELECT gmvid, dateAndTime, venue, themeFile, levelSet FROM genone_core WHERE account = ? AND versionID IN ('5.0.5', '5.0.8') AND saffire = 'false'";
$stmt_venues = $con->prepare($sql_genone_core);
$stmt_venues->bind_param("s", $username);
$stmt_venues->execute();
$result_venues = $stmt_venues->get_result();

// Define the fileSystemPath$fileSystemPath for image folders
$fileSystemPath = __DIR__ . "/$username/graphics/"; 

$webPath = "$username/graphics/";

// Get the selected folder from the query parameter
$selectedFolder = isset($_GET['folder']) ? $_GET['folder'] : null;

// Function to get the list of folders
function getFolders($fileSystemPath) {
    if (is_dir($fileSystemPath)) { 
        return array_filter(glob($fileSystemPath . '*'), 'is_dir');
    }
    return [];
}

// Function to get images from a folder
function getImages($folder) {
    return glob("$folder/*.{jpg,png,gif,jpeg}", GLOB_BRACE);
}

// Get all folders
$graphicFolders = getFolders($fileSystemPath);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGL GRAPHICS</title>
    <style>
        body {
            font-family: verdana;
        }
        .tabs-container {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            padding: 10px;
            background-color: lightgray;
            margin: 0 auto;
            border: 1px solid #ddd;
            flex-wrap: wrap;
            overflow-x: auto;
            justify-content: center;
            max-width: 100%;
            min-width: 400px;
            z-index: 1;
            position: relative;
            margin-bottom: 60px;
        }
        .tabs-container::-webkit-scrollbar {
            height: 8px; /* Height of the scrollbar */
        }

        .tabs-container::-webkit-scrollbar-thumb {
            background-color: green; /* Color of the scrollbar thumb */
            border-radius: 4px; /* Rounded corners for the scrollbar thumb */
        }

        .tabs-container::-webkit-scrollbar-track {
            background-color:lightgray; /* Color of the scrollbar track */
        }
        .tab {
            display: inline-block;
            flex: 0 0 auto;
            padding: 10px;
            margin-right: 5px;
            background-color: lightgray;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            border-radius: 8px;
            transition: all 0.3s ease;
            min-width: 120px;
        }
        .tab img {
            display: block;
            margin: 0 auto 5px;
            max-width: 120px;
            max-height: 120px;
        }
        .tab span {
            display: block;
            font-size: 14px;
        }

        .tab:hover {
            background-color: #f0f0f0;
            border-color: #ccc;
        }

        .tab.active {
            background-color:green;
            color: white;
            border-color:black;
        }
        .image-grid img {
            margin: 10px;
            max-width: 100px;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .image-grid img.selected {
            border: 2px solid blue;
        }
        .image-item {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .image-item.active {
            background-color: green;
            color: white;
        }
        .images-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            justify-content: center; /* Center the grid items */
            align-items: center; /* Vertically align the grid items */
            max-width: 800px; /* Limit the grid width */
            margin: 0 auto;
        }
        .image-item img {
            max-width: 150px;
            display: block;
            margin: 0 auto;
        }
        .image-item p {
            margin-top: 5px;
            font-size: 14px;
            color: #333;
        }
        .image-grid {
            display: flex;
            flex-direction: column;
        }
        .image-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .image-item.selected {
            border-color: blue;
        }
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
            top: calc(100px + 20px); /* Adjust based on banner height + padding */
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
        .form-actions {
            margin-top: 20px;
            text-align: center;
        }
        .my-form{
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

        .menu button:hover {
            background-color: darkgreen;
            transform: translateY(0px); /* Lifts button on hover */
            box-shadow: 5px 5px 8px rgba(0, 0, 0, 0.3); /* Slightly deeper shadow */
        }
        .content {
            margin-top: calc(110px + 10px + 50px); /* Match .banner height + padding + .menu height */
            padding: 20px;
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
        .main-content {
            padding-bottom: 100px; /* Extra space so nothing gets hidden behind sticky bar */
        }
    </style>
    </style>
    <script>
        function selectImage(element, imagePath) {
            // Remove 'selected' class from all image items
            document.querySelectorAll('.image-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Add 'selected' class to the clicked item
            element.classList.add('selected');

            // Store the selected image path in the hidden input
            document.getElementById('selectedImage').value = imagePath;


        }

        document.addEventListener("DOMContentLoaded", function () {
            const submitButton = document.querySelector(".form-button");
            const hiddenInput = document.getElementById("selectedImage");
            const form = document.getElementById("imageSelectionForm");

            submitButton.disabled = true;

            function selectImage(element, imageName) {
                // Remove active class from any previously selected item
                const previousActive = document.querySelector('.image-item.active');
                if (previousActive) {
                    previousActive.classList.remove('active');
                }

                // Add active class to the clicked item
                element.classList.add('active');

                // Update the hidden input field
                document.getElementById('selectedImage').value = imageName;

                submitButton.disabled = false;

            }
            form.addEventListener("submit", function (event) {
                if (hiddenInput.value.trim() === "") {
                    event.preventDefault(); // Stop form submission
                    alert("Please select a theme before submitting.");
                }
            });
            window.selectImage = selectImage;
        });
        function navigateTo(page) {
            window.location.href = page;
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Watch for clicks on tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    // Wait briefly for the active class to be applied
                    setTimeout(() => {
                        if (this.classList.contains('active')) {
                            window.scrollTo({
                                top: document.documentElement.scrollHeight,
                                behavior: 'smooth'
                            });
                        }
                    }, 50);
                });
            });

            // Also check for initially active tab
            const activeTab = document.querySelector('.tab.active');
            if (activeTab) {
                setTimeout(() => {
                    window.scrollTo({
                        top: document.documentElement.scrollHeight,
                        behavior: 'smooth'
                    });
                }, 100);
            }
        });
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
<div class ="content"></div>
<div class="my-form">
    <H1>UPDATE YOUR GRAPHICS</H1>
    <div></div>
    <h2>Select a Graphic To View Levels</h2>
    <div class="main-content">
        <div class="tabs-container">
            <?php foreach ($graphicFolders as $folder): ?>
                <?php
                $folderName = basename($folder);
                $folderPath = $fileSystemPath . $folderName;
                // Get the first image in the folder 
                $images = getImages($folderPath);

                $thumbnail = count($images) > 0 ? $images[0] : null;
                $thumbnailWebPath = $thumbnail
                ? $webPath . $folderName . '/' . basename($thumbnail)
                : '/updateGraphics/AGL.png';
                ?>
                <a href="?folder=<?php echo urlencode($folderName); ?>" class="tab <?php echo ($selectedFolder === $folderName) ? 'active' : ''; ?>">
                    <img src="<?php echo $thumbnailWebPath; ?>" alt="Thumbnail for <?php echo $folderName; ?>">
                    <span><?php echo $folderName; ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="image-grid">
        <?php if ($selectedFolder): ?>
            <?php 
            // Construct the filesystem path for the selected folder
            $selectedFolderPath = $fileSystemPath . $selectedFolder;

            // Get all images in the selected folder
            $images = getImages($selectedFolderPath); 
            ?>
            
            <?php if (count($images) > 0): ?>
                <form action="submitGraphic.php" method="post" id="imageSelectionForm">
                    <input type="hidden" name="selectedImage" id="selectedImage">
                    
                    <div class="images-container">
                        <?php foreach ($images as $image): ?>
                            <?php
                            // Convert the filesystem path of the image to a web path
                            $imageWebPath = $webPath . $selectedFolder . '/' . basename($image);

                            // Extract the image name
                            $imageName = basename($image); // Get the filename
                            $parts = explode('_', $imageName); // Split by underscore

                            if (strpos($imageName, 'FIXED') !== false && count($parts) >= 3) {
                                // If "FIXED" is in the name, display up to the 4th underscore
                                $displayName = implode('_', array_slice($parts, 0, 3));
                            } elseif (count($parts) > 1) {
                                // Otherwise, display up to the 2nd underscore
                                $displayName = $parts[0] . '_' . $parts[1];
                            } else {
                                // Handle names with fewer than two underscores
                                $displayName = $parts[0];
                            }
                            ?>
                            <div class="image-item" 
                                onclick="selectImage(this, '<?php echo htmlspecialchars($imageName); ?>')">
                                <img src="<?php echo $imageWebPath; ?>" alt="Image">
                                <p><?php echo htmlspecialchars($displayName); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-actions" style="position: sticky; bottom: 0; background: white; padding-top: 10px; z-index: 100;">
                        <button type="submit" class="form-button" disabled>Submit</button>
                    </div>
                </form>
            <?php else: ?>
                <p>No images found in this folder.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    </div>
</form>
<div class="bottom-banner">
        <p>Advance Gaming - Copyright © 2025</p>
</div>
</html>
<?php
session_start();
include "../../connectionG5i.php";


$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']); // Clear the error after showing it

// Check for an error message in the URL
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
    echo "<script>showPopup('$error');</script>";
}
date_default_timezone_set("Pacific/Auckland");



?>


<!-- CSS styling code for all elements  -->
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name ="viewport" content = "width=device-width, initial-scale=1.0">
        <title>Graphic Updater</title>
        <style>
            body{
                margin:0;
                padding:30;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 60vh;
                background-size: cover;
                background-position: center;
                transition: background 0.3s ease;
            }
            .login-container{
                background-color: rgba(255, 255, 255, 0.8);
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            select, input {
                display: block;
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                font-size: 16px;
                border-radius: 5px;
                border: 1px solid #ccc;
            }
            button {
                padding: 10px 20px;
                background-color: #28a745;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }

            button:hover {
                background-color: #218838;
            }
            .popup {
                display: none; /* Hidden by default */
                position: fixed; /* Stay in place */
                z-index: 1; /* Sit on top */
                left: 0;
                top: 2;
                width: 100%; /* Full width */
                height: 100%; /* Full height */
                overflow: auto; /* Enable scroll if needed */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            }

            .popup-content {
                background-color: white;
                margin: 15% auto; /* 15% from the top and centered */
                padding: 20px;
                border: 1px solid #888;
                width: 80%; /* Could be more or less */
                text-align: center;
            }

            .close-btn {
                color: red;
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }

        </style>
    </head>
    <?php
        if (isset($_GET['message'])) {
            echo "<p style='color:red'>" . htmlspecialchars($_GET['message']) . "</p>";
        }
        ?>
    <body> 
        <!-- A login form that lets the user select which trust, changes the background based on what they select, enter a accessCode and get validated on "Login" -->
        <div class ="login-container">
            <img style="vertical-align:middle: 40px;width:300px;height:150px" src="AGL.png" alt="AGL">
            <h2>Trust Login</h2>
            <form id="loginForm" action="logIn.php" method="POST" onsubmit="return validateForm()">
                <label for="account">Select Trust</label>
                <select name="account" id="account" onchange="changeBackground()">
                    <option value="">--Select Trust--</option>
                    <option value="nzct">New Zealand Community Trust</option>
                    <option value="pubcharity">Pub Charity Ltd</option>
                    <option value="lionfoundation">Lion Foundation</option>
                    <option value="grassroots">Grassroots</option>
                    <option value="trillian">Trillian</option>
                    <option value="airrescue">Air Rescue</option>
                    <option value="mainland">Mainland Foundation</option>
                    <option value="aotearoa">Aotearoa Gaming Trust</option>
                    <option value="ilt">ILT Foundation</option>
                    <option value="fourwinds">Four Winds Foundation</option>
                    <option value="pelorus">Pelorus</option>
                    <option value="kiwigaming">Kiwi Gaming</option>
                    <option value="agl">Advance Gaming Ltd</option>                   
                </select>
                <label for="venue">Select Venue</label>
                <select id="venue" name="venue" required>
                    <option value="">Select Venue</option>
                </select>
                <label for="accessCode">Access Code</label>                
                <input type="accessCode" id="accessCode" name="accessCode">
                <button type="submit">Log In</button>
            </form>
        </div>
        <div id="errorPopup" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup()">&times;</span>
                <p id="popupMessage"></p>
            </div>
        </div>
        <script>
            function changeBackground(){
                const account = document.getElementById('account').value.toLowerCase();
                const body = document.body;

                if(account!== 'default'){
                    body.style.backgroundImage = `url('${account}/logo.png')`;


                } else {
                    body.style.backgroundImage = "none";  
                }
            }
            function validateForm() {
                let account = document.forms["loginForm"]["account"].value;
                let accessCode = document.forms["loginForm"]["accessCode"].value;
                let errorMessage = "";

                if (account === "") {
                    errorMessage = "Please select an account.";
                } else if (accessCode === "") {
                    errorMessage = "Please enter an Access Code.";
                }

                if (errorMessage) {
                    displayPopup(errorMessage);
                    return false; // Prevent form submission
                }
                }
            function displayPopup(message) {
                document.getElementById("popupMessage").innerText = message;
                document.getElementById("errorPopup").style.display = "block";
            }

            function closePopup() {
                document.getElementById("errorPopup").style.display = "none";
            }
            // Display server-side error in popup on page load
            const serverError = <?php echo json_encode($error); ?>;
            if (serverError) {
                displayPopup(serverError);
            }

            
            //fetch venues script
            document.getElementById('account').addEventListener('change', function() {
                const account = this.value;

                if (!account) {
                    // Clear the venue dropdown if no account is selected
                    document.getElementById('venue').innerHTML = '<option value="">Select Venue</option>';
                    return;
                }

                // Fetch venues for the selected account
                fetch('fetch_venues.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ account: account })
                })
                .then(response => response.json())
                .then(data => {
                    const venueDropdown = document.getElementById('venue');
                    venueDropdown.innerHTML = '<option value="">Select Venue</option>'; // Reset dropdown

                    if (data.venues && data.venues.length > 0) {
                                    // Sort venues alphabetically by venue name
                        data.venues.sort((a, b) => a.venue.localeCompare(b.venue));
                        
                        data.venues.forEach(venue => {
                            const option = document.createElement('option');
                            option.value = venue.gmvid; // Use gmvid as the value
                            option.textContent = venue.venue; // Display venue name
                            venueDropdown.appendChild(option);
                        });
                    } else {
                        venueDropdown.innerHTML = '<option value="">No venues found</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching venues:', error);
                });
            });
            window.addEventListener('DOMContentLoaded', () => {
                const select = document.getElementById('account');
                const options = Array.from(select.options).slice(1);

                options.sort((a, b) => a.text.localeCompare(b.text));

                // Remove old options
                options.forEach(option => select.remove(option.index));

                // Re-add sorted options
                options.forEach(option => select.add(option));
            });
                
        </script>
        


</body>

</html>





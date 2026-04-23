<?php   
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../connectionG5i.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = isset($_POST['account']) ? trim($_POST['account']) : '';
    $accessCode = isset($_POST['accessCode']) ? trim($_POST['accessCode']) : '';
    $venue = isset($_POST['venue']) ? trim($_POST['venue']) : ''; 

    // Simple validation
    if (empty($account) || empty($accessCode) || empty($venue)) {
        $error = "Account, Access Code, and Venue are required.";
    } else {
        // Prepare a SQL query to get the code where the venue matches gmvid
        $query = $con->prepare("SELECT code FROM genone_core WHERE gmvid = ?");
        if (!$query) {
            die("Query preparation failed: " . $con->error);
        }
        $query->bind_param("s", $venue); // Bind the venue from POST to the placeholder
        $query->execute();
        $query->bind_result($stored_accessCode);

        if ($query->fetch()) {
            // Verify the accessCode
            if ($accessCode === $stored_accessCode) {
                $_SESSION['account'] = $account;
                $_SESSION['venue'] = $venue;

                $_SESSION['loggedin'] = true;
                $_SESSION['LAST_ACTIVITY'] = time();

                header("Location: testerUpdaterWithMenu.php");
                exit();
            } else {
                $error = "Invalid Access Code. Please try again.";
            }
        } else {
            $error = "No matching venue found.";
        }

        $query->close();
    }
    // Redirect to index.php with the error message
    if (!empty($error)) {
        header("Location: index.php?error=" . urlencode($error));
        exit();
    }
}
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    include('session_timeout.php');
}

?>
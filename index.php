<?php
require('config.php');
session_destroy();
session_unset();
session_start(); // Start the session
$user_id = "";
if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    if(empty($user_id)) {
        // Display "Welcome, enter User ID" message if the user has not entered anything
        header("location: login.html");
        exit;
    } else {
        $query = $con->prepare("SELECT * FROM users WHERE User_ID = ?");
        $query->bind_param('s', $user_id);
        $query->execute();
        $row = $query->get_result();

        if($row->num_rows > 0) {
            // Store the user ID in the session
            $_SESSION['user_id'] = $user_id;

            // Redirect to welcome page if user ID is found in the database
            header("location: welcome.html");
            exit;
        } else {
            // Display "Incorrect user ID" message if user ID is not found in the database
            header("location: incorrect_login.html");
            exit;
        }
    }
} else {
    // Check if the user ID is already stored in the session
    if(isset($_SESSION['user_id'])) {
        // Redirect to welcome page if user ID is already stored in the session
        header("location: welcome.html");
        exit;
    } else {
        // Display "Welcome, enter User ID" message by default
        header("location: login.html");
        exit;
    }
}
?>

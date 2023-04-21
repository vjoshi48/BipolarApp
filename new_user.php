<?php
require 'config.php';

if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    if(empty($user_id)) {
        // Display "Welcome, enter User ID" message if the user has not entered anything
        header("location: login.html");
        exit;
    } else {
        // check if the user ID already exists in the database
        $sql = "SELECT * FROM users WHERE User_ID = '$user_id'";
        $result = $con->query($sql);

        if ($result->num_rows > 0) {
            // user ID already exists, display message
            echo "User ID already exists<br>";
            echo "<button onclick=\"window.location.href='index.php'\">Go Back</button>";
        } else {
            // user ID does not exist, insert it into the database
            $sql = "INSERT INTO users (User_ID) VALUES ('$user_id')";
            if ($con->query($sql) === TRUE) {
                // redirect to welcome page
                header('Location: welcome.html');
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $con->error;
            }
        }
    }
} else {
    // Display "Enter New User ID" message by default
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>New User</title>
    </head>
    <body>
        <h1>Enter New User ID</h1>
        <form method="post" action="new_user.php">
            <label for="user_id">User ID:</label>
            <input type="text" id="user_id" name="user_id" required>
            <br><br>
            <input type="submit" value="Create User">
        </form>
    </body>
    </html>
    <?php
}

$con->close();
?>

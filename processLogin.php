<?php
// processLogin.php: Authenticate user login
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $unique_number = $conn->real_escape_string($_POST['unique_number']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE unique_number = '$unique_number'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables and redirect to chat interface
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['unique_number'] = $user['unique_number'];
            header("Location: index.php");
            exit();
        } else {
            echo "Incorrect password. <a href='login.php'>Try again</a>";
        }
    } else {
        echo "User not found. <a href='signup.php'>Sign up</a>";
    }
}
?>

<?php
// processSignup.php: Process the signup form, generate unique number, and save the user.
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert user record (unique_number will be generated below)
    $sql = "INSERT INTO users (name, password) VALUES ('$name', '$password')";
    if ($conn->query($sql) === TRUE) {
        // Use the auto_increment id to generate a unique number (e.g., +786-1001)
        $user_id = $conn->insert_id;
        $unique_number = "+786-" . (1000 + $user_id);
        $update_sql = "UPDATE users SET unique_number = '$unique_number' WHERE id = $user_id";
        if ($conn->query($update_sql) === TRUE) {
            header("Location: login.php");
            exit();
        } else {
            echo "Error updating unique number: " . $conn->error;
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

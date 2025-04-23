<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    exit("Not authorized");
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status_text'])) {
    $status_text = $conn->real_escape_string($_POST['status_text']);
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO statuses (user_id, status_text) VALUES ($user_id, '$status_text')";
    if ($conn->query($sql) === TRUE) {
        echo "Status posted";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    die("Not authorized");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_id'], $_POST['message'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = intval($_POST['receiver_id']);
    $message = trim($_POST['message']);
    if ($message == "") {
        die("Empty message");
    }
    $message = $conn->real_escape_string($message);
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ($sender_id, $receiver_id, '$message')";
    if ($conn->query($sql) === TRUE) {
        echo "Message sent";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    exit("Not authorized");
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['group_name'])) {
    $group_name = $conn->real_escape_string($_POST['group_name']);
    $created_by = $_SESSION['user_id'];
    $sql = "INSERT INTO groups (group_name, created_by) VALUES ('$group_name', $created_by)";
    if ($conn->query($sql) === TRUE) {
        echo "Group created";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    die("Not authorized");
}
$current_user_id = $_SESSION['user_id'];
if (!isset($_GET['contact_id'])) {
    die("No contact selected");
}
$contact_id = intval($_GET['contact_id']);
$sql = "SELECT * FROM messages WHERE (sender_id = $current_user_id AND receiver_id = $contact_id) OR (sender_id = $contact_id AND receiver_id = $current_user_id) ORDER BY timestamp ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
        $class = ($row['sender_id'] == $current_user_id) ? "sent" : "received";
        echo "<div class='message $class'>";
        echo htmlspecialchars($row['message']);
        echo "<div class='timestamp'>" . date("H:i", strtotime($row['timestamp'])) . "</div>";
        echo "</div>";
    }
} else {
    echo "<p style='text-align:center; color:#888;'>No messages yet.</p>";
}
?>

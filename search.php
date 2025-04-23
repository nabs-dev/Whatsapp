<?php
// search.php: Return contacts matching a unique number search query
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    exit("Not authorized");
}

$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
$current_user_id = $_SESSION['user_id'];

if ($query != '') {
    $sql = "SELECT * FROM users WHERE unique_number LIKE '%$query%' AND id != $current_user_id ORDER BY name ASC";
} else {
    $sql = "SELECT * FROM users WHERE id != $current_user_id ORDER BY name ASC";
}

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
        echo '<div class="contact" data-id="'.$row['id'].'" data-unique="'.$row['unique_number'].'">';
        echo '<strong>' . htmlspecialchars($row['name']) . '</strong><br>';
        echo '<small>' . htmlspecialchars($row['unique_number']) . '</small>';
        echo '</div>';
    }
} else {
    echo '<p style="padding:15px;">No contacts found.</p>';
}
?>

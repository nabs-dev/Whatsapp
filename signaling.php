<?php
// signaling.php: Basic file-based signaling for WebRTC calls.
header('Content-Type: application/json');

function getSignalingFile($room) {
    return "signaling_room_" . preg_replace('/[^a-zA-Z0-9_\-]/', '', $room) . ".json";
}

// Handle POST request: store incoming signaling message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents("php://input");
    $message = json_decode($input, true);
    if (!$message || !isset($message['room'])) {
        echo json_encode(["error" => "Invalid message"]);
        exit();
    }
    $room = $message['room'];
    $file = getSignalingFile($room);
    $messages = [];
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $messages = json_decode($content, true);
        if (!is_array($messages)) { $messages = []; }
    }
    $messages[] = $message;
    file_put_contents($file, json_encode($messages));
    echo json_encode(["status" => "ok"]);
    exit();
}

// Handle GET request: retrieve and clear signaling messages
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['room'])) {
    $room = $_GET['room'];
    $file = getSignalingFile($room);
    $messages = [];
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $messages = json_decode($content, true);
        // Clear the file after reading
        file_put_contents($file, json_encode([]));
    }
    echo json_encode($messages);
    exit();
}

echo json_encode(["error" => "Invalid request"]);

<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
$current_user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>WhatsApp Clone - Chat</title>
  <style>
    /* Basic Reset */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: Arial, sans-serif;
      background: #e5ddd5;
    }
    /* Header */
    .header {
      background: #075e54;
      color: #fff;
      padding: 15px;
      text-align: center;
      position: relative;
    }
    .header a {
      position: absolute;
      right: 20px;
      top: 15px;
      color: #fff;
      text-decoration: none;
    }
    .header .call-buttons {
      position: absolute;
      left: 20px;
      top: 15px;
    }
    .header .call-buttons button {
      background: transparent;
      border: none;
      color: #fff;
      font-size: 20px;
      margin-right: 10px;
      cursor: pointer;
    }
    /* Container Layout */
    .container {
      display: flex;
      height: calc(100vh - 60px);
    }
    .contacts {
      width: 30%;
      background: #fff;
      overflow-y: auto;
      border-right: 1px solid #ccc;
    }
    .contacts .contact {
      padding: 15px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
      transition: background 0.3s;
    }
    .contacts .contact:hover {
      background: #f0f0f0;
    }
    .chat {
      width: 70%;
      display: flex;
      flex-direction: column;
      background: #f8f9fa;
    }
    .chat-window {
      flex: 1;
      padding: 15px;
      overflow-y: auto;
      background: #e5ddd5;
    }
    .message-input {
      background: #fff;
      padding: 10px;
      display: flex;
      border-top: 1px solid #ccc;
    }
    .message-input input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 20px;
      outline: none;
    }
    .message-input button {
      background: #075e54;
      color: #fff;
      border: none;
      padding: 10px 15px;
      margin-left: 10px;
      border-radius: 50%;
      cursor: pointer;
    }
    .message {
      margin: 10px 0;
      padding: 10px 15px;
      border-radius: 20px;
      max-width: 70%;
      font-size: 14px;
      line-height: 1.4;
    }
    .sent {
      background: #dcf8c6;
      margin-left: auto;
    }
    .received {
      background: #fff;
      margin-right: auto;
      border: 1px solid #ccc;
    }
    .timestamp {
      font-size: 10px;
      color: #555;
      text-align: right;
    }
    /* Simulated Call Overlay - Fixed */
    .simulated-call {
      display: none; /* Hidden by default */
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.8);
      color: #fff;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      flex-direction: column;
    }
    .simulated-call.active {
      display: flex; /* Shown only when active */
    }
    .simulated-call h2 {
      margin-bottom: 10px;
      font-size: 24px;
    }
    .simulated-call p {
      margin: 10px 0;
      font-size: 18px;
    }
    .simulated-call button {
      padding: 10px 20px;
      background: #d93025;
      border: none;
      border-radius: 5px;
      color: #fff;
      font-size: 16px;
      cursor: pointer;
    }
  </style>
</head>
<body>
<div class="header">
  <div class="call-buttons">
    <button onclick="startSimulatedCall('voice')">📞</button>
    <button onclick="startSimulatedCall('video')">📹</button>
  </div>
  WhatsApp Clone - <?php echo $_SESSION['unique_number']; ?>
  <a href="logout.php">Logout</a>
</div>
<div class="container">
  <div class="contacts" id="contactsList">
    <?php
    $sql = "SELECT * FROM users WHERE id != $current_user_id ORDER BY name ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<div class='contact' data-id='{$row['id']}' data-unique='" . htmlspecialchars($row['unique_number']) . "'>";
        echo "<strong>" . htmlspecialchars($row['name']) . "</strong><br>";
        echo "<small>" . htmlspecialchars($row['unique_number']) . "</small>";
        echo "</div>";
      }
    } else {
      echo "<p>No contacts found.</p>";
    }
    ?>
  </div>
  <div class="chat">
    <div class="chat-window" id="chatWindow">
      <p style="text-align:center; color:#888;">Select a contact to start chatting</p>
    </div>
    <div class="message-input">
      <input type="text" id="messageInput" placeholder="Type a message">
      <button id="sendButton">➤</button>
    </div>
  </div>
</div>
<!-- Simulated Call Overlay (Hidden by default) -->
<div class="simulated-call" id="fakeCallOverlay">
  <div>
    <h2 id="callTypeHeading"></h2>
    <p id="callStatus">Connecting...</p>
    <p id="callTimer">00:00</p>
    <button onclick="endSimulatedCall()">Hang Up</button>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Chat Functionality
  let selectedContactId = null;
  function attachContactClickHandlers() {
    document.querySelectorAll('.contact').forEach(function(contact) {
      contact.addEventListener('click', function(){
        selectedContactId = this.getAttribute('data-id');
        loadChat(selectedContactId);
      });
    });
  }
  attachContactClickHandlers();
  
  document.getElementById('sendButton').addEventListener('click', function(){
    const message = document.getElementById('messageInput').value;
    if(selectedContactId && message.trim() !== ""){
      sendMessage(selectedContactId, message);
      document.getElementById('messageInput').value = "";
    }
  });
  
  function sendMessage(receiverId, message) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "sendMessage.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function(){
      if(xhr.status === 200){
        console.log("Message sent:", xhr.responseText);
        loadChat(receiverId);
      } else {
        console.error("Error sending message.");
      }
    };
    xhr.send("receiver_id=" + receiverId + "&message=" + encodeURIComponent(message));
  }
  
  function loadChat(receiverId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "fetchMessages.php?contact_id=" + receiverId, true);
    xhr.onload = function(){
      if(xhr.status === 200){
        document.getElementById('chatWindow').innerHTML = xhr.responseText;
        document.getElementById('chatWindow').scrollTop = document.getElementById('chatWindow').scrollHeight;
      } else {
        console.error("Error loading chat.");
      }
    };
    xhr.send();
  }
  
  setInterval(function(){
    if(selectedContactId) {
      loadChat(selectedContactId);
    }
  }, 3000);
  
  // Simulated Call Functionality
  let callStartTime, callTimerInterval;
  window.startSimulatedCall = function(type) {
    document.getElementById('callTypeHeading').innerText = (type === 'voice') ? "Voice Call" : "Video Call";
    document.getElementById('callStatus').innerText = "Connecting...";
    document.getElementById('callTimer').innerText = "00:00";
    document.getElementById('fakeCallOverlay').classList.add('active');
    setTimeout(function(){
      document.getElementById('callStatus').innerText = "Connected";
      callStartTime = new Date();
      callTimerInterval = setInterval(updateCallTimer, 1000);
    }, 3000);
  };
  
  function updateCallTimer() {
    const now = new Date();
    const diff = Math.floor((now - callStartTime) / 1000);
    const minutes = Math.floor(diff / 60);
    const seconds = diff % 60;
    document.getElementById('callTimer').innerText =
      (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
  }
  
  window.endSimulatedCall = function() {
    clearInterval(callTimerInterval);
    document.getElementById('fakeCallOverlay').classList.remove('active');
  };
});
</script>
</body>
</html>

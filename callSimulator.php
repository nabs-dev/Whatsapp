<?php
// callSimulator.php: A dummy/simulated call interface for voice and video calls.
include 'config.php';
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>WhatsApp Clone - Fake Call Simulator</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f0f0f0;
    }
    .call-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .call-box {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      text-align: center;
      width: 300px;
    }
    .call-box h2 {
      margin-bottom: 10px;
      color: #075e54;
    }
    .call-box p {
      margin: 10px 0;
      font-size: 14px;
      color: #333;
    }
    .call-buttons {
      margin-top: 20px;
    }
    .call-buttons button {
      padding: 10px 20px;
      margin: 5px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }
    .start-voice, .start-video {
      background: #25d366;
      color: #fff;
    }
    .hangup {
      background: #d93025;
      color: #fff;
    }
    /* Simulated Call Overlay */
    .simulated-call {
      display: none;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.8);
      color: #fff;
      z-index: 1000;
    }
    .simulated-call.active {
      display: flex;
    }
    .simulated-call h2 {
      margin-bottom: 10px;
      font-size: 24px;
    }
    .call-status {
      margin: 10px 0;
      font-size: 18px;
    }
    .call-timer {
      margin: 10px 0;
      font-size: 20px;
      font-weight: bold;
    }
    .simulated-call button {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      background: #d93025;
      color: #fff;
      font-size: 16px;
      cursor: pointer;
    }
  </style>
</head>
<body>
<div class="call-container">
  <div class="call-box">
    <h2>Fake Call Simulator</h2>
    <p>Select the type of call you want to simulate:</p>
    <div class="call-buttons">
      <button class="start-voice" onclick="startCall('voice')">Simulated Voice Call</button>
      <button class="start-video" onclick="startCall('video')">Simulated Video Call</button>
    </div>
  </div>
</div>

<div id="fakeCallOverlay" class="simulated-call">
  <div>
    <h2 id="callTypeHeading"></h2>
    <p class="call-status" id="callStatus">Connecting...</p>
    <div class="call-timer" id="callTimer">00:00</div>
    <button onclick="endCall()">Hang Up</button>
  </div>
</div>

<script>
  let callType = '';
  let callStartTime;
  let callTimerInterval;

  function startCall(type) {
    callType = type;
    document.getElementById('callTypeHeading').innerText = (type === 'voice') ? "Voice Call" : "Video Call";
    document.getElementById('callStatus').innerText = "Connecting...";
    document.getElementById('callTimer').innerText = "00:00";
    // Show simulated call overlay
    document.getElementById('fakeCallOverlay').classList.add('active');
    // Simulate connection delay (e.g., 3 seconds)
    setTimeout(function(){
      document.getElementById('callStatus').innerText = "Connected";
      callStartTime = new Date();
      callTimerInterval = setInterval(updateCallTimer, 1000);
    }, 3000);
  }

  function updateCallTimer() {
    const now = new Date();
    const diff = Math.floor((now - callStartTime) / 1000);
    const minutes = Math.floor(diff / 60);
    const seconds = diff % 60;
    document.getElementById('callTimer').innerText = 
      (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
  }

  function endCall() {
    clearInterval(callTimerInterval);
    document.getElementById('fakeCallOverlay').classList.remove('active');
  }
</script>
</body>
</html>

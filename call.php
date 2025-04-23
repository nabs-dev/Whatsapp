<?php
// call.php: Call interface for voice and video calls.
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// For demonstration, we use a "room" identifier passed as a GET parameter.
// In a one-to-one call, you might generate a room ID based on the two users' unique numbers.
$room = isset($_GET['room']) ? $_GET['room'] : $_SESSION['unique_number'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>WhatsApp Clone - Call</title>
  <style>
    body { margin: 0; font-family: sans-serif; background: #f0f0f0; }
    #controls { padding: 10px; background: #075e54; color: #fff; text-align: center; }
    #videos { display: flex; justify-content: space-around; margin-top: 20px; }
    video { width: 45%; background: #000; }
    #buttons { text-align: center; margin-top: 20px; }
    button { padding: 10px 20px; margin: 5px; border: none; background: #25d366; color: #fff; font-size: 16px; cursor: pointer; }
    button:hover { background: #128c7e; }
  </style>
</head>
<body>
<div id="controls">
  <h2>Call Room: <?php echo htmlspecialchars($room); ?></h2>
</div>
<div id="buttons">
  <button id="startVoiceCall">Start Voice Call</button>
  <button id="startVideoCall">Start Video Call</button>
  <button id="hangUp" style="display:none;">Hang Up</button>
</div>
<div id="videos" style="display:none;">
  <video id="localVideo" autoplay muted></video>
  <video id="remoteVideo" autoplay></video>
</div>

<script>
  // WebRTC and signaling setup
  let localStream;
  let pc;
  const configuration = {
    iceServers: [{ urls: "stun:stun.l.google.com:19302" }]
  };
  const room = "<?php echo $room; ?>";
  const pollingInterval = 3000; // Poll signaling every 3 seconds

  // DOM elements
  const startVoiceCallBtn = document.getElementById('startVoiceCall');
  const startVideoCallBtn = document.getElementById('startVideoCall');
  const hangUpBtn = document.getElementById('hangUp');
  const videosDiv = document.getElementById('videos');
  const localVideo = document.getElementById('localVideo');
  const remoteVideo = document.getElementById('remoteVideo');

  startVoiceCallBtn.addEventListener('click', function() {
    startCall({ video: false, audio: true });
  });
  startVideoCallBtn.addEventListener('click', function() {
    startCall({ video: true, audio: true });
  });
  hangUpBtn.addEventListener('click', hangUp);

  async function startCall(constraints) {
    try {
      localStream = await navigator.mediaDevices.getUserMedia(constraints);
      localVideo.srcObject = localStream;
      videosDiv.style.display = 'flex';
      hangUpBtn.style.display = 'inline-block';
      // Create peer connection
      pc = new RTCPeerConnection(configuration);
      localStream.getTracks().forEach(track => {
        pc.addTrack(track, localStream);
      });
      pc.ontrack = event => {
        remoteVideo.srcObject = event.streams[0];
      };
      pc.onicecandidate = event => {
        if (event.candidate) {
          sendSignalingMessage({ type: "candidate", candidate: event.candidate });
        }
      };
      // Create and send offer
      const offer = await pc.createOffer();
      await pc.setLocalDescription(offer);
      sendSignalingMessage({ type: "offer", offer: offer });
      // Start polling for incoming signaling messages
      pollSignaling();
    } catch (err) {
      console.error("Error starting call:", err);
    }
  }

  async function pollSignaling() {
    try {
      const response = await fetch("signaling.php?room=" + room);
      const messages = await response.json();
      if (messages && messages.length > 0) {
        messages.forEach(message => {
          handleSignalingMessage(message);
        });
      }
    } catch (err) {
      console.error("Error polling signaling:", err);
    }
    setTimeout(pollSignaling, pollingInterval);
  }

  async function sendSignalingMessage(message) {
    message.room = room;
    try {
      await fetch("signaling.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(message)
      });
    } catch (err) {
      console.error("Error sending signaling message:", err);
    }
  }

  async function handleSignalingMessage(message) {
    if (!pc) {
      // If no peer connection exists yet, handle incoming offer (callee side)
      if (message.type === "offer") {
        pc = new RTCPeerConnection(configuration);
        pc.onicecandidate = event => {
          if (event.candidate) {
            sendSignalingMessage({ type: "candidate", candidate: event.candidate });
          }
        };
        pc.ontrack = event => {
          remoteVideo.srcObject = event.streams[0];
        };
        await pc.setRemoteDescription(new RTCSessionDescription(message.offer));
        // For callee, request media (for demonstration, we request video+audio)
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        localVideo.srcObject = localStream;
        localStream.getTracks().forEach(track => {
          pc.addTrack(track, localStream);
        });
        const answer = await pc.createAnswer();
        await pc.setLocalDescription(answer);
        sendSignalingMessage({ type: "answer", answer: answer });
        videosDiv.style.display = 'flex';
        hangUpBtn.style.display = 'inline-block';
      }
    } else {
      if (message.type === "answer") {
        await pc.setRemoteDescription(new RTCSessionDescription(message.answer));
      } else if (message.type === "candidate") {
        try {
          await pc.addIceCandidate(new RTCIceCandidate(message.candidate));
        } catch (err) {
          console.error("Error adding received ICE candidate:", err);
        }
      } else if (message.type === "hangup") {
        hangUp();
      }
    }
  }

  function hangUp() {
    if (pc) {
      pc.close();
      pc = null;
    }
    if (localStream) {
      localStream.getTracks().forEach(track => track.stop());
      localStream = null;
    }
    videosDiv.style.display = 'none';
    hangUpBtn.style.display = 'none';
    // Optionally, send a hangup signal
    sendSignalingMessage({ type: "hangup" });
  }
</script>
</body>
</html>

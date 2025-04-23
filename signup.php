<?php
// signup.php: Signup page for new users
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>WhatsApp Clone - Signup</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f0f0; }
    .container {
      width: 400px; margin: 100px auto; background: #fff; padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 5px;
    }
    h2 { text-align: center; color: #075e54; }
    input[type="text"], input[type="password"] {
      width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc;
      border-radius: 3px;
    }
    button {
      width: 100%; padding: 10px; background: #25d366; border: none;
      color: #fff; font-size: 16px; border-radius: 3px; cursor: pointer;
    }
    button:hover { background: #128c7e; }
    .link { text-align: center; margin-top: 10px; }
  </style>
</head>
<body>
<div class="container">
  <h2>Sign Up</h2>
  <form action="processSignup.php" method="post">
    <input type="text" name="name" placeholder="Enter your name" required>
    <input type="password" name="password" placeholder="Enter your password" required>
    <button type="submit">Sign Up</button>
  </form>
  <div class="link">
    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>
</div>
</body>
</html>

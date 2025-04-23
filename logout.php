<?php
// logout.php: Log out the current user by destroying the session.
session_start();
session_destroy();
header("Location: login.php");
exit();
?>

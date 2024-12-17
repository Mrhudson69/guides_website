<?php
session_start();

// Destroy the session to log out the user
session_destroy();

echo "You have logged out. <a href='login.php'>Login again</a>";
?>

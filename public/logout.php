<?php
session_start(); // Start the session
session_unset(); // Unset all the session variables
session_destroy(); // Destroy the session
header("Location: index.php");
exit();

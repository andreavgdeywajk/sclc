<?php
session_start();
session_destroy(); // This will clear all session data
header("Location: login.php");
exit;

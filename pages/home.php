<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../phpScripts/auth/logout.php");
    exit();
}

echo '<h1>Hola '. $_SESSION['user'] .'<h1>';

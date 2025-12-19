<?php

require_once "../config/database.php";

if (!isset($_SESSION['user_name'])) {
    header("Location: ../auth/login.php");
    exit;
}
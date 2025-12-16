<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
  $pdo = new PDO("mysql:host=$servername;dbname=qodex_v1", $username, $password);
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>
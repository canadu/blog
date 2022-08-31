<?php
$db_name = 'mysql:host=localhost;dbname=blog_db';
$user_name = 'root';
$user_password = '';

try {
  $conn = new PDO($db_name, $user_name, $user_password);
  $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "接続失敗: " . $e->getMessage() . "\n";
  exit();
}

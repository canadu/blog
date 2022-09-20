<?php
require_once 'components/connect.php';
require_once 'components/functions.php';

session_start();

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = '';
  header('location:home.php');
}

if (isset($_POST['submit'])) {

  $name = h($_POST['name']);
  $email = h($_POST['email']);

  if (!empty($name)) {
    $update_name = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
    $update_name->execute([$name, $user_id]);
  }
  if (!empty($email)) {
    $select_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $select_email->execute([$email]);
    if ($select_email->rowCount() > 0) {
      $message[] = "このメールアドレスは使用できません。";
    } else {
      $update_email = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
      $update_email->execute([$email, $user_id]);
    }
  }
}

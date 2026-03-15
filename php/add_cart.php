<?php
session_start();
require '../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
  $customer_id = $_SESSION['user_id'];
  $product_count = $_POST['product_count'];
  $product_price = $_POST['product_price'];
  $product_id = $_POST['product_id'];

  $amount = $product_price * $product_count;
  // echo $amount . '</br>';
  // echo $customer_id;
  try {
    // echo 1;
    $query = $pdo->prepare("INSERT INTO cart (product_id, products_count, cart_amount, user_id) VALUES (?,?,?,?)");
    $query->execute([$product_id, $product_count, $amount, $customer_id]);
    header('Location: ../index.php');
  } catch (PDOException $e) {
    echo $e;
    $_SESSION['errors'] = ["Ошибка БД: " . $e->getMessage()];
  }
}

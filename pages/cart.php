<?php
require('../config.php');
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: ../pages/login.php');
}
$cart_items = [];
try {
  $sql = "SELECT c.*, p.product_name, p.product_description, p.product_image_path, p.product_price 
            FROM cart c 
            JOIN products p ON c.product_id = p.product_id 
            WHERE c.user_id = ?";

  $q = $pdo->prepare($sql);
  $q->execute([$_SESSION['user_id']]);
  $cart_items = $q->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $pe) {
  die("Ошибка: " . $pe->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=`device-width`, initial-scale=1.0">
  <title>Document</title>
</head>
<style>
  .nav_list {
    list-style: none;
    display: flex;
    gap: 3rem;
  }
</style>

<body>
  <h1>Cart</h1>
  <a href="../index.php" class="">назад</a>
  <ul class="nav_list flex gap-3">
    <!-- <?php print_r($cart_items) ?> -->
    <!-- <?php print_r($_SESSION['user_id']) ?> -->
    <?php if (!empty($cart_items)): ?>
      <?php foreach ($cart_items as $product): ?>
        <li class="product_item border gap-2 border-black flex rounded-lg flex-col p-3">
          <img src="../<?php echo $product['product_image_path'] ?>" alt="image" width="150">
          <h3 class="text-xl font-bold"><?php echo ($product['product_name']) ?></h3>
          <p class=""><?php echo ($product['product_description']) ?></p>
          <p class="">кол-во: <?php echo ($product['products_count']) ?></p>
          <span class="price">Цена: <?php echo $product['cart_amount'] ?> руб.</span>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Корзина пуста.</p>
    <?php endif; ?>
  </ul>
</body>

</html>
<?php
require('../config.php');
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: ./login.php');
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<style>
  .roleid_span {
    background: #000;
    padding: 1px 6px;
    color: white;
    border-radius: 2px;
  }

  .relative_block_cart {
    margin-top: 10px;
    position: relative;
  }

  .count_of_cart_items {
    padding: 3px 6.5px;
    font-size: 10px;
    background: red;
    color: white;
    position: absolute;
    bottom: 5px;
    border-radius: 100%;
  }

  .card_data_user {
    background: rgba(0, 0, 0, 0.1);
    padding: 10px;
    width: 300px;
    border-radius: 10px;
  }

  .avatar_card_user_data {
    background: rgba(0, 0, 0, 0.1);
    padding: 10px;
    width: 300px;
    border-radius: 10px;
    margin-top: 15px;
    margin-bottom: 10px;
  }
</style>

<body>
  <h1>profile</h1>
  <a href="../index.php">To Market</a>
  <div class="avatar_card_user_data" style="display:flex; gap:1rem;">
    <div class="">
      <img src="<?php echo $_SESSION['user_avatar_path'] ?>" style="margin-top:15px; width: 50px;" alt="">
    </div>
    <div class="">
      <div class="username">
        <p>Name: <?php echo $_SESSION['user_name'] ?></p>
      </div>
      <div class="user_surname">
        <p>SurName: <?php echo $_SESSION['user_surname'] ?></p>
      </div>
    </div>
  </div>
  <div class="card_data_user">
    <div class="user_email">
      <p>Email: <?php echo $_SESSION['user_email'] ?></p>
    </div>
    <div class="user_regd">
      <p>Registration's Date: <?php echo $_SESSION['user_regd'] ?></p>
    </div>
    <div class="user_rolename">
      <p>Role: <?php echo $_SESSION['user_rolename'] ?> <span class="roleid_span">#<?php echo $_SESSION['user_roleid'] ?></span></p>
    </div>
    <a href="./settings.php">To Settings</a>
  </div>
  <?php
  if ($_SESSION['user_roleid'] == '2') {
    echo '<a href="../admin.php">go to admin</a>' . '</br>';
  }
  ?>
  <div class="relative_block_cart">
    <a href="./cart.php">To cart</a><span class="count_of_cart_items"><?php echo count($cart_items) ?></span>
  </div>
</body>

</html>
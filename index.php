<?php
require('./config.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ./pages/login.php');
}
$products = [];
try {
    $q = $pdo->prepare('SELECT * FROM products ORDER BY product_id DESC');
    $q->execute();
    $products = $q->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $pe) {
    echo $pe->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<style>
    .hidden_ac {
        display: none;
    }
</style>

<body>
    <header class="header">
        <nav class="nav w-full ">
            <a href="" class="web_shop">Web Shop</a> <br>
            <a href="./pages/profile.php">To Profile</a> <br>
            <?php
            if (isset($_SESSION['user_id'])) {
                echo "welcome";
            }
            ?>
            <?php
            if ($_SESSION['user_roleid'] == 2) {
                header('Location: ./admin.php');
                exit();
            }
            ?>
            <a href="./php/logout.php">Выйти из системы</a> <br>
            <a href="./pages/cart.php">cart</a>
            <ul class="nav_list flex gap-3 ">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <li class="product_item border gap-2 border-black flex rounded-lg flex-col p-3">
                            <img src="<?php echo $product['product_image_path'] ?>" alt="image" width="150">
                            <h3 class="text-xl font-bold"><?php echo ($product['product_name']) ?></h3>
                            <p class=""><?php echo ($product['product_description']) ?></p>
                            <span class="price">Цена: <?php echo $product['product_price'] ?> руб.</span>
                            <button type="button" class="open_c_window">Купить</button>
                            <form class="hidden_ac gap-2 form_c" method="POST" action="./php/add_cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id'] ?>">
                                <input type="hidden" name="product_price" value="<?php echo $product['product_price'] ?>">
                                <input type="number" name="product_count" value="1" class="border p-1">
                                <button name="add_cart" class="product-add">В корзину</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Товаров пока нет.</p>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <script src="./scripts/show_cart_window.js"></script>
</body>

</html>
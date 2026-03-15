<?php
session_start();
require './config.php';

$products_admin = [];
try {
    $q = $pdo->prepare('SELECT * FROM products ORDER BY product_id DESC');
    $q->execute();
    $products_admin = $q->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $pe) {
    echo $pe->getMessage();
}

// admin user: em: a@a.com, p: Delfa!333
// user: e: u@u.com, p: Delfa!333

//---------------------------------------------------------------------------add product
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $admin_id = $_SESSION['user_id'];
        $image_path = null;

        if (isset($_FILES["image"]) && $_FILES["image"]['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'upload/';
            if (!file_exists($uploadDir)) mkdir($uploadDir);

            $extension = strtolower(pathinfo($_FILES["image"]['name'], PATHINFO_EXTENSION));
            $newName = uniqid() . "." . $extension;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                $image_path = $targetPath;
            }
        } else {
            $errors[] = "Файл изображения обязателен";
        }

        if (empty($name)) $errors[] = "Название обязательно";
        if ($price <= 0) $errors[] = "Некорректная цена";

        if (empty($errors)) {
            $query = $pdo->prepare("INSERT INTO products (product_name, product_description, product_price, product_image_path, adminId) VALUES (?, ?, ?, ?, ?)");
            $query->execute([$name, $description, $price, $image_path, $admin_id]);
            header('Location: admin.php?success=added');
            exit();
        }
    }
}

//---------------------------------------------------------------------------delete product
if (isset($_GET['delete']) && isset($_GET['ipath'])) {
    $p_id = $_GET['delete'];
    $d_image_path = $_GET['ipath'];
    try {
        $q = $pdo->prepare('DELETE FROM products WHERE product_id = ?;');
        $q->execute([$p_id]);
        unlink($d_image_path);
        header('Location: admin.php');
    } catch (PDOException $pe) {
        echo $pe->getMessage();
    }
}
// if ($_SERVER['REQUEST_METHOD'] == "POST") {
//     $id = $_POST['id'];
//     $ename = trim($_POST['ename']);
//     $edescription = $_POST['edescription'];
//     $eprice = $_POST['eprice'];
//     $eimage_path = $_POST['old_image'];
//     $eerrors = [];

//     if (isset($_FILES["eimage"]) && $_FILES["eimage"]['error'] !== UPLOAD_ERR_NO_FILE) {
//         $uploadDir = 'upload/';
//         if (!file_exists($uploadDir)) mkdir($uploadDir);

//         $eimage_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
//         $emaxSize = 1024 * 1024 * 10;

//         $file = $_FILES["eimage"];

//         if ($file['error'] !== UPLOAD_ERR_OK) {
//             $eerrors[] = "Ошибка загрузки: " . $file['error'];
//         } elseif (!in_array($file['type'], $eimage_types)) {
//             $eerrors[] = "Недопустимый формат";
//         } elseif ($file['size'] > $emaxSize) {
//             $eerrors[] = "Файл слишком большой";
//         } else {
//             $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
//             $newName = uniqid() . "." . $extension;
//             $targetPath = $uploadDir . $newName;

//             if (move_uploaded_file($file['tmp_name'], $targetPath)) {
//                 $eimage_path = $targetPath;
//             } else {
//                 $eerrors[] = "Ошибка при сохранении файла";
//             }
//         }
//     }
//     if (empty($ename)) $eerrors[] = "Название обязательно";
//     if (!is_numeric($eprice) || $eprice < 0) $eerrors[] = "Некорректная цена";

//     if (empty($eerrors)) {
//         try {
//             $query = $pdo->prepare("UPDATE products SET product_name=?, product_description=?, product_price=?, product_image_path=? WHERE id=?");
//             $query->execute([$ename, $edescription, $eprice, $eimage_path, $id]);

//             header('Location: admin.php');
//             exit();
//         } catch (PDOException $e) {
//             $eerrors[] = "Ошибка БД: " . $e->getMessage();
//         }
//     }
// }


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Document</title>
</head>
<style>
    .nav_list {
        display: flex;
        gap: 3rem;
    }

    li {
        text-decoration: none;
        list-style: none;
        padding: 2em;
        margin: 0;
        width: 1/3;
        border-radius: 0.5rem;
        border: 2px solid black;
    }

    .edit_window_u {
        display: none;
    }

    .edit_window_u.active_ef {
        display: block !important;
    }

    .add_product_invisible {
        display: none;
    }
</style>

<body>
    <h1>Администратор</h1>
    <a href="../logout.php">Выйти из системы</a>

    <div class="product-container">
        <?php if (!empty($errors)): ?>
            <h2>Ошибки:</h2>
            <ul class="errors-list">
                <?php
                foreach ($errors as $e) {
                    echo "<li>" . $e . "</li>";
                }
                ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <h1><?php echo $success ?></h1>
            <?php
            $success = "";
            ?>
        <?php endif; ?>


        <div class="edit_window_u" id="edit_window_u">
            <h1>Редактировать товар</h1>
            <button id="close_edit_window" type="button">Закрыть</button>
            <form method="POST" action="./php/edit_admin.php" enctype="multipart/form-data">
                <!-- ВАЖНО: скрытые поля -->
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="old_image" id="edit_old_image">

                <div class="e-form-group">
                    <p>Название товара:</p>
                    <input type="text" name="ename" id="ename" required>
                </div>

                <div class="form-group">
                    <p>Описание товара:</p>
                    <textarea name="edescription" id="edescription"></textarea>
                </div>

                <div class="form-group">
                    <p>Цена товара:</p>
                    <input type="number" step="0.01" name="eprice" id="eprice">
                </div>

                <div class="form-group">
                    <p>Изображение товара (оставьте пустым, чтобы не менять):</p>
                    <input type="file" name="eimage" id="eimage">
                </div>

                <button type="submit">Обновить товар</button>
            </form>
        </div>
        <div class="add_product">
            <h1>Добавить новый товар</h1>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <p>Название товара:</p>
                    <input type="text" name="name" required>
                </div>
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <p>Описание товара:</p>
                    <textarea name="description"></textarea>
                </div>

                <div class="form-group">
                    <p>Цена товара:</p>
                    <input type="number" name="price" required">
                </div>

                <div class="form-group">
                    <p>Изображение товара:</p>
                    <input type="file" name="image" required">
                    <br>
                    <i>Разрешены: JPG, PNG, GIF, WEBP (макс. 10МБ)</i>
                </div>

                <button>Добавить товар</button>
            </form>
        </div>
        <ul class="nav_list">
            <?php if (!empty($products_admin)): ?>
                <?php foreach ($products_admin as $product): ?>
                    <li class="product_item">
                        <img src="<?php echo $product['product_image_path'] ?>" alt="image" width="150">
                        <h3><?php echo ($product['product_name']) ?></h3>
                        <p><?php echo ($product['product_description']) ?></p>
                        <span class="price">Цена: <?php echo $product['product_price'] ?> руб.</span>
                        <button class="open_e_window"
                            data-id="<?= $product['product_id'] ?>"
                            data-name="<?= htmlspecialchars($product['product_name']) ?>"
                            data-description="<?= htmlspecialchars($product['product_description']) ?>"
                            data-price="<?= $product['product_price'] ?>"
                            data-image="<?= $product['product_image_path'] ?>">
                            Редактировать
                        </button>
                        <a href="?delete=<?php echo $product['product_id'] ?>&ipath=<?php echo urlencode($product['product_image_path']) ?>">delete</a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Товаров пока нет.</p>
            <?php endif; ?>
        </ul>

    </div>
    <script src="./scripts/show_edit_window_admin.js"></script>
</body>

</html>
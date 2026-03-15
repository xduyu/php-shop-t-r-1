<?php
session_start();
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $errors = [];

    $id = trim($_POST['id']);
    $ename = trim($_POST['ename']);
    $edescription = trim($_POST['edescription']);
    $eprice = floatval($_POST['eprice']);
    $old_image = $_POST['old_image'];
    $eimage_path = $old_image;
    if (empty($ename)) $errors[] = "Название обязательно";
    if ($eprice <= 0) $errors[] = "Некорректная цена";
    if ($id <= 0) $errors[] = "Неверный ID товара";
    if (isset($_FILES["eimage"]) && $_FILES["eimage"]['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'upload/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $extension = strtolower(pathinfo($_FILES["eimage"]['name'], PATHINFO_EXTENSION));
        $newName = uniqid() . "." . $extension;
        $targetPath = $uploadDir . $newName;

        if (move_uploaded_file($_FILES["eimage"]['tmp_name'], $targetPath)) {
            if (!empty($old_image) && file_exists($old_image)) {
                unlink($old_image);
            }
            $eimage_path = $targetPath;
        }
    }

    if (empty($errors)) {
        try {
            $query = $pdo->prepare("UPDATE products SET product_name=?, product_description=?, product_price=?, product_image_path=? WHERE product_id=?");
            $query->execute([$ename, $edescription, $eprice, $eimage_path, $id]);
            header('Location: ../admin.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['errors'] = ["Ошибка БД: " . $e->getMessage()];
        }
    } else {
        $_SESSION['errors'] = $errors;
    }
    header('Location: admin.php');
    exit();
}

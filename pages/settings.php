<?php
require('../config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: ./login.php');
  exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_name = $_POST['new_name'] ?? $_SESSION['user_name'];
  $new_surname = $_POST['new_surname'] ?? $_SESSION['user_surname'];
  $new_email = $_POST['new_email'] ?? $_SESSION['user_email'];
  $new_avatar_path = $_SESSION['user_avatar_path'] ?? null;

  if (empty($new_name)) $errors[] = 'Name can\'t be empty';
  if (empty($new_surname)) $errors[] = 'Surname can\'t be empty';
  if (empty($new_email)) $errors[] = 'Email can\'t be empty';
  if (isset($_FILES["new_avatar"]) && $_FILES["new_avatar"]['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../upload/avatars/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $extension = strtolower(pathinfo($_FILES["new_avatar"]['name'], PATHINFO_EXTENSION));
    $newName = uniqid() . "." . $extension;
    $targetPath = $uploadDir . $newName;

    if (move_uploaded_file($_FILES["new_avatar"]["tmp_name"], $targetPath)) {
      $new_avatar_path = $targetPath;
    } else {
      $errors[] = "Ошибка при сохранении файла";
    }
  }

  if (empty($errors)) {
    try {
      $query = $pdo->prepare("UPDATE `users` SET `UserName`=?, `UserSurname`=?, `UserEmail`=?, `User_Avatar`=? WHERE UserId=?");
      $query->execute([$new_name, $new_surname, $new_email, $new_avatar_path, $_SESSION['user_id']]);
      $_SESSION['user_name'] = $new_name;
      $_SESSION['user_surname'] = $new_surname;
      $_SESSION['user_email'] = $new_email;
      $_SESSION['user_avatar_path'] = $new_avatar_path;
      header('Location: ./profile.php');
      exit;
    } catch (PDOException $e) {
      $errors[] = "Ошибка БД: " . $e->getMessage();
    }
  }
  $_SESSION['errors'] = $errors;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <h1>Settings</h1>
  <a href="./profile.php">To Profile</a>
  <form action="" method="POST" enctype="multipart/form-data">
    <div style=" display: flex; gap: 3px;" class="">
      <div class="">
        <p>Avatar:</p>
        <input type="file" name="new_avatar" require id="">
      </div>
      <div class="">
        <p>Name:</p>
        <input type="text" name="new_name" require value="<?php echo $_SESSION['user_name'] ?>" id="">
      </div>
      <div class="">
        <p>SurName:</p>
        <input type="text" name="new_surname" require value="<?php echo $_SESSION['user_surname'] ?>" id="">
      </div>
      <div class="">
        <p>Email:</p>
        <input type="text" name="new_email" require value="<?php echo $_SESSION['user_email'] ?>" id="">
      </div>
    </div>
    <button style="margin-top: 10px;" type="submit">Update</button>
  </form>
</body>

</html>
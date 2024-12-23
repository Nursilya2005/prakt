<?php
require 'db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['fio'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['login'];
    $password = $_POST['password'];

    // Проверка пустых полей
    if (empty($full_name) || empty($email) || empty($phone) || empty($username) || empty($password)) {
        die('Все поля обязательны для заполнения.');
    }

    // Проверка форматов
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Некорректный формат email.');
    }
    if (strlen($password) < 6) {
        die('Пароль должен быть не менее 6 символов.');
    }

    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->bind_result($exists);
    $stmt->fetch();
    $stmt->close();

    if ($exists > 0) {
        die('Такой email или логин уже существует.');
    }

    $password_hash = md5($password);

    $stmt = $mysqli->prepare("INSERT INTO users (full_name, email, phone, username, password_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $phone, $username, $password_hash);
    if ($stmt->execute()) {
        $_SESSION['login'] = $username;
        header('Location: ../index.php'); 
        exit;
    } else {
        echo 'Ошибка регистрации. Попробуйте ещё раз.';
    }

    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="text-center mb-4">Регистрация</h1>
                <form id="registrationForm" action="" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="fio" class="form-label">ФИО</label>
                        <input type="text" class="form-control" id="fio" name="fio" placeholder="Иванов Иван Иванович" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="+79999999999" required pattern="^\+7\d{10}$">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="example@mail.ru" required>
                    </div>
                    <div class="mb-3">
                        <label for="login" class="form-label">Логин</label>
                        <input type="text" class="form-control" id="login" name="login" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                </form>
                <p class="text-center mt-4">Есть аккаунт? <a class="link-offset-2 link-underline link-underline-opacity-100" href="auth.php">Авторизоваться</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

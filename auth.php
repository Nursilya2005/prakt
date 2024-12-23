<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['login']);
    $password = trim($_POST['password']);
    $password_hash = md5($password);

    if (empty($username) || empty($password)) {
        $error_message = 'Логин и пароль не могут быть пустыми.';
    } else {
        if($username === "adminka" && $password === "password"){
            $_SESSION['admin'] = 1;
            header("Location: admin.php");
            exit();
        }
        else{
            echo "Не Админ";
        }
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && $user['password_hash'] === $password_hash) {
                $_SESSION['login'] = $username;
                header('Location: index.php');
                exit();
            } else {
                $error_message = 'Неверный логин или пароль.';
            }

            $stmt->close();
        } else {
            $error_message = 'Ошибка базы данных: ' . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="text-center mb-4">Авторизация</h1>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                <form id="loginForm" method="post">
                    <div class="mb-3">
                        <label for="login" class="form-label">Логин</label>
                        <input type="text" class="form-control" id="login" name="login" required>
                        <div class="invalid-feedback">Введите ваш логин.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Введите ваш пароль.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Войти</button>
                </form>
                <p class="text-center mt-4">Нет аккаунта? <a href="register.php" class="link-offset-2 link-underline link-underline-opacity-100">Зарегистрироваться</a></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            const form = event.target;
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

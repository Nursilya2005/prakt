<?php
session_start();
require 'db.php'; 

// Проверка авторизации
if (empty($_SESSION['login'])) {
    header('Location: register.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Проверка формата даты
        $date = $_POST['date'];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new Exception("Неверный формат даты. Ожидается формат YYYY-MM-DD.");
        }

        // SQL-запрос для вставки данных
        $sql = "INSERT INTO orders (user_id, address, phone, date, time, service_type, other_service_description, payment_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // Подготовка запроса
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $mysqli->error);
        }

        // Получение user_id
        $user_id = get_user_id($_SESSION['login']);

        // Привязка параметров
        $stmt->bind_param(
            "isssssss", 
            $user_id, 
            $_POST['address'], 
            $_POST['phone'], 
            $date, 
            $_POST['time'], 
            $_POST['serviceType'], 
            $_POST['otherServiceDescription'], 
            $_POST['paymentType']
        );

        // Выполнение запроса
        if (!$stmt->execute()) {
            throw new Exception("Ошибка выполнения запроса: " . $stmt->error);
        }

        $stmt->close();
        header("Location: ../");
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Ошибка: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Формирование заявки</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 form-container">
                <h1 class="text-center mb-4">Создание новой заявки</h1>
                <form method="POST">
                    <div class="mb-3">
                        <label for="address" class="form-label">Адрес</label>
                        <input type="text" class="form-control" name="address" id="address" placeholder="Введите адрес" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Контактный телефон</label>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="+7(XXX)-XXX-XX-XX" required>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Дата</label>
                        <input type="date" class="form-control" name="date" id="date" required>
                    </div>

                    <div class="mb-3">
                        <label for="time" class="form-label">Время</label>
                        <input type="time" class="form-control" name="time" id="time" required>
                    </div>

                    <div class="mb-3">
                        <label for="serviceType" class="form-label">Вид услуги</label>
                        <select class="form-select" name="serviceType" id="serviceType" required>
                            <option value="other" selected>Выберите услугу</option>
                            <option value="general">Общий клининг</option>
                            <option value="deep">Генеральная уборка</option>
                            <option value="postConstruction">Послестроительная уборка</option>
                            <option value="carpetCleaning">Химчистка ковров и мебели</option>
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="otherService">
                        <label class="form-check-label" for="otherService">
                            Иная услуга
                        </label>
                    </div>

                    <div class="mb-3" id="otherServiceDetails" style="display: none;">
                        <label for="otherServiceDescription" class="form-label">Описание услуги</label>
                        <textarea class="form-control" name="otherServiceDescription" id="otherServiceDescription" rows="3" placeholder="Опишите услугу"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Тип оплаты</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="paymentType" id="cash" value="cash" required>
                                <label class="form-check-label" for="cash">Наличные</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="paymentType" id="card" value="card" required>
                                <label class="form-check-label" for="card">Банковская карта</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Отправить заявку</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('otherService').addEventListener('change', function() {
            const otherServiceDetails = document.getElementById('otherServiceDetails');
            otherServiceDetails.style.display = this.checked ? 'block' : 'none';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

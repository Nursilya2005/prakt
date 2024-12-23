<?php
require 'db.php'; 
session_start();
if(empty($_SESSION['login'])){
    header('Location: register.php');
    exit();
}
$user_id = get_user_id($_SESSION['login']);
$user_order = get_user_orders($user_id);
function getStatusLabel($status) {
    switch ($status) {
        case 'pending':
            return '<span class="badge bg-secondary">В ожидании</span>';
        case 'in_progress':
            return '<span class="badge bg-warning text-dark">В работе</span>';
        case 'completed':
            return '<span class="badge bg-success">Выполнено</span>';
        case 'cancelled':
            return '<span class="badge bg-danger">Отменён</span>';
        default:
            return '<span class="badge bg-secondary">Неизвестно</span>';
    }
}

function getServiceTypeLabel($serviceType) {
    switch ($serviceType) {
        case 'general':
            return 'Общая уборка';
        case 'deep':
            return 'Глубокая уборка';
        case 'postConstruction':
            return 'После строительных работ';
        case 'carpetCleaning':
            return 'Чистка ковров';
        case 'other':
            return 'Другая услуга';
        default:
            return 'Неизвестная услуга';
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История заявок</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
        }
        .request-btn {
            background-color: #28a745;
            color: white;
        }
        .request-btn:hover {
            background-color: #218838;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

            <div class="text-left mb-4">
                    <form action="logout.php" method="post">
                        <button class="btn btn-danger">Выйти</button>
                    </form>
                </div>


                <h1 class="text-center mb-4">История заявок</h1>


                <div class="text-start mb-3">
                    <button class="btn request-btn" id="newRequestButton">Оставить новую заявку</button>
                </div>

                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Адрес</th>
                            <th>Дата и время</th>
                            <th>Услуга</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody id="requestsTable">
                    <?php foreach ($user_order as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= $order['address'] ?></td>
                            <td><?= $order['date'] ?> <?= $order['time'] ?></td>
                            <td>
                                <?= $order['service_type'] === 'other' && $order['other_service_description'] !== null
                                    ? 'Другая услуга: ' . $order['other_service_description']
                                    : getServiceTypeLabel($order['service_type']) ?>
                            </td>
                            <td>
                                <?php if ($order['status'] === 'cancelled' && $order['cancellation_reason'] !== null): ?>
                                    <div>
                                        <span class="badge bg-danger">Отменён</span>
                                        <p class="text-danger mt-1 mb-0"><strong>Причина:</strong> <?= $order['cancellation_reason'] ?></p>
                                    </div>
                                <?php else: ?>
                                    <?= getStatusLabel($order['status']) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('newRequestButton').addEventListener('click', function() {
            window.location.href = 'new_request.php';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

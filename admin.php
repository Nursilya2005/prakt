<?php
require 'db.php';
session_start();

// Проверка доступа администратора
if ($_SESSION['admin'] != 1) {
    header('HTTP/1.0 403 Forbidden');
    exit('Доступ запрещён');
}

// Функция получения всех заказов
function fetch_all_orders()
{
    global $mysqli;
    try {
        $result = $mysqli->query("SELECT * FROM orders");
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        echo 'Ошибка базы данных: ' . $e->getMessage();
        return [];
    }
}

// Обработка POST-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;

    if (!$order_id) {
        exit('Некорректный запрос');
    }

    if (isset($_POST['update_status'])) {
        $status = $_POST['status'];
        $reason = $_POST['cancellation_reason'] ?? null;

        if ($status === 'cancelled' && empty($reason)) {
            exit('Причина отмены обязательна');
        }

        try {
            $stmt = $mysqli->prepare("UPDATE orders SET status = ?, cancellation_reason = ? WHERE id = ?");
            $stmt->bind_param('ssi', $status, $reason, $order_id);
            $stmt->execute();
            echo "<div class='alert alert-success'>Статус успешно обновлён!</div>";
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Ошибка обновления: {$e->getMessage()}</div>";
        }
    }

    if (isset($_POST['delete_order'])) {
        try {
            $stmt = $mysqli->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->bind_param('i', $order_id);
            $stmt->execute();
            echo "<div class='alert alert-success'>Заявка успешно удалена!</div>";
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Ошибка удаления: {$e->getMessage()}</div>";
        }
    }
}

$orders = fetch_all_orders();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Панель администратора</h1>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Адрес</th>
            <th>Дата</th>
            <th>Услуга</th>
            <th>Статус</th>
            <th>Причина отмены</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <form method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['address']) ?></td>
                    <td><?= htmlspecialchars($order['date']) ?></td>
                    <td><?= htmlspecialchars($order['service_type']) ?></td>
                    <td>
                        <select name="status" class="form-select">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>В ожидании</option>
                            <option value="in_progress" <?= $order['status'] === 'in_progress' ? 'selected' : '' ?>>В работе</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Выполнено</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Отменён</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="cancellation_reason" class="form-control" value="<?= $order['cancellation_reason'] ?>">
                    </td>
                    <td>
                        <button type="submit" name="update_status" class="btn btn-primary btn-sm">Сохранить</button>
                        <button type="submit" name="delete_order" class="btn btn-danger btn-sm">Удалить</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>

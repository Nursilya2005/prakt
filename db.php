<?php
$host = 'localhost';
$dbname = 'pra';
$username = 'root';
$password = '';

// Подключение к базе данных
$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Ошибка подключения к базе данных: " . $mysqli->connect_error);
}

function get_user_id($login) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user['id'] ?? null;
    } else {
        echo 'Ошибка базы данных: ' . $mysqli->error;
        return null;
    }
}

function get_user_orders($user_id) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM orders WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $orders ?? null;
    } else {
        echo 'Ошибка базы данных: ' . $mysqli->error;
        return null;
    }
}
?>

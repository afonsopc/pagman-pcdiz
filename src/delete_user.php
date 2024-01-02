<?php
if (isset($_GET['id'])) {
    session_start();

    $db_host = $_ENV['DB_HOST'];
    $db_port = $_ENV['DB_PORT'];
    $db_database = $_ENV['DB_DATABASE'];
    $db_user = $_ENV['DB_USER'];
    $db_password = $_ENV['DB_PASSWORD'];

    if (!isset($_SESSION['user'])) {
        header('Location: /');
        exit();
    } else {
        if (!isset($_SESSION['user']['id'])) {
            header('Location: /');
            exit();
        }

        $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);

        $stmt = $db->prepare('SELECT role FROM users WHERE id = ?');
        $stmt->bind_param('i', $_SESSION['user']['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            header('Location: /');
            exit();
        }
        if ($user['role'] !== 'admin') {
            header('Location: /');
            exit();
        }
    }


    $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);

    $id = $_GET['id'];

    $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: /admin.php');
}

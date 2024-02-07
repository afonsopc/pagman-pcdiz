<?php
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

if (isset($_POST['name'])) {

    $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);

    $name = $_POST['name'];
    $cost = $_POST['cost'];
    $description = $_POST['description'];
    $image = $_POST['image'];

    $stmt = $db->prepare('INSERT INTO products (name, cost, description, image) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('siss', $name, $cost, $description, $image);
    $stmt->execute();

    header('Location: /');
}
?>


<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./tailwind.js"></script>
    <title>Admin</title>
</head>

<body>

    <nav class="w-full flex justify-between border-b-2 border-gray-100 p-2">
        <a href="/" class="flex h-10 w-40">
            <img src="/pcdiz.webp" alt="Logotipo da PCDIZ">
        </a>
        <div class="flex gap-3 justify-center align-middle items-center">
            <?php
            if (isset($_SESSION['user'])) {
                if (isset($_SESSION['user']['name']) && isset($_SESSION['user']['money'])) {
                    echo '<h1 class="text-xl text-nowrap">Bem-vindo, <span class="font-black">' . $_SESSION['user']['name'] . '</span> (' . $_SESSION['user']['money'] . ' €)</h1>';
                }
                if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                    echo '<a href="/admin.php" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Admin</a>';
                }
                
                echo '<a href="/orders.php" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Compras</a>';
                echo '<a href="/user_page.php" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Utilizador</a>';
                echo '<a href="/logout.php" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Logout</a>';
            } else {
                echo '<a href="/auth.php" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Login</a>';
            }
            ?>
        </div>
    </nav>
    <main class="p-5 flex flex-col gap-10 items-center align-middle">
        <section class="flex gap-5 flex-col">
            <h1 class="text-3xl font-black text-center">Utilizadores:</h1>

            <div class="w-full overflow-auto block">
                <table class="border-2 border-gray-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Saldo</th>
                            <th>Cargo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $db_host = $_ENV['DB_HOST'];
                        $db_port = $_ENV['DB_PORT'];
                        $db_database = $_ENV['DB_DATABASE'];
                        $db_user = $_ENV['DB_USER'];
                        $db_password = $_ENV['DB_PASSWORD'];

                        $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);
                        $stmt = $db->prepare('SELECT * FROM users');
                        $stmt->execute();
                        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                        if (!$users) {
                            echo '<tr class="text-center">';
                            echo '<td class="border-2 border-gray-100" colspan="5">Não há utilizadores.</td>';
                            echo '</tr>';
                        }
                        foreach ($users as $user) {
                            echo '<tr class="text-center">';
                            echo '<td class="p-5 border-2 border-gray-100">' . $user['id'] . '</td>';
                            echo '<td class="p-5 border-2 border-gray-100">' . $user['name'] . '</td>';
                            echo '<td class="p-5 border-2 border-gray-100">' . $user['money'] . '</td>';
                            echo '<td class="p-5 border-2 border-gray-100">' . $user['role'] . '</td>';
                            echo '
                                <td class="p-5 border-2 border-gray-100">
                                    <div class="h-full justify-center align-middle flex flex-col gap-3">
                                        <a href="/delete_user.php?id=' . $user['id'] . '" onclick="return confirm(\'Are you sure you want to delete this user?\');" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Apagar</a>
                                        <a href="/view_user.php?id=' . $user['id'] . '" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Ver</a>
                                    </div>
                                </td>
                            ';
                            echo '</tr>';
                        }
                        ?>
                </table>
            </div>
    </main>
</body>

</html>
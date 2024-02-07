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
    $stmt->bind_param('ssss', $name, $cost, $description, $image);
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
    <title>Adicionar Productos :)</title>
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
    <main class="flex flex-col justify-center items-center align-middle p-10 gap-5">
        <section>
            <h1 class="font-black text-5xl">Adicionar Producto</h1>
        </section>
        <section>
            <form action="" method="POST" class="border-gray-100 border-2 rounded-xl p-5 w-min text-center flex flex-col gap-5">
                <section class="flex flex-col gap-2">
                    <label for="name">Nome</label>
                    <input class="border-gray-200 rounded-md border-2" type="text" name="name" id="name">
                    <label for="cost">Custo (€)</label>
                    <input class="border-gray-200 rounded-md border-2" type="number" name="cost" id="cost">
                    <label for="description">Descrição</label>
                    <input class="border-gray-200 rounded-md border-2" type="text" name="description" id="description">
                    <label for="image">Imagem (URL)</label>
                    <input class="border-gray-200 rounded-md border-2" type="text" name="image" id="image">
                </section>
                <section class="pt-3 flex flex-col gap-3">
                    <input class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2 cursor-pointer" type="submit" value="Adicionar">
                </section>
            </form>
        </section>
    </main>

</body>

</html>
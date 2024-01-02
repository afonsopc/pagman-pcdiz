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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        header("Location: /");
        exit();
    }

    $name = $user['name'];
    $money = $user['money'];
    $role = $user['role'];
} else {
    header("Location: /");
    exit();
}

if (
    isset($_POST['name']) &&
    isset($_POST['money'])
) {
    $db_host = $_ENV['DB_HOST'];
    $db_port = $_ENV['DB_PORT'];
    $db_database = $_ENV['DB_DATABASE'];
    $db_user = $_ENV['DB_USER'];
    $db_password = $_ENV['DB_PASSWORD'];

    $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);

    $name = $_POST['name'];
    $money = $_POST['money'];

    if (isset($_POST['is_admin'])) {
        $role = 'admin';
    } else {
        $role = 'user';
    }

    $stmt = $db->prepare('UPDATE users SET name = ?, money = ?, role = ? WHERE id = ?');
    $stmt->bind_param('sssi', $name, $money, $role, $id);
    $stmt->execute();

    header("Location: /admin.php#$id");
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./tailwind.js"></script>
    <title>Visualizar Utilizador</title>
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
                echo '<a href="/logout.php" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Logout</a>';
            } else {
                echo '<a href="/auth.php" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Login</a>';
            }
            ?>
        </div>
    </nav>
    <main class="flex flex-col justify-center items-center p-10 align-middle gap-5">
        <section>
            <h1 class="font-black text-5xl">Editar o Utilizador</h1>
        </section>
        <section>
            <form action="" method="POST" class="border-gray-100 border-2 rounded-xl p-5 w-min text-center flex flex-col gap-5">
                <section class="flex flex-col gap-2 items-center">
                    <label for="name">Nome</label>
                    <input class="border-gray-200 p-2 rounded-md border-2" type="text" name="name" id="name" value="<?php echo $name; ?>">
                    <label for="money">Saldo (€)</label>
                    <input class="border-gray-200 p-2 rounded-md border-2" type="number" name="money" id="money" value="<?php echo $money; ?>">
                    <label for="is_admin">Admin</label>
                    <input class="border-gray-200 p-2 rounded-md border-2 size-8 cursor-pointer" type="checkbox" name="is_admin" id="is_admin" <?php if ($role === 'admin') echo "checked='checked'"; ?>>
                </section>
                <section class="pt-3 flex flex-col gap-3">
                    <input class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2 cursor-pointer" type="submit" value="Editar">
                </section>
            </form>
        </section>

        <section class="flex gap-5 flex-col text-center">
            <h1 class="font-black text-5xl">Compras do Utilizador</h1>

            <div class="w-full overflow-auto block">
                <table class="border-2 border-gray-100">
                    <thead>
                        <tr>
                            <th>ID da Compra</th>
                            <th>ID do Producto</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Imagem</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);
                        $stmt = $db->prepare('SELECT * FROM orders WHERE user_id = ?');
                        $stmt->bind_param('i', $id);
                        $stmt->execute();
                        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                        foreach ($orders as $order) {
                            echo '<tr id="' . $order['id'] . '">';
                            echo '<td class="border-2 border-gray-100 p-5">' . $order['id'] . '</td>';
                            echo '<td class="border-2 border-gray-100 p-5">' . $order['product_id'] . '</td>';
                            echo '<td class="border-2 border-gray-100 p-5">' . $order['product_name'] . '</td>';
                            echo '<td class="border-2 border-gray-100 p-5">' . $order['product_cost'] . '</td>';
                            echo '<td class="border-2 border-gray-100 p-5"><img class="h-80 w-80 aspect-square object-cover" src="' . $order['product_image'] . '" alt="' . $order['product_name'] . '"></td>';
                            echo '<td class="border-2 border-gray-100 p-5"><div class="p-5 h-full justify-center align-middle flex flex-col gap-3"><a href="/view.php?id=' . $order['product_id'] . '" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Ver</a></div></td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>
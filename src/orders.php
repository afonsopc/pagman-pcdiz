<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./tailwind.js"></script>
    <title>Compras</title>
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
    <main class="p-5 flex flex-col gap-10 items-center align-middle">
        <section class="flex gap-5 flex-col text-center">
            <h1 class="text-3xl font-black">Compras:</h1>

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
                        $db_host = $_ENV['DB_HOST'];
                        $db_port = $_ENV['DB_PORT'];
                        $db_database = $_ENV['DB_DATABASE'];
                        $db_user = $_ENV['DB_USER'];
                        $db_password = $_ENV['DB_PASSWORD'];

                        $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);
                        $stmt = $db->prepare('SELECT * FROM orders WHERE user_id = ?');
                        $stmt->bind_param('i', $_SESSION['user']['id']);
                        $stmt->execute();
                        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                        foreach ($orders as $order) {
                            $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
                            $stmt->bind_param('i', $order['product_id']);
                            $stmt->execute();
                            $product = $stmt->get_result()->fetch_assoc();

                            echo '<tr id="' . $order['id'] . '">';
                            echo '<td class="border-2 border-gray-100 p-5">' . $order['id'] . '</td>';
                            echo '<td class="border-2 border-gray-100 p-5">' . $product['id'] . '</td>';
                            echo '<td class="border-2 border-gray-100 p-5">' . $product['name'] . '</td>';
                            echo '<td class="border-2 border-gray-100 p-5">' . $order['cost'] . '</td>';
                            echo '<td class="border-2 border-gray-100 p-5"><img class="h-80 w-80 aspect-square object-cover" src="' . $product['image'] . '" alt="' . $product['name'] . '"></td>';
                            echo '<td class="border-2 border-gray-100 p-5"><div class="h-full justify-center align-middle flex flex-col gap-3"><a href="/view.php?id=' . $product['id'] . '" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Ver</a></div></td>';
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
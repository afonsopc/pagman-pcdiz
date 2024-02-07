<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: /");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./tailwind.js"></script>
    <title>Processo de Compra</title>
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
        <section>
            <a href="/view.php?id=<?php echo $_GET['id'] ?>" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2 w-min">Voltar</a>
        </section>
        <div class="flex flex-col justify-center items-center align-middle p-10 gap-5">
            <?php
            $id = $_GET['id'];

            $db_host = $_ENV['DB_HOST'];
            $db_port = $_ENV['DB_PORT'];
            $db_database = $_ENV['DB_DATABASE'];
            $db_user = $_ENV['DB_USER'];
            $db_password = $_ENV['DB_PASSWORD'];

            $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);
            $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();

            if (!$product) {
                echo "Product not Found";
                return;
            }

            $stmt = $db->prepare('SELECT money FROM users WHERE id = ?');
            $stmt->bind_param('i', $_SESSION['user']['id']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if (!$user) {
                echo "Precisas de estar autenticado para comprar produtos.";
                return;
            }

            if ($user['money'] < $product['cost']) {
                echo "Não tens dinheiro suficiente para comprar este produto.";
                return;
            }

            $user_money_left = $user['money'] - $product['cost'];

            $stmt = $db->prepare('UPDATE users SET money = ? WHERE id = ?');
            $stmt->bind_param('ii', $user_money_left, $_SESSION['user']['id']);
            $stmt->execute();

            if (isset($_SESSION['user']['money'])) {
                $_SESSION['user']['money'] = $user_money_left;
            }

            $stmt = $db->prepare('INSERT INTO orders (user_id, product_id, product_name, product_cost, product_description, product_image) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('iisiss', $_SESSION['user']['id'], $id, $product['name'], $product['cost'], $product['description'], $product['image']);
            $stmt->execute();

            $order_id = $db->insert_id;

            echo "Compra efetuada com sucesso! Obrigado por comprar na PCDIZ! O seu número de encomenda é " . $order_id;
            echo "<a class='hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2' href='/orders.php#$order_id'>Ver Compra</a>";
            ?>
        </div>
    </main>
</body>

</html>
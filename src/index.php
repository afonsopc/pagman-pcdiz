<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./tailwind.js"></script>
    <title>PCDIZ</title>
</head>

<body>
    <nav class="w-full flex justify-between border-b-2 border-gray-100 p-2">
        <a href="/" class="flex h-10 w-40">
            <img src="/pcdiz.webp" alt="Logotipo da PCDIZ">
        </a>
        <div class="flex gap-3 justify-center align-middle items-center">
            <?php
            if (isset($_SESSION['user']['id'])) {
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
    <main class="p-5 flex flex-col gap-24 items-center align-middle">
        <section class="flex gap-5 flex-col">
            <h1 class="text-3xl font-black text-center">Produtos:</h1>
            <div class="w-full overflow-auto block">
                <table class="border-2 border-gray-100 text-center">
                    <thead>
                        <tr>
                            <th class="border-2 border-gray-100">ID</th>
                            <th class="border-2 border-gray-100">Nome</th>
                            <th class="border-2 border-gray-100">Preço</th>
                            <th class="border-2 border-gray-100">Imagem</th>
                            <th class="border-2 border-gray-100">Ações</th>
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
                        $stmt = $db->prepare('SELECT * FROM products');
                        $stmt->execute();
                        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        if (!$products) {
                            echo '<tr class="text-center">';
                            echo '<td class="border-2 border-gray-100" colspan="5">Não há produtos.</td>';
                            echo '</tr>';
                        }
                        foreach ($products as $product) {
                            echo '<tr class="text-center">';
                            echo '<td class="border-2 border-gray-100 p-5">' . $product['id'] . '</td>';
                            echo '<td class="border-2 border-gray-100 p-5">' . $product['name'] . '</td>';
                            echo '<td class="border-2 border-gray-100 p-5">' . $product['cost'] . '€</td>';
                            echo '<td class="border-2 border-gray-100 p-5"><img src="' . $product['image'] . '" alt="Imagem do produto" class="h-80 w-80 aspect-square object-cover"></td>';
                            if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                                echo '<td class="border-2 border-gray-100 p-5"><div class="h-full justify-center align-middle flex flex-col gap-3"><a href="/edit.php?id=' . $product['id'] . '" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Editar</a><a href="/delete.php?id=' . $product['id'] . '" onclick="return confirm(\'Are you sure you want to delete this product?\');" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Apagar</a><a href="/view.php?id=' . $product['id'] . '" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Ver</a></div></td>';
                            } else {
                                echo '<td class="border-2 border-gray-100 p-5"><div class="h-full justify-center align-middle flex flex-col gap-3"><a href="/view.php?id=' . $product['id'] . '" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Ver</a></div></td>';
                            }
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <?php
            if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                echo '<a href="/add.php" class="text-center hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2">Adicionar produto</a>';
            }
            ?>
        </section>
        <section class="flex flex-col px-5 gap-2 text-center bg-black text-white items-center justify-center align-middle w-screen h-80">
            <p>Website desenvolvido por</p>
            <a href="https://omelhorsite.pt" target="_blank" rel="noopener noreferrer" class="hover:bg-gray-800 px-5 border-gray-700 rounded-lg p-2 border-2">Afonso Maria Pacheco de Castro Pereira Coutinho</a>
        </section>
    </main>
</body>

</html>
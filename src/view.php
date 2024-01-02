<?php
session_start();

if (isset($_GET['id'])) {
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
        header("Location: /");
        exit();
    }

    $name = $product['name'];
    $cost = $product['cost'];
    $description = $product['description'];
    $image = $product['image'];
} else {
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
    <title><?php echo $name ?></title>
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
        <section>
            <div class="flex flex-col lg:flex-row gap-5">
                <img src="<?php echo $image ?>" alt="<?php echo $name ?>" class="w-full md:w-1/2 lg:w-96">
                <div class="flex flex-col gap-5 items-center">
                    <a href="/buy.php?id=<?php echo $id ?>" class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2 w-min">Comprar</a>
                    <div class="border-gray-200 border-2 rounded-lg p-5 flex flex-col gap-2">
                        <h1 class="text-3xl font-black text-center"><?php echo $name ?></h1>
                        <h2 class="text-2xl text-center"><span class="font-bold"><?php echo $cost ?></span> €</h2>
                        <p class="text-xl text-center"><?php echo $description ?></p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
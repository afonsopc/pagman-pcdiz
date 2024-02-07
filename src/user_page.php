<?php
session_start();

$db_host = $_ENV['DB_HOST'];
$db_port = $_ENV['DB_PORT'];
$db_database = $_ENV['DB_DATABASE'];
$db_user = $_ENV['DB_USER'];
$db_password = $_ENV['DB_PASSWORD'];

if (!isset($_SESSION['user']['id'])) {
    header('Location: /');
    exit();
} 
$id = $_SESSION['user']['id'];

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
$email = $user['email'];
$phone = $user['phone'];

if (
    isset($_POST['name']) &&
    isset($_POST['email']) &&
    isset($_POST['phone'])
) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    if ($password !== '') {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->bind_param('si', $password, $id);
        $stmt->execute();
    }

    if (isset($_POST['is_admin'])) {
        $role = 'admin';
    } else {
        $role = 'user';
    }

    $stmt = $db->prepare('UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?');
    $stmt->bind_param('sssi', $name, $email, $phone, $id);
    $stmt->execute();

    if ($password !== '') {
        session_destroy();
    }
    else {
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $_SESSION['user'] = $user;
    }

    header('Location: /');
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./tailwind.js"></script>
    <title>Pagina do Utilizador</title>
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
    <main class="flex flex-col justify-center items-center p-10 align-middle gap-5">
        <section>
            <h1 class="font-black text-5xl">Editar o Utilizador</h1>
        </section>
        <section>
            <form action="" method="POST" class="border-gray-100 border-2 rounded-xl p-5 w-min text-center flex flex-col gap-5">
                <section class="flex flex-col gap-2 items-center">
                    <label for="name">Nome</label>
                    <input class="border-gray-200 p-2 rounded-md border-2" type="text" name="name" id="name" value="<?php echo $name; ?>">
                    <label for="money">Email</label>
                    <input class="border-gray-200 p-2 rounded-md border-2" type="email" name="email" id="email" value="<?php echo $email; ?>">
                    <label for="money">Telefone</label>
                    <input class="border-gray-200 p-2 rounded-md border-2" type="tel" maxlength="9" name="phone" id="phone" value="<?php echo $phone; ?>">
                    <label for="money">Password</label>
                    <input class="border-gray-200 p-2 rounded-md border-2" type="password" minlength="8" name="password" id="password">
                </section>
                <section class="pt-3 flex flex-col gap-3">
                    <input class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2 cursor-pointer" type="submit" value="Editar">
                </section>
            </form>
        </section>
    </main>
</body>

</html>
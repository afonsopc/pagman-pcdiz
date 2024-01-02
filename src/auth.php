<?php
session_start();

$db_host = $_ENV['DB_HOST'];
$db_port = $_ENV['DB_PORT'];
$db_database = $_ENV['DB_DATABASE'];
$db_user = $_ENV['DB_USER'];
$db_password = $_ENV['DB_PASSWORD'];
$default_admin_username = $_ENV['DEFAULT_ADMIN_USERNAME'];

if (isset($_SESSION['user'])) {
    header('Location: /');
    exit();
}

if (isset($_POST['login'])) {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);
    $stmt = $db->prepare('SELECT * FROM users WHERE name = ?');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: /');
        exit();
    } else {
        $loginError = 'Nome ou palavra-passe incorretos.';
    }
} else if (isset($_POST['createAccount'])) {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $db = new mysqli($db_host, $db_user, $db_password, $db_database, $db_port);
    $stmt = $db->prepare('SELECT * FROM users WHERE name = ?');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $loginError = 'Nome de utilizador já existe.';
    } else {
        $stmt = $db->prepare('INSERT INTO users (name, password, role) VALUES (?, ?, ?)');
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        if ($name === $default_admin_username) {
            $role = 'admin';
        } else {
            $role = 'user';
        }
        $stmt->bind_param('sss', $name, $password_hash, $default_role);
        $stmt->execute();
        $loginError = 'Conta criada com sucesso.';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./tailwind.js"></script>
    <title>Login</title>
</head>


<body>
    <nav class="w-full flex justify-between border-b-2 border-gray-100 p-2">
        <a href="/" class="flex h-10 w-40">
            <img src="/pcdiz.webp" alt="Logotipo da PCDIZ">
        </a>
    </nav>
    <main class="flex flex-col justify-center items-center align-middle p-10 gap-5">

        <section>
            <h1 class="font-black text-5xl">Authentication</h1>
        </section>
        <section>

            <form method="POST" class="border-gray-100 border-2 rounded-xl p-5 w-min text-center flex flex-col gap-5" action="">
                <section class="flex flex-col gap-2">
                    <label for="username">Nome:</label>
                    <input class="border-gray-200 rounded-md border-2 p-2" type="text" name="name" id="name" required><br>
                    <label for="password">Palavra-Passe:</label>
                    <input class="border-gray-200 rounded-md border-2 p-2" type="password" name="password" id="password" required><br>
                </section>
                <section class="pt-3 flex flex-col gap-3">
                    <input class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2 cursor-pointer" type="submit" name="login" value="Login">
                    <input class="hover:bg-gray-100 px-5 border-gray-100 rounded-lg p-2 border-2 cursor-pointer" type="submit" name="createAccount" value="Criar Conta">
                </section>
            </form>
        </section>
        <section>
            <?php if (isset($loginError)) { ?>
                <p><?php echo $loginError; ?></p>
            <?php } ?>
        </section>
    </main>
</body>

</html>
<?php
// Запуск сессии для хранения авторизации
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключение к базе данных (PDO в db.php)
require_once __DIR__ . '/db.php';

// Если пользователь уже авторизован — просто пропускаем дальше
if (isset($_SESSION['user_id'])) {
    return;
}

$error = '';

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Пожалуйста, заполните логин и пароль.';
    } else {
        // Ищем пользователя в таблице users
        $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            $stored = $user['password'];
            $ok = false;

            // Если пароль похож на bcrypt-хеш — пробуем password_verify
            if (preg_match('/^\$2y\$\d+\$/', $stored)) {
                if (password_verify($password, $stored)) {
                    $ok = true;
                }
            } else {
                // Иначе считаем, что пароль хранится в базе в открытом виде
                if (hash_equals($stored, $password)) {
                    $ok = true;
                }
            }

            if ($ok) {
                // Авторизация успешна — сохраняем в сессию
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Неверный логин или пароль.';
            }
        } else {
            $error = 'Неверный логин или пароль.';
        }
    }
}

// Если мы здесь — пользователь не авторизован, показываем форму и выходим
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в админ-панель</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
<div class="container">
    <h1>Администрирование сайта</h1>
    <p>Для входа в панель администратора введите логин и пароль.</p>

    <?php if (!empty($error)): ?>
        <div class="error" style="color: red; margin-bottom: 10px;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <div class="form-group">
            <label for="username">Логин:</label><br>
            <input type="text" name="username" id="username" required>
        </div>

        <div class="form-group" style="margin-top: 8px;">
            <label for="password">Пароль:</label><br>
            <input type="password" name="password" id="password" required>
        </div>

        <div style="margin-top: 12px;">
            <button type="submit">Войти</button>
        </div>
    </form>
</div>
</body>
</html>
<?php
// Очень важно: дальше код admin/index.php выполняться не должен
exit;

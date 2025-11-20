<?php
require_once 'auth.php'; // здесь происходит проверка и/или показ формы
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
<div class="container">
    <h1>Административная панель</h1>
    <p>
        Вы вошли как
        <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'admin', ENT_QUOTES, 'UTF-8'); ?></strong>.
    </p>

    <h2>Доступные действия</h2>
    <ul>
        <li><a href="/static/index.html">Открыть статическую страницу про погоду</a></li>
        <li><a href="/dynamic/weather.php">Проверить динамическую страницу погоды</a></li>
        <li><a href="/dynamic/datetime.php">Проверить вывод даты и времени</a></li>
        <!-- Здесь можно добавить любые админ-фичи позже -->
    </ul>
</div>
</body>
</html>

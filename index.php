<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница сервера</title>
    <link rel="stylesheet" href="style.css" type="text/css"/>
</head>
<body>
<div class="container">
    <h1>Тестовый проект: погода</h1>
    <p>Это стартовая страница для демонстрации конфигурации nginx + Apache + PHP + MySQL.</p>

    <ul>
        <li><a href="/static/index.html">Статическая страница о погоде</a></li>
        <li><a href="/static/info.html">Вторая статическая страница (описание сервиса)</a></li>
        <li><a href="/dynamic/weather.php">Динамическая страница с погодой (БД)</a></li>
        <li><a href="/dynamic/datetime.php">Динамическая страница с датой и временем</a></li>
        <li><a href="/admin/">Админ-панель (логин через БД)</a></li>
    </ul>
</div>
</body>
</html>

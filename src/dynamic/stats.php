<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../stats/stats_lib.php';
$charts = generateAllCharts();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика по фикстурам</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body class="<?php echo isset($_SESSION['theme']) ? 'theme-' . htmlspecialchars($_SESSION['theme']) : ''; ?>">
<div class="container">
    <h1>Страница статистики</h1>
    <p>
        На этой странице показаны три примера графиков,
        построенных по сгенерированным тестовым данным (фикстурам).
        Для каждого графика используется разный тип визуализации,
        а на изображение нанесён полупрозрачный водяной знак.
    </p>

    <?php foreach ($charts as $chart): ?>
        <h2><?php echo htmlspecialchars($chart['title']); ?></h2>
        <p>
            <img src="/uploads/charts/<?php echo htmlspecialchars($chart['file']); ?>"
                 alt="<?php echo htmlspecialchars($chart['title']); ?>"
                 style="max-width: 100%; height: auto; border-radius: 8px;">
        </p>
    <?php endforeach; ?>

    <p style="margin-top: 16px;">
        <a href="/dynamic/profile.php">Профиль и настройки</a> |
        <a href="/dynamic/weather.php">Динамическая погода</a> |
        <a href="/dynamic/upload.php">Загрузка PDF</a> |
        <a href="/admin/">Админ-панель</a>
    </p>
</div>
</body>
</html>

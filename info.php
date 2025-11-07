<?php
function run_command(string $cmd): string {
    $output = shell_exec($cmd . ' 2>&1');
    if ($output === null) {
        return "Команда не выполнилась или shell_exec отключён в php.ini";
    }
    return $output;
}

$sections = [
    "Общая информация о системе (uname -a)" => "uname -a",
    "Пользователь, под которым работает Apache (whoami)" => "whoami",
    "Информация о пользователе (id)" => "id",
    "Текущая директория и содержимое (pwd, ls -la)" => "pwd && ls -la",
    "Топ-10 процессов (ps aux | head -10)" => "ps aux | head -10",
    "Занятое место на диске (df -h)" => "df -h",
    "Память (free -m)" => "free -m"
];
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Информация о сервере</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #111827;
            color: #e5e7eb;
            padding: 20px;
        }
        h1 {
            color: #a5b4fc;
        }
        .block {
            margin-bottom: 25px;
            padding: 15px;
            border-radius: 8px;
            background: #030712;
            border: 1px solid #374151;
        }
        .block h2 {
            margin: 0 0 10px;
            font-size: 1rem;
            color: #f9fafb;
        }
        pre {
            margin: 0;
            font-family: "JetBrains Mono", "Fira Code", monospace;
            font-size: 0.85rem;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <h1>Информационно-административная страница сервера</h1>
    <p>Apache + PHP: отображение вывода базовых Unix-команд.</p>

    <?php foreach ($sections as $title => $cmd): ?>
        <div class="block">
            <h2><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>
            <pre><strong>$ <?= htmlspecialchars($cmd, ENT_QUOTES, 'UTF-8') ?></strong>
<?= htmlspecialchars(run_command($cmd), ENT_QUOTES, 'UTF-8') ?></pre>
        </div>
    <?php endforeach; ?>
</body>
</html>

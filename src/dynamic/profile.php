<?php

require_once __DIR__ . '/../session.php';

$availableThemes = ['light', 'dark', 'colorblind'];
$availableLangs  = ['ru', 'en'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $theme    = $_POST['theme'] ?? 'light';
    $lang     = $_POST['lang'] ?? 'ru';

    if ($username === '') {
        $username = 'Гость';
    }

    if (!in_array($theme, $availableThemes, true)) {
        $theme = 'light';
    }

    if (!in_array($lang, $availableLangs, true)) {
        $lang = 'ru';
    }

    $_SESSION['username'] = $username;
    $_SESSION['theme']    = $theme;
    $_SESSION['lang']     = $lang;

    header('Location: profile.php');
    exit;
}


$username = $_SESSION['username'] ?? 'Гость';
$theme    = $_SESSION['theme'] ?? 'light';
$lang     = $_SESSION['lang'] ?? 'ru';

$bodyClass = 'theme-' . $theme;


$texts = [
    'ru' => [
        'title'          => 'Профиль и настройки',
        'intro'          => 'Здесь можно выбрать имя, тему оформления и язык интерфейса. Эти параметры сохраняются в сессии (Redis) и используются для персонализации контента.',
        'username_label' => 'Имя пользователя',
        'theme_label'    => 'Тема оформления',
        'lang_label'     => 'Язык интерфейса',
        'submit'         => 'Сохранить настройки',
        'current'        => 'Текущие настройки',
        'theme_light'    => 'Светлая',
        'theme_dark'     => 'Тёмная',
        'theme_cb'       => 'Для людей с цветовой слепотой',
        'lang_ru'        => 'Русский',
        'lang_en'        => 'Английский',
        'greeting'       => 'Привет',
    ],
    'en' => [
        'title'          => 'Profile & settings',
        'intro'          => 'Here you can choose your name, theme and language. These values are stored in the session (Redis) and used to personalize the content.',
        'username_label' => 'User name',
        'theme_label'    => 'Theme',
        'lang_label'     => 'Language',
        'submit'         => 'Save settings',
        'current'        => 'Current settings',
        'theme_light'    => 'Light',
        'theme_dark'     => 'Dark',
        'theme_cb'       => 'Color-blind friendly',
        'lang_ru'        => 'Russian',
        'lang_en'        => 'English',
        'greeting'       => 'Hello',
    ],
];

$t = $texts[$lang] ?? $texts['ru'];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($t['title']); ?></title>
    <link rel="stylesheet" href="/style.css" type="text/css">
</head>
<body class="<?php echo htmlspecialchars($bodyClass); ?>">
<div class="container">
    <h1><?php echo htmlspecialchars($t['title']); ?></h1>
    
    <p><?php echo htmlspecialchars($t['intro']); ?></p>

    <div class="profile-greeting">
        <?php echo htmlspecialchars($t['greeting'] . ', ' . $username . '!'); ?>
    </div>

    <form method="post" class="profile-form">
        <div class="form-group">
            <label for="username"><?php echo htmlspecialchars($t['username_label']); ?></label>
            <input type="text" id="username" name="username"
                   value="<?php echo htmlspecialchars($username); ?>">
        </div>

        <div class="form-group">
            <label><?php echo htmlspecialchars($t['theme_label']); ?></label>
            <div class="radio-row">
                <label>
                    <input type="radio" name="theme" value="light" <?php if ($theme === 'light') echo 'checked'; ?>>
                    <?php echo htmlspecialchars($t['theme_light']); ?>
                </label>
                <label>
                    <input type="radio" name="theme" value="dark" <?php if ($theme === 'dark') echo 'checked'; ?>>
                    <?php echo htmlspecialchars($t['theme_dark']); ?>
                </label>
                <label>
                    <input type="radio" name="theme" value="colorblind" <?php if ($theme === 'colorblind') echo 'checked'; ?>>
                    <?php echo htmlspecialchars($t['theme_cb']); ?>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label><?php echo htmlspecialchars($t['lang_label']); ?></label>
            <div class="radio-row">
                <label>
                    <input type="radio" name="lang" value="ru" <?php if ($lang === 'ru') echo 'checked'; ?>>
                    <?php echo htmlspecialchars($t['lang_ru']); ?>
                </label>
                <label>
                    <input type="radio" name="lang" value="en" <?php if ($lang === 'en') echo 'checked'; ?>>
                    <?php echo htmlspecialchars($t['lang_en']); ?>
                </label>
            </div>
        </div>

        <button type="submit"><?php echo htmlspecialchars($t['submit']); ?></button>
    </form>

    <h2><?php echo htmlspecialchars($t['current']); ?></h2>
    <ul class="current-settings">
        <li><span class="badge">username</span> <?php echo htmlspecialchars($username); ?></li>
        <li><span class="badge">theme</span> <?php echo htmlspecialchars($theme); ?></li>
        <li><span class="badge">lang</span> <?php echo htmlspecialchars($lang); ?></li>
    </ul>

    <p style="margin-top: 16px;">
        <a href="/dynamic/weather.php">Перейти к динамической погоде</a> |
        <a href="/static/index.html">Статическая страница</a> |
        <a href="/admin/">Админ-панель</a>
    </p>
</div>
</body>
</html>

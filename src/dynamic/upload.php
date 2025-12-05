<?php
require_once __DIR__ . '/../session.php';


$uploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/uploads/';


if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}


$message = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        $message = "Ошибка загрузки файла.";
    } else {
        $file     = $_FILES['pdf'];
        $filename = basename($file['name']);

       
        if ($file['type'] !== 'application/pdf') {
            $message = "Можно загружать только PDF!";
        } else {
            
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $message = "Файл успешно загружен!";
            } else {
                $message = "Ошибка сохранения файла.";
            }
        }
    }
}


$files = [];
foreach (glob($uploadDir . "*.pdf") as $path) {
    $files[] = basename($path);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка PDF</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
    <h1>Загрузка PDF файлов</h1>

    <?php if ($message): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Выберите PDF:</label>
        <input type="file" name="pdf" accept="application/pdf" required>
        <button type="submit">Загрузить</button>
    </form>

    <h2>Загруженные файлы</h2>
    <ul>
        <?php if (empty($files)): ?>
            <li>Пока ничего не загружено.</li>
        <?php else: ?>
            <?php foreach ($files as $pdf): ?>
                <li>
                    <a href="/uploads/<?php echo rawurlencode($pdf); ?>" target="_blank">
                        <?php echo htmlspecialchars($pdf); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <p style="margin-top:16px;">
        <a href="/dynamic/profile.php">Профиль</a> |
        <a href="/dynamic/weather.php">Погода</a> |
        <a href="/admin/">Админ</a>
    </p>
</div>
</body>
</html>

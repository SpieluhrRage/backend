<?php

use Faker\Factory as FakerFactory;

function generateFixtures(int $count = 80): array
{
    $faker   = FakerFactory::create('en_US'); 
    $devices = ['desktop', 'mobile', 'tablet'];

    $data = [];
    for ($i = 1; $i <= $count; $i++) {
        $data[] = [
            'id'           => $i,
            'city'         => $faker->city,                       
            'age'          => $faker->numberBetween(18, 45),      
            'day_of_week'  => $faker->numberBetween(1, 7),        
            'votes'        => $faker->numberBetween(0, 100),      
            'device'       => $faker->randomElement($devices),    
        ];
    }

    return $data;
}


function generateAllCharts(): array
{
    $documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
    $chartsDir = $documentRoot . '/uploads/charts/';

    if (!is_dir($chartsDir)) {
        mkdir($chartsDir, 0777, true);
    }

    $fixtures = generateFixtures(80);

    $cityCounts   = [];
    $dayVotes     = array_fill(1, 7, 0);
    $deviceCounts = [];

    foreach ($fixtures as $row) {
        $city = $row['city'];
         if (!isset($cityVotes[$city])) {
            $cityVotes[$city] = 0;
        }
            $cityVotes[$city] += (int)$row['votes'];

        // 2) Суммарное количество голосов по дням недели
        $day = $row['day_of_week'];
        $dayVotes[$day] += $row['votes'];

        // 3) Распределение по типам устройств
        $device = $row['device'];
        if (!isset($deviceCounts[$device])) {
            $deviceCounts[$device] = 0;
        }
        $deviceCounts[$device]++;
    }

    $charts = [];

    // График 1: столбчатая диаграмма по городам
    $file1 = 'chart_cities.png';
    buildBarChart(
    $cityVotes,
    $chartsDir . $file1,
    'Total votes by city'
    );

    $charts[] = [
        'file'  => $file1,
        'title' => 'Fixture Distribution by Cities',
    ];

    // График 2: линейный график по дням недели
    $file2 = 'chart_day_votes.png';
    buildLineChart(
        $dayVotes,
        $chartsDir . $file2,
        'Sum of Votes by Days of the Week'
    );
    $charts[] = [
        'file'  => $file2,
        'title' => 'Sum of Votes by Days of the Week',
    ];

    // График 3: круговая диаграмма по устройствам
    $file3 = 'chart_devices.png';
    buildPieChart(
        $deviceCounts,
        $chartsDir . $file3,
        'Distribution by Device Types'
    );
    $charts[] = [
        'file'  => $file3,
        'title' => 'Distribution by Device Types',
    ];

    return $charts;
}


function buildBarChart(array $data, string $path, string $title): void
{
    arsort($data);

    $width  = 900;
    $height = 450;

    $img = imagecreatetruecolor($width, $height);

    // Colors
    $bg       = imagecolorallocate($img, 245, 247, 252);
    $axis     = imagecolorallocate($img, 30, 41, 59);
    $barColor = imagecolorallocate($img, 59, 130, 246);
    $text     = imagecolorallocate($img, 15, 23, 42);
    $water    = imagecolorallocatealpha($img, 15, 23, 42, 85);

    imagefilledrectangle($img, 0, 0, $width, $height, $bg);

    // Margins
    $marginLeft   = 70;
    $marginRight  = 30;
    $marginTop    = 50;
    $marginBottom = 90; 

    // Axes
    imageline($img, $marginLeft, $marginTop, $marginLeft, $height - $marginBottom, $axis);
    imageline($img, $marginLeft, $height - $marginBottom, $width - $marginRight, $height - $marginBottom, $axis);

    $categories = array_keys($data);
    $values     = array_values($data);

    if (count($values) === 0) {
        imagestring($img, 5, 10, 10, $title, $text);
        imagepng($img, $path);
        imagedestroy($img);
        return;
    }

    $max = max($values);
    if ($max <= 0) $max = 1;

    $plotWidth  = $width - $marginLeft - $marginRight;
    $plotHeight = $height - $marginTop - $marginBottom;

    $n = count($values);
    $slotWidth = $plotWidth / $n;
    $barWidth = (int)min(22, max(8, $slotWidth * 0.45));

    // Центрирование бара внутри слота
    $barOffset = (int)(($slotWidth - $barWidth) / 2);

    // Grid / ticks по Y 
    $ticks = 5;
    for ($i = 0; $i <= $ticks; $i++) {
        $y = (int)($height - $marginBottom - ($plotHeight * $i / $ticks));
        imageline($img, $marginLeft, $y, $width - $marginRight, $y, imagecolorallocate($img, 226, 232, 240));

        $val = (int)round($max * $i / $ticks);
        imagestring($img, 2, 8, $y - 7, (string)$val, $text);
    }

    // Bars
    for ($i = 0; $i < $n; $i++) {
        $val = (int)$values[$i];

        $x1 = (int)($marginLeft + $i * $slotWidth + $barOffset);
        $x2 = $x1 + $barWidth;

        $barH = (int)round($plotHeight * ($val / $max));
        $y2 = $height - $marginBottom;
        $y1 = $y2 - $barH;

        imagefilledrectangle($img, $x1, $y1, $x2, $y2, $barColor);

        // Значение над столбиком
        imagestring($img, 2, $x1 - 2, $y1 - 15, (string)$val, $text);

        // Подпись города по X 
        $label = (string)$categories[$i];
        if (strlen($label) > 10) {
            $label = substr($label, 0, 10) . '…';
        }

       // Вертикальная подпись города (снизу вверх)
        $label = (string)$categories[$i];
        if (strlen($label) > 12) {
            $label = substr($label, 0, 12) . '…';
        }

        $labelX = $x1 + (int)($barWidth / 2) - 6;

        $labelY = $height - $marginBottom + 80;

imagestringup($img, 2, $labelX, $labelY, $label, $text);

    }

    // Title
    imagestring($img, 5, 10, 15, $title, $text);

    // Watermark
    $watermark = 'Timur Platonov, IKBO-22-23';
    $wmX = $width - 10 - strlen($watermark) * 6;
    $wmY = $height - 25;
    imagestring($img, 3, $wmX, $wmY, $watermark, $water);

    imagepng($img, $path);
}
 

function buildLineChart(array $data, string $path, string $title): void
{
    $width  = 800;
    $height = 400;

    $img = imagecreatetruecolor($width, $height);

    $bg       = imagecolorallocate($img, 245, 247, 252);
    $axis     = imagecolorallocate($img, 30, 41, 59);
    $line     = imagecolorallocate($img, 239, 68, 68);
    $point    = imagecolorallocate($img, 30, 64, 175);
    $text     = imagecolorallocate($img, 15, 23, 42);
    $water    = imagecolorallocatealpha($img, 15, 23, 42, 80);

    imagefilledrectangle($img, 0, 0, $width, $height, $bg);

    $marginLeft   = 60;
    $marginBottom = 60;
    $marginTop    = 40;
    $marginRight  = 30;

    imageline($img, $marginLeft, $marginTop,
              $marginLeft, $height - $marginBottom, $axis);
    imageline($img, $marginLeft, $height - $marginBottom,
              $width - $marginRight, $height - $marginBottom, $axis);

    $max = max($data);
    if ($max <= 0) {
        $max = 1;
    }

    $plotWidth  = $width - $marginLeft - $marginRight;
    $plotHeight = $height - $marginTop - $marginBottom;

    $points = [];
    $days   = range(1, 7);
    $stepX  = (int)($plotWidth / (count($days) - 1));

    foreach ($days as $i => $day) {
        $val = $data[$day] ?? 0;
        $x = $marginLeft + $stepX * $i;
        $y = $height - $marginBottom - (int)($plotHeight * ($val / $max));
        $points[] = [$x, $y];

        // подпись по оси X
        imagestring($img, 2, $x - 5, $height - $marginBottom + 5, (string)$day, $text);
    }

    // Линии
    for ($i = 0; $i < count($points) - 1; $i++) {
        imageline(
            $img,
            $points[$i][0], $points[$i][1],
            $points[$i+1][0], $points[$i+1][1],
            $line
        );
    }

    // Точки
    foreach ($points as [$x, $y]) {
        imagefilledellipse($img, $x, $y, 6, 6, $point);
    }

    imagestring($img, 5, 10, 10, $title, $text);

    $watermark = 'Platonov TA, IKBO-22-23';
    $wmX = $width - 10 - strlen($watermark) * 6;
    $wmY = $height - 20;
    imagestring($img, 3, $wmX, $wmY, $watermark, $water);

    imagepng($img, $path);
    imagedestroy($img);
}

function buildPieChart(array $data, string $path, string $title): void
{
    $width  = 800;
    $height = 400;

    $img = imagecreatetruecolor($width, $height);

    $bg    = imagecolorallocate($img, 245, 247, 252);
    $text  = imagecolorallocate($img, 15, 23, 42);
    $water = imagecolorallocatealpha($img, 15, 23, 42, 80);

    imagefilledrectangle($img, 0, 0, $width, $height, $bg);

    $cx = 260;
    $cy = 200;
    $radius = 150;

    $colors = [
        imagecolorallocate($img, 96, 165, 250),   // синий
        imagecolorallocate($img, 52, 211, 153),   // зелёный
        imagecolorallocate($img, 251, 191, 36),   // жёлтый
        imagecolorallocate($img, 248, 113, 113),  // красный
        imagecolorallocate($img, 129, 140, 248),  // фиолетовый
    ];

    $total = array_sum($data);
    if ($total <= 0) {
        $total = 1;
    }

    $startAngle = 0;
    $legendX = 520;
    $legendY = 120;
    $i = 0;

    foreach ($data as $label => $value) {
        $angle = 360 * ($value / $total);
        $color = $colors[$i % count($colors)];

        imagefilledarc(
            $img, $cx, $cy, $radius * 2, $radius * 2,
            (int)$startAngle, (int)($startAngle + $angle),
            $color, IMG_ARC_PIE
        );

        // Легенда
        imagefilledrectangle($img, $legendX, $legendY + $i * 24,
                             $legendX + 16, $legendY + 16 + $i * 24, $color);
        $textLabel = sprintf('%s (%d)', $label, $value);
        imagestring($img, 3, $legendX + 24, $legendY + 4 + $i * 24, $textLabel, $text);

        $startAngle += $angle;
        $i++;
    }

    imagestring($img, 5, 10, 10, $title, $text);

    $watermark = 'Platonov TA, IKBO-22-23';
    $wmX = $width - 10 - strlen($watermark) * 6;
    $wmY = $height - 20;
    imagestring($img, 3, $wmX, $wmY, $watermark, $water);

    imagepng($img, $path);
    imagedestroy($img);
}

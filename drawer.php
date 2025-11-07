<?php

$num_str = filter_input(INPUT_GET, 'num', FILTER_UNSAFE_RAW);

if ($num_str === null) {
    echo "<svg xmlns='http://www.w3.org/2000/svg' width='600' height='120'>
            <text x='10' y='60'>Параметр: ?num=SCCWWWHHH (9 цифр)</text>
          </svg>";
    exit;
}

$num_str = trim($num_str);

if (!preg_match('/^\d{9}$/', $num_str)) {
    echo "<svg xmlns='http://www.w3.org/2000/svg' width='600' height='120'>
            <text x='10' y='60'>num должен быть 9-значным числом: SCCWWWHHH</text>
          </svg>";
    exit;
}

$S   = (int) substr($num_str, 0, 1);
$CC  = (int) substr($num_str, 1, 2);
$WWW = (int) substr($num_str, 3, 3);
$HHH = (int) substr($num_str, 6, 3);

$S   = max(1, min(4, $S));
$CC  = max(1, min(16, $CC));
$WWW = max(20, min(800, $WWW));
$HHH = max(20, min(800, $HHH));

$shapePacked  = $S - 1;            // 0..3
$colorPacked  = $CC - 1;           // 0..15
$widthPacked  = min(1023, $WWW);   // 10 бит
$heightPacked = min(1023, $HHH);   // 10 бит

// [height:10][width:10][color:4][shape:2]
$packed = ($heightPacked << 16)
        | ($widthPacked  <<  6)
        | ($colorPacked  <<  2)
        |  $shapePacked;

$shapeIndex =  ($packed      ) & 0b11;          // 0..3
$colorIndex =  ($packed >>  2) & 0b1111;        // 0..15
$width      =  ($packed >>  6) & 0b1111111111;  // 0..1023
$height     =  ($packed >> 16) & 0b1111111111;  // 0..1023

$width  = max(20, min(800, $width));
$height = max(20, min(800, $height));

$palette = [
    '#000000','#FF0000','#00FF00','#0000FF','#800080','#FFA500',
    '#00FFFF','#FFC0CB','#FFFF00','#008000','#808000','#008080',
    '#800000','#A52A2A','#808080','#FFFFFF',
];
$fill = $palette[$colorIndex];


$svgW = $width + 20;
$svgH = $height + 20;
$ox = 10;
$oy = 10;

$shapeNames = ['rect','circle','ellipse','triangle'];
$shapeName  = $shapeNames[$shapeIndex];


echo "<svg xmlns='http://www.w3.org/2000/svg' width='{$svgW}' height='{$svgH}' viewBox='0 0 {$svgW} {$svgH}'>";
echo "<rect x='0' y='0' width='{$svgW}' height='{$svgH}' fill='white'/>";

switch ($shapeName) {
    case 'rect':
        echo "<rect x='{$ox}' y='{$oy}' width='{$width}' height='{$height}' fill='{$fill}' stroke='black'/>";
        break;

    case 'circle':
        $r  = (int) floor(min($width, $height) / 2);
        $cx = $ox + $width  / 2;
        $cy = $oy + $height / 2;
        echo "<circle cx='{$cx}' cy='{$cy}' r='{$r}' fill='{$fill}' stroke='black'/>";
        break;

    case 'ellipse':
        $rx = (int) floor($width  / 2);
        $ry = (int) floor($height / 2);
        $cx = $ox + $rx;
        $cy = $oy + $ry;
        echo "<ellipse cx='{$cx}' cy='{$cy}' rx='{$rx}' ry='{$ry}' fill='{$fill}' stroke='black'/>";
        break;

    case 'triangle':
        $x1 = $ox + $width / 2;  $y1 = $oy;
        $x2 = $ox;               $y2 = $oy + $height;
        $x3 = $ox + $width;      $y3 = $oy + $height;
        $points = "{$x1},{$y1} {$x2},{$y2} {$x3},{$y3}";
        echo "<polygon points='{$points}' fill='{$fill}' stroke='black'/>";
        break;
}


$label = htmlspecialchars(
    "num={$num_str} | S={$S}, CC={$CC}, W={$WWW}, H={$HHH} | packed(dec)={$packed}, packed(bin)=".decbin($packed),
    ENT_QUOTES,
    'UTF-8'
);
echo "<text x='10' y='".($svgH)."' font-size='10' fill='#333'>{$label}</text>";
echo "</svg>";

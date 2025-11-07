<?php

if (isset($_GET["numbers"])){
    $numbers = array_map('intval', explode(',', $_GET["numbers"]));
}

function merge(array $left, array $right): array{
    $result = [];
    $leftIndex = $rightIndex = 0;

    while ($leftIndex < count($left) && $rightIndex < count($right)) {
        if ($left[$leftIndex] < $right[$rightIndex]) {
            $result[] = $left[$leftIndex++];
        } else {
            $result[] = $right[$rightIndex++];
        }
    }
    return array_merge($result, array_slice($left, $leftIndex), array_slice($right, $rightIndex));
}
function merge_sort(array $a): array{
    
    if (count($a) <= 1){
        return $a;
    }
    $mid = floor(count($a) / 2);
    $left = array_slice($a, $mid);
    $right = array_slice($a, 0, $mid);

    return merge(merge_sort($left), merge_sort($right));


}
echo "<div>Неотсортированный массив: " . implode(', ', $numbers) . "</div>";
echo "<div>Отсортированный массив: " . implode(', ', merge_sort($numbers)) . "</div>";
?>
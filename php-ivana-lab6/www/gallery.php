<?php
$thumbnailDir = "thumbnails/";

// Получение списка всех файлов в каталоге
$files = glob($thumbnailDir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);

// Вывод уменьшенных фотографий
foreach ($files as $file) {
    echo '<a href="view.php?image=' . urlencode(basename($file)) . '">';
    echo '<img src="' . $file . '" alt="Thumbnail">';
    echo '</a>';
}
?>

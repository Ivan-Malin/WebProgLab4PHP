<?php
if (isset($_POST["submit"])) {
    extension_loaded('gd') or die('GD extension not available');

    $targetDir = "uploads/";
    $thumbnailDir = "thumbnails/";

    $targetFile = $targetDir . basename($_FILES["file"]["name"]);
    $thumbnailFile = $thumbnailDir . basename($_FILES["file"]["name"]);

    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = array("jpg", "jpeg", "png", "gif");

    if (in_array($imageFileType, $allowedTypes)) {
        move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile);

        // Создание уменьшенной копии
        $originalImage = imagecreatefromstring(file_get_contents($targetFile));
        $width = imagesx($originalImage);
        $height = imagesy($originalImage);
        $newWidth = 200;
        $newHeight = floor($height * ($newWidth / $width));

        $thumbnailImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresized($thumbnailImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagejpeg($thumbnailImage, $thumbnailFile);

        imagedestroy($originalImage);
        imagedestroy($thumbnailImage);

        echo "The file " . htmlspecialchars(basename($_FILES["file"]["name"])) . " has been uploaded.";
    } else {
        echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
    }
}
?>

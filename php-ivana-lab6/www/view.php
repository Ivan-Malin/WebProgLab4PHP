<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo View</title>
</head>
<body>

<?php
if (isset($_GET['image'])) {
    $image = "uploads/" . urldecode($_GET['image']);

    echo '<img src="' . $image . '" alt="Full Size">';
} else {
    echo 'Image not found.';
}
?>

</body>
</html>

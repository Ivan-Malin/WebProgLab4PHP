<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            margin: 20px 0;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
        }

        input[type="file"] {
            display: none;
        }

        .custom-file-upload {
            display: inline-block;
            padding: 10px 20px;
            cursor: pointer;
            background-color: #4caf50; /* Default color */
            color: #fff;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .custom-file-upload:hover {
            background-color: #45a049;
        }

        .uploading {
            background-color: #3498db; /* Different color during uploading */
        }

        .uploading:hover {
            background-color: #2980b9;
        }

        button {
            padding: 8px 16px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        hr {
            border: 1px solid #ddd;
            margin: 20px 0;
        }

        .gallery-container {
            overflow-y: auto;
        }

        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .thumbnail {
            margin: 10px;
            overflow: hidden;
            cursor: pointer;
        }

        .thumbnail img {
            max-width: 100%;
            height: auto;
            transition: transform 0.2s;
        }

        .thumbnail img:hover {
            transform: scale(1.1);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            max-width: 80%;
            max-height: 80%;
        }

        .modal img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Photo Gallery</h2>

    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="file" class="custom-file-upload">Choose a file</label>
        <input type="file" name="file" id="file" accept="image/*">
        <button type="submit" name="submit" class="custom-file-upload">Upload</button>
    </form>

    <hr>

    <div class="gallery-container">
        <div class="gallery">
            <?php include 'gallery.php'; ?>
        </div>
    </div>

    <!-- Modal for expanding images -->
    <div class="modal" id="myModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="expandedImg">
        </div>
    </div>

    <script>
        function openModal(imgSrc) {
            document.getElementById('expandedImg').src = imgSrc;
            document.getElementById('myModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('myModal').style.display = 'none';
        }
    </script>
</body>
</html>

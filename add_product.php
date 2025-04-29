<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';
if (!isset($conn) || $conn->connect_error) {
    $error = "Database connection failed: " . $conn->connect_error;
}

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $title = trim($_POST['title']);
    $artist = trim($_POST['artist']);
    $genre_id = (int)$_POST['genre_id'];
    $price = floatval($_POST['price']);
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir);
    
        $filename = basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $filename;
    
        $fileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $image = 'uploads/' . $filename;
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid image type. Only JPG, PNG, and GIF allowed.";
        }
    } else {
        $error = "Please upload a valid image.";
    }
    
    if (empty($title) || empty($artist) || $genre_id <= 0 || $price <= 0 || empty($image)) {
        $error = "All fields are required and must be valid.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (title, artist, genre_id, price, image) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssids", $title, $artist, $genre_id, $price, $image);
            if ($stmt->execute()) {
                $message = "Product added successfully.";
            } else {
                $error = "Failed to add product: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background-color: #f5f7fa;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }
        main.box {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.06);
        }
        h4 {
            font-size: 26px;
            color: #37418d;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            background-color: #2e86de;
            color: #fff;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #1b4f72;
        }
        form label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
        }
        form input[type="text"],
        form input[type="number"],
        form select {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9fb;
            font-size: 14px;
        }
        form input:focus,
        form select:focus {
            border-color: #2e86de;
            outline: none;
        }
        form button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background-color: #10b981;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #0f766e;
        }
        .message {
            color: #10b981;
            font-weight: 500;
        }
        .error {
            color: #e74c3c;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <main class="box">
        <h4>Add New Product</h4>
        <a href="<?php echo BASE_URL; ?>admin-home" class="btn">← Back to Dashboard</a>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>add_product" enctype="multipart/form-data">
    <label>Title:</label>
    <input type="text" name="title" required>

    <label>Artist:</label>
    <input type="text" name="artist" required>

    <label>Genre:</label>
    <select name="genre_id" required>
        <?php
        $genres = $conn->query("SELECT id, name FROM genres");
        if ($genres && $genres->num_rows > 0) {
            while ($genre = $genres->fetch_assoc()) {
                echo "<option value='" . (int)$genre['id'] . "'>" . htmlspecialchars($genre['name']) . "</option>";
            }
        } else {
            echo "<option disabled>No genres available</option>";
        }
        ?>
    </select>

    <label>Price:</label>
    <input type="number" step="0.01" name="price" min="0" required>

    <label>Image:</label>
    <input type="file" name="image" accept="image/*" required style="margin-top: 8px;">

    <button type="submit" name="add_product">Add Product</button>
</form>

    </main>
</body>
</html>

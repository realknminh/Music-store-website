<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login');
    exit();
}

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure the id is an integer
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Product deleted successfully.";
    } else {
        $error = "Failed to delete product.";
    }
    
}

// Handle Edit Action
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure the id is an integer

    // Fetch product details for editing
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    } else {
        $error = "Product not found.";
    }
}

// Handle Update Action (when the edit form is submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $artist = trim($_POST['artist']);
    $genre_id = intval($_POST['genre_id']);
    $price = floatval($_POST['price']);
    $image = trim($_POST['image']);

    $stmt = $conn->prepare("UPDATE products SET title = ?, artist = ?, genre_id = ?, price = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssidsi", $title, $artist, $genre_id, $price, $image, $id);

    if ($stmt->execute()) {
        $message = "Product updated successfully.";
    } else {
        $error = "Failed to update product.";
    }
}

// Fetch products for display
$sql = "SELECT p.id, p.title, p.artist, p.price, p.image, g.name AS genre 
        FROM products p
        JOIN genres g ON p.genre_id = g.id
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<link rel="stylesheet" href="css/admin_style.css">
<main class="box">
    <div class="admin-actions">
    <a href="<?php echo BASE_URL; ?>add_product" class="them-btn">Add New Product</a>
    </div>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($product)): ?>
        <!-- Edit Product Form -->
        <h2>Edit Product</h2>
        <form method="POST" action="?page=admin-home">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <label>Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
            <label>Artist:</label>
            <input type="text" name="artist" value="<?php echo htmlspecialchars($product['artist']); ?>" required>
            <label>Genre:</label>
            <select name="genre_id" required>
                <?php
                $genres = $conn->query("SELECT * FROM genres");
                while ($genre = $genres->fetch_assoc()) {
                    $selected = $genre['id'] === $product['genre_id'] ? 'selected' : '';
                    echo "<option value='{$genre['id']}' $selected>{$genre['name']}</option>";
                }
                ?>
            </select>
            <label>Price:</label>
            <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
            <label>Image:</label>
            <input type="text" name="image" value="<?php echo htmlspecialchars($product['image']); ?>" required>
            <button type="submit" name="update_product" class="btn">Save Changes</button>
        </form>
    <?php else: ?>
        <!-- Product List -->
        <h2>Product List</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Artist</th>
                    <th>Genre</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['artist']; ?></td>
                            <td><?php echo $row['genre']; ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td><img src="<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>" width="50"></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>admin-home?action=edit&id=<?php echo (int)$row['id']; ?>" class="btn">Edit</a>
                                <a href="<?php echo BASE_URL; ?>admin-home?action=delete&id=<?php echo (int)$row['id']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<style>
    .them-btn {
        background-color:rgb(166, 88, 88);
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 5px;
        margin-left: 0.8vw;
    }
    .them-btn:hover {
        background-color: #0056b3;
    }
</style>
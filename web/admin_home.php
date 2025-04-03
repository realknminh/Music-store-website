<?php
// filepath: c:\xampp\htdocs\lab1\admin_home.php
session_start();
@include 'db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not an admin
    exit();
}

// Fetch products for display
$sql = "SELECT p.id, p.title, p.artist, p.price, p.image, g.name AS genre 
        FROM products p
        JOIN genres g ON p.genre_id = g.id
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
    <header class="header">
        <div class="flex">
            <a href="index.php" class="logo">🎵 Music Store Admin 🎶</a>
            <nav class="navbar">
                <ul>
                    <li><a href="admin_home.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="box">
        <h1>Admin Dashboard</h1>
        <div class="admin-actions">
            <a href="add_product.php" class="btn">Add New Product</a>
            <a href="add_category.php" class="btn">Add New Category</a>
        </div>
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
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
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
    </main>
</body>
</html>
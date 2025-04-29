<?php
include 'db.php';

// Fetch genres for the dropdown menu
$genreQuery = "SELECT name FROM genres ORDER BY name ASC";
$genreResult = $conn->query($genreQuery);
if (!$genreResult) {
    die("Error fetching genres: " . $conn->error);
}
$genres = [];
while ($row = $genreResult->fetch_assoc()) {
    $genres[] = $row['name'];
}

$genre = isset($_GET['genre']) ? $_GET['genre'] : '';

// Ensure genre is valid before querying
if (!empty($genre)) {
    $sql = "SELECT p.id, p.title, p.artist, p.price, p.image FROM products p 
            JOIN genres g ON p.genre_id = g.id 
            WHERE g.name = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $genre);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo !empty($genre) ? ucfirst($genre) . ' Music' : 'Browse Categories'; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="box">
        <h1><?php echo !empty($genre) ? ucfirst($genre) . ' Music' : 'Select a Category'; ?></h1>
        <div class="music-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="music-card">
                        <a href="<?php echo BASE_URL; ?>previewproduct?id=<?php echo $row['id']; ?>">
                            <img style="margin: 0.8vw auto;" src="<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>">
                        </a>
                        <h3><?php echo $row['title']; ?></h3>
                        <p>Artist: <?php echo $row['artist']; ?></p>
                        <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
                        <form method="post" action="<?php echo BASE_URL; ?>cart">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="add_to_cart" class="buy-btn">Add to Cart</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>

                <?php
                $randomQuery = "SELECT id, title, artist, price, image FROM products ORDER BY RAND() LIMIT 6";
                $randomResult = $conn->query($randomQuery);
                if ($randomResult && $randomResult->num_rows > 0):
                    while ($row = $randomResult->fetch_assoc()):
                ?>
                        <div class="music-card">
                            <img style="margin: 0.8vw auto;" src="<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>">
                            <h3><?php echo $row['title']; ?></h3>
                            <p>Artist: <?php echo $row['artist']; ?></p>
                            <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
                            <form method="post" action="<?php echo BASE_URL; ?>cart">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_to_cart" class="buy-btn">Add to Cart</button>
                            </form>
                        </div>
                <?php
                    endwhile;
                endif;
                ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
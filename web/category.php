<?php
@include 'db.php';
@include 'header.php';
$genreQuery = "SELECT name FROM genres ORDER BY name ASC";
$genreResult = $conn->query($genreQuery);
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
    <title><?php echo ucfirst($genre); ?> Music</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="box">
        <h1><?php echo !empty($genre) ? ucfirst($genre) . ' Music' : 'Select a Category'; ?></h1>
        <div class="music-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="music-card">
                        <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>">
                        <h3><?php echo $row['title']; ?></h3>
                        <p>Artist: <?php echo $row['artist']; ?></p>
                        <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
                        <a href="cart.php?add=<?php echo $row['id']; ?>" class="buy-btn">Add to Cart</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <?php
                $randomQuery = "SELECT id, title, artist, price, image FROM products ORDER BY RAND() LIMIT 5";
                $randomResult = $conn->query($randomQuery);
                if ($randomResult && $randomResult->num_rows > 0):
                    while ($row = $randomResult->fetch_assoc()):
                ?>
                        <div class="music-card">
                            <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>">
                            <h3><?php echo $row['title']; ?></h3>
                            <p>Artist: <?php echo $row['artist']; ?></p>
                            <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
                            <a href="cart.php?add=<?php echo $row['id']; ?>" class="buy-btn">Add to Cart</a>
                        </div>
                <?php
                    endwhile;
                endif;
                ?></div>
            <?php endif; ?>
        </div>
    </main>
<?php
@include 'footer.php';
?>
</body>
</html>
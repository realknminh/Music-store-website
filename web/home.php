<?php
@include 'db.php';
@include 'header.php';

// Pagination
$currentPage = isset($_GET['currentPage']) ? (int)$_GET['currentPage'] : 1;
$limit = 12; //per page
$start = ($currentPage - 1) * $limit;

// Sort setup
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$validSortColumns = ['title', 'price'];
$sort = in_array($sort, $validSortColumns) ? $sort : 'title';
$order = ($order === 'DESC') ? 'DESC' : 'ASC';

// total records
$totalQuery = "SELECT COUNT(*) as total FROM products";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);

$sql = "SELECT p.id, p.title, p.artist, p.price, p.image, p.track, g.name AS genre 
        FROM products p
        JOIN genres g ON p.genre_id = g.id
        ORDER BY $sort $order LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Store - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
        <div class="welcome-image">
            <img src="./images/backgr.jpg" alt="Welcome to Music Store" style="width: 100%; height: auto;">
        </div>

    <div class="search-bar">
    <h2>Search for Music</h2>
    <div class="search-input-container">
        <input type="text" id="search-input" placeholder="Search for music by name or category..." onkeyup="searchProducts()">
        <button type="submit" class="search-btn">🔍</button>
    </div>
</div>
<div id="search-results" class="music-container"></div>

    <main class="box">
        <h1>Music Collection</h1>
        <div class="sort-buttons">
            <a href="?page=home&sort=title&order=<?php echo ($sort == 'title' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">Sort by Name</a>
            <a href="?page=home&sort=price&order=<?php echo ($sort == 'price' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">Sort by Price</a>
        </div>
        <div class="music-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="music-card">
                        <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>">
                        <h3><?php echo $row['title']; ?></h3>
                        <p>Artist: <?php echo $row['artist']; ?></p>
                        <p>Genre: <?php echo ucfirst($row['genre']); ?></p>
                        <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
                        <audio controls>
                            <source src="<?php echo $row['track']; ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                        <a href="cart.php?add=<?php echo $row['id']; ?>" class="buy-btn">Add to Cart</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-results">No music available.</p>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=home&currentPage=<?php echo $currentPage - 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>" class="prev">&laquo; Prev</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=home&currentPage=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>" class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>"> <?php echo $i; ?> </a>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=home&currentPage=<?php echo $currentPage + 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>" class="next">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </main>
<?php
@include 'footer.php';
?>

<script>
    function searchProducts() {
        const query = document.getElementById('search-input').value;

        // If the input is empty, clear the search results
        if (query.trim() === '') {
            document.getElementById('search-results').innerHTML = '';
            return;
        }

        // Create an AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `search.php?query=${encodeURIComponent(query)}`, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Update the search results container
                document.getElementById('search-results').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }
</script>
</body>
</html>
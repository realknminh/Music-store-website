<?php
include 'db.php';
// Pagination
$currentPage = isset($_GET['currentPage']) ? (int)$_GET['currentPage'] : 1;
$limit = 9;
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

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Music Store - Home</title>
<link rel="stylesheet" href="css/style.css">
<div class="search-wrapper">
    <div class="search-bar">
        <div class="search-input-container">
            <input type="text" id="search-input" placeholder="Search for music by name..." onkeyup="searchProducts()">
            <!-- <button type="button" class="search-btn" onclick="goToFirstMatch()">🔍</button> -->
        </div>
    </div>
    <div id="search-results" class="search-results-container"></div>
</div>

<header class="hero-banner">
    <h2>Welcome to the Midnight Audio <span class="spinning-icon">💿</span></h2>
    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        .spinning-icon {
            display: inline-block;
            animation: spin 2s linear infinite;
            width: 1.5em;
        }
    </style>
    <p>Discover music that moves you. From classics to fresh beats.</p>
</header>
<div style="background: #e4e4e4; text-align: center; padding: 0;">
    <img class="theme" style="width:60%" src="<?php echo BASE_URL; ?>images/theme.png" alt="Theme Image">
</div>

<main class="box">
    <h1>Music Collection</h1>
    <div class="sort-buttons">
        <a href="<?php echo BASE_URL; ?>home?sort=title&order=<?php echo ($sort == 'title' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">Sort by Name</a>
        <a href="<?php echo BASE_URL; ?>home?sort=price&order=<?php echo ($sort == 'price' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">Sort by Price</a>
    </div>

    <div class="music-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="music-card">
                    <a href="<?php echo BASE_URL; ?>previewproduct?id=<?php echo $row['id']; ?>">
                        <img src="<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>">
                    </a>
                    <div class="card-info">
                        <h3><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p>Artist: <?php echo htmlspecialchars($row['artist'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p>Genre: <?php echo ucfirst(htmlspecialchars($row['genre'], ENT_QUOTES, 'UTF-8')); ?></p>
                        <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
                    </div>
                    <form method="post" action="<?php echo BASE_URL; ?>cart">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <input type="submit" name="add_to_cart" class="buy-btn" value="Add to Cart">
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-results">No music available.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="<?php echo BASE_URL; ?>home?currentPage=<?php echo $currentPage - 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>" class="prev">« Prev</a>
        <?php endif; ?>

        <?php
        $range = 2;
        for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if (
                $i == 1 ||
                $i == $totalPages ||
                ($i >= $currentPage - $range && $i <= $currentPage + $range)
            ): ?>
                <?php if ($i != 1 && $i == $currentPage - $range): ?>
                    <span class="dots">...</span>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>home?currentPage=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>"
                   class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php if ($i != $totalPages && $i == $currentPage + $range): ?>
                    <span class="dots">...</span>
                <?php endif; ?>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="<?php echo BASE_URL; ?>home?currentPage=<?php echo $currentPage + 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>" class="next">Next »</a>
        <?php endif; ?>
    </div>
</main>

<script>
function searchProducts() {
    const query = document.getElementById('search-input').value;
    if (query.trim() === '') {
        document.getElementById('search-results').innerHTML = '';
        return;
    }
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '<?php echo BASE_URL; ?>search.php?query=' + encodeURIComponent(query), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('search-results').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

function goToFirstMatch() {
    const query = document.getElementById('search-input').value;
    if (query.trim() === '') return;
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '<?php echo BASE_URL; ?>matchprod.php?query=' + encodeURIComponent(query), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response && response.id) {
                    window.location.href = '<?php echo BASE_URL; ?>previewproduct?id=' + response.id;
                } else {
                    alert('No matching song found.');
                }
            } catch (e) {
                alert('Error processing search.');
            }
        }
    };
    xhr.send();
}
</script>

<style>
.hero-banner {
    background: url('<?php echo BASE_URL; ?>banner.jpg') no-repeat center center/cover;
    text-align: center;
    padding: 80px 20px;
    color: white;
    background-color: #262626;
}
.hero-banner h1 {
    font-size: 3rem;
    margin-bottom: 10px;
}
.hero-banner p {
    font-size: 1.2rem;
    margin-bottom: 20px;
}
.genre-filter {
    text-align: center;
    margin: 20px auto;
}
.genre-filter select {
    padding: 8px 10px;
    border-radius: 6px;
}
.toggle-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 999;
}
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    border-radius: 24px;
    transition: 0.4s;
}
.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}
input:checked + .slider {
    background-color: #4b0082;
}
input:checked + .slider:before {
    transform: translateX(26px);
}
.toggle-label {
    margin-left: 10px;
    color: #333;
}
body.dark-mode {
    background: #121212;
    color: #ddd;
}
body.dark-mode .box {
    background: #1e1e1e;
}
body.dark-mode .music-card {
    background: #2a2a2a;
    color: #f0f0f0;
}
body.dark-mode .buy-btn {
    background: #6a1b9a;
}
body.dark-mode .sort-buttons a,
body.dark-mode .pagination a {
    background: #333;
    color: #fff;
}
body.dark-mode .hero-banner {
    background-color: #000000;
}
</style>
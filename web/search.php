<?php
@include 'db.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (!empty($query)) {
    // Match titles that start with the search term
    $sql = "SELECT p.id, p.title, p.artist, p.price, p.image 
            FROM products p
            WHERE p.title LIKE ?
            LIMIT 10";
    $stmt = $conn->prepare($sql);
    $searchTerm = $query . '%'; // Add wildcard only at the end
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="music-card">';
            echo '<img src="' . $row['image'] . '" alt="' . $row['title'] . '">';
            echo '<h3>' . $row['title'] . '</h3>';
            echo '<p>Artist: ' . $row['artist'] . '</p>';
            echo '<p>Price: $' . number_format($row['price'], 2) . '</p>';
            echo '<a href="cart.php?add=' . $row['id'] . '" class="buy-btn">Add to Cart</a>';
            echo '</div>';
        }
    } else {
        echo '<p class="no-results">No songs found.</p>';
    }
}
?>
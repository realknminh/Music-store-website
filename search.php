<?php
include 'db.php';
include 'config.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (!empty($query)) {
    $sql = "SELECT p.id, p.title, p.artist, p.price, p.image 
            FROM products p
            WHERE p.title LIKE ?
            LIMIT 10";
    $stmt = $conn->prepare($sql);
    $searchTerm = $query . '%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a class="search-result-item" href="' . BASE_URL . 'previewproduct?id=' . (int)$row['id'] . '">';
            echo '<img src="' . $row['image'] . '" alt="' . $row['title'] . '">';
            echo '<div class="song-details">';
            echo '<h4>' . $row['title'] . '</h4>';
            echo '<p>' . $row['artist'] . '</p>';
            echo '<span>$' . number_format($row['price'], 2) . '</span>';
            echo '</div>';
            echo '</a>';
        }
    } else {
        echo '<p class="no-results">No songs found.</p>';
    }
}
?>

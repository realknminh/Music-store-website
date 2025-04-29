<?php
@include 'db.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (!empty($query)) {
    $sql = "SELECT id FROM products WHERE title LIKE ? ORDER BY title ASC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $searchTerm = $query . '%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode(['id' => $row['id']]);
    } else {
        echo json_encode(['id' => null]);
    }
} else {
    echo json_encode(['id' => null]);
}
?>

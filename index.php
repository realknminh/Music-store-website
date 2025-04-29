<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering to prevent premature output
ob_start();

session_start();
include 'config.php'; 
include 'db.php';

$page = isset($_GET['url']) ? trim($_GET['url'], '/') : 'home';
$page = $page === '' ? 'home' : $page;

$allowed_pages = [
    'home', 'login', 'signup', 'contact', 'category',
    'cart', 'admin-home', 'logout', 'previewproduct', 'add_product'
];
$title = 'Music Store';
if ($page === 'previewproduct' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT title FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($product = $result->fetch_assoc()) {
        $title = htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') . ' - Preview';
    }
    $stmt->close();
} elseif ($page === 'admin-home') {
    $title = 'Admin Dashboard';
} elseif ($page === 'add_product') {
    $title = 'Add New Product';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <?php
    if (in_array($page, $allowed_pages)) {
        if ($page === 'logout') {
            session_destroy();
            unset($_SESSION['username']);
            header("Location: " . BASE_URL . "home");
            exit;
        } elseif (file_exists("$page.php")) {
            include("$page.php");
        } else {
            error_log("Page not found: $page.php");
            include("404.html");
        }
    } else {
        include("404.html");
    }
    ?>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
<?php
// End output buffering and flush the output
ob_end_flush();
?>
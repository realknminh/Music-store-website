<?php
@include 'db.php';
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit();
}
$user_id = $_SESSION['user_id'];

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    $checkStmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $checkStmt->bind_param("ii", $user_id, $product_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $updateStmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $updateStmt->bind_param("iii", $quantity, $user_id, $product_id);
        $updateStmt->execute();
        $message = "🛒 Updated quantity in cart!";
    } else {
        $insertStmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insertStmt->execute();
        $message = "✔ Product added to cart!";
    }
}

// Remove item
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    header("Location: " . BASE_URL . "cart");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $cart_id = (int)$_POST['cart_id'];
    $action = $_POST['action'];

    if ($action === 'increase') {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
    } elseif ($action === 'decrease') {
        $stmt = $conn->prepare("UPDATE cart SET quantity = GREATEST(quantity - 1, 1) WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
    }

    header("Location: " . BASE_URL . "cart");
    exit;
}

// Handle checkout confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_checkout'])) {
    $clearCartQuery = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clearCartQuery->bind_param("i", $user_id);
    $clearCartQuery->execute();
    $message = "🎉 Purchase successful! Thank you for your order.";
}

// Fetch cart items
$cartItems = $conn->query("
    SELECT c.id AS cart_id, p.title, p.price, c.quantity 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");
if (!$cartItems) {
    $message = "Error fetching cart items: " . $conn->error;
}

// Fetch product list for adding to cart
$productList = $conn->query("SELECT id, title FROM products");
if (!$productList) {
    $message = "Error fetching products: " . $conn->error;
}

$total = 0;
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Music Store - Cart</title>
<link rel="stylesheet" href="css/style.css">

<body>
<div class="container" style="background-color: #fff;">
    <h2>🛍 Your Shopping Cart</h2>

    <?php if (!empty($message)): ?>
        <div class="message" style="color: green; font-size: 18px; margin: 20px 0;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($cartItems && $cartItems->num_rows > 0): ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
            <?php while ($item = $cartItems->fetch_assoc()):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['title']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td>
    <form method="POST" style="display:inline;">
        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
        <input type="hidden" name="action" value="decrease">
        <input type="hidden" name="update_qty" value="1">
        <button type="submit" style="padding: 8px; font-size: 14px; background-color:rgb(169, 134, 161); color: white; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s ease;">-</button>
    </form>
    <?= $item['quantity'] ?>
    <form method="POST" style="display:inline;">
        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
        <input type="hidden" name="action" value="increase">
        <input type="hidden" name="update_qty" value="1">
        <button type="submit" style="padding: 8px; font-size: 10px; background-color:rgb(169, 134, 161); color: white; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s ease;">＋</button>
    </form>
</td>
                <td>$<?= number_format($subtotal, 2) ?></td>
                <td><a class="remove-link" href="<?php echo BASE_URL; ?>cart?remove=<?= $item['cart_id'] ?>" onclick="return confirm('Remove this item?')">Remove</a></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <div class="total" style="margin-top: 20px; font-size: 18px;">
            <strong>Total:</strong> $<?= number_format($total, 2) ?>
        </div>

        <form method="POST" style="margin-top: 20px; text-align: center;">
            <button type="submit" name="confirm_checkout" style="display:inline-block; padding: 10px 20px; font-size: 1rem; border-radius: 6px; border: none; cursor: pointer;">Confirm Purchase</button>
        </form>
    <?php else: ?>
        <p class="empty-cart" style="color: red; font-size: 16px;">🛒 Your cart is empty! <a href="<?php echo BASE_URL; ?>home">Continue Shopping</a></p>
    <?php endif; ?>

    <div class="form-section">
        <h3>➕ Add Product to Cart</h3>
        <form method="POST" style="gap: 1vw; display: flex; text-align: center; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <select name="product_id" required style="padding: 10px; margin: 10px 0; font-size: 1rem; border-radius: 6px; border: 1px solid #ccc; width: 100%;">
                <option value="">Select Product</option>
                <?php while($product = $productList->fetch_assoc()): ?>
                    <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['title']) ?></option>
                <?php endwhile; ?>
            </select>
            <input type="number" name="quantity" placeholder="Quantity" min="1" value="1" required style="padding: 10px; margin: 10px 0; font-size: 1rem; border-radius: 6px; border: 1px solid #ccc; width: 100%;">
            <button type="submit" name="add_to_cart" style="display:inline-block; padding: 10px 20px; font-size: 1rem; border-radius: 6px; border: none; width: 90%;">Add to Cart</button>
        </form>
    </div>
</div>

<style>
table {
    width: 100%;
    border-collapse: collapse;
    background-color: #222;
    color: #fff;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 20px;
}
th, td {
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid #444;
}
th {
    background-color: #2c2c2c;
    color: #ff9de0;
    text-transform: uppercase;
}
tr:hover {
    background-color: #333;
}
.container {
    max-width: 1200px;
    margin: 60px auto;
    background-color: #111;
    padding: 30px;
    border-radius: 16px;
    border: 0.1vw solid #ff00aa;
    box-shadow: 0 0 15px rgba(255, 0, 170, 0.3);
    animation: fadeIn 0.6s ease;
}
.form-section button {
    background-color: #ff00aa;
    border: none;
    padding: 12px 25px;
    border-radius: 10px;
    color: white;
    font-size: 1rem;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s ease;
}
.form-section button:hover {
    background: #cc00ff;
    transform: scale(1.05);
    box-shadow: 0 0 15px #cc00ff;
}
.remove-link {
    color: #ff5555;
    text-decoration: none;
}
.remove-link:hover {
    text-decoration: underline;
}


</style>
</body>
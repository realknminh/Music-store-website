<?php
session_start();
@include 'db.php';
@include 'header.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$cartItems = $conn->query("
    SELECT c.id AS cart_id, p.title, p.price, c.quantity, (p.price * c.quantity) AS subtotal 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

$total = 0;

// Handle checkout confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_checkout'])) {
    // Clear the cart after checkout
    $clearCartQuery = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clearCartQuery->bind_param("i", $user_id);
    $clearCartQuery->execute();

    $message = "🎉 Purchase successful! Thank you for your order.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container" style="background-color: #fff;">
    <h2>🛒 Checkout</h2>

    <?php if (!empty($message)): ?>
        <div class="message" style="color: green; font-size: 18px; margin: 20px 0;"><?= $message ?></div>
    <?php else: ?>
        <?php if ($cartItems->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
                <?php while ($item = $cartItems->fetch_assoc()):
                    $total += $item['subtotal'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </table>

            <div class="total" style="margin-top: 20px; font-size: 18px;">
                <strong>Total:</strong> $<?= number_format($total, 2) ?>
            </div>

            <form method="POST" style="margin-top: 20px; text-align: center;">
                <button type="submit" name="confirm_checkout" style="display:inline-block; padding: 10px 20px; font-size: 1rem; border-radius: 6px; border: none; background-color: #4CAF50; color: white; cursor: pointer;">Confirm Purchase</button>
            </form>
        <?php else: ?>
            <p class="empty-cart" style="color: red; font-size: 16px;">🛒 Your cart is empty! <a href="home.php">Continue Shopping</a></p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
@include 'footer.php';
?>
</body>
</html>
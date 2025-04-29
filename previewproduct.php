<?php
include 'db.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT p.id, p.title, p.artist, p.price, p.image, p.track, g.name AS genre 
        FROM products p
        JOIN genres g ON p.genre_id = g.id
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
?>

<head>
    <meta charset="UTF-8">
    <title><?php echo $product['title']; ?> - Preview</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@600&family=Rock+Salt&display=swap');

    body {
        font-family: 'Orbitron', sans-serif;
        background: linear-gradient(135deg, #0f0f1a, #1a1a2e);
        color: #e5e5f1;
        margin: 0;
        padding: 0;
        background-image: url('https://www.transparenttextures.com/patterns/asfalt-dark.png');
    }

    .preview-container {
        max-width: 900px;
        margin: 9vw auto;
        background: #121826;
        padding: 40px;
        border: 3px solid #00aaff;
        border-radius: 20px;
        box-shadow: 0 0 25px rgba(0, 170, 255, 0.3);
        text-align: center;
        animation: glitchIn 0.7s ease;
        width: 100%;
    }

    .preview-container img {
        max-width: 300px;
        border-radius: 16px;
        border: 2px solid #00aaff;
        transition: transform 0.3s ease, filter 0.3s ease;
        filter: grayscale(20%) contrast(110%);
    }

    .preview-container h2 {
        font-family: 'Rock Salt', cursive;
        font-size: 36px;
        color: #00aaff;
        margin: 20px 0 10px;
        text-shadow: 1px 1px 4px #000;
    }

    .preview-container p {
        font-size: 16px;
        color: #cceeff;
        margin: 6px 0;
    }

    .preview-container p strong {
        color: #5adfff;
    }

    .preview-container audio {
        margin-top: 25px;
        width: 100%;
        filter: hue-rotate(180deg);
    }

    .add-btn, .back-btn {
        display: inline-block;
        margin: 25px 10px 0;
        padding: 12px 25px;
        font-size: 16px;
        border: 2px solid #00aaff;
        border-radius: 12px;
        font-weight: bold;
        cursor: pointer;
        background-color: transparent;
        color: #00aaff;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
    }

    .add-btn:hover {
        background-color: #00aaff;
        color: #000;
        transform: scale(1.05);
    }

    .back-btn {
        border-color: #666;
        color: #aaa;
    }

    .back-btn:hover {
        background-color: #666;
        color: #000;
        transform: scale(1.05);
    }

    @keyframes glitchIn {
        0% {
            opacity: 0;
            transform: translateY(40px) skewX(10deg);
        }
        100% {
            opacity: 1;
            transform: translateY(0) skewX(0deg);
        }
    }

    .preview-container::before {
        content: "";
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: url('https://www.transparenttextures.com/patterns/checkered-pattern.png');
        opacity: 0.05;
        z-index: 0;
        pointer-events: none;
    }

    .preview-container * {
        position: relative;
        z-index: 1;
    }
</style>

</head>
<body>

<?php if ($product): ?>
    <div class="preview-container">
        <img src="<?php echo $product['image']; ?>" alt="Cover" onclick="togglePreview()">
        <h2><?php echo $product['title']; ?></h2>
        <p><strong>Artist:</strong> <?php echo $product['artist']; ?></p>
        <p><strong>Genre:</strong> <?php echo ucfirst($product['genre']); ?></p>
        <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>

        <form method="post" action="<?php echo BASE_URL; ?>cart">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="quantity" value="1">
            <input type="submit" name="add_to_cart" class="add-btn" value="Add to Cart">
        </form>

        <a href="<?php echo BASE_URL; ?>home" class="back-btn">⬅ Back to Store</a>
    </div>
<?php else: ?>
    <p style="text-align:center;">Product not found.</p>
<?php endif; ?>

</body>

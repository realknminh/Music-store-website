<?php
@include 'db.php';
session_start(); // Start the session to check login status

// Fetch genres for the dropdown menu
$genreQuery = "SELECT name FROM genres ORDER BY name ASC";
$genreResult = $conn->query($genreQuery);
$genres = [];
while ($row = $genreResult->fetch_assoc()) {
    $genres[] = $row['name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>header</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="header">
    <div class="flex">
        <a href="index.php" class="logo" style="text-decoration: none;">🎵 Music Store 🎶</a>
        <nav class="navbar">
            <ul>
                <li><a href="index.php?page=home">Home</a></li>
                <li class="dropdown">
                    <a href="index.php?page=category" class="dropbtn">Categories <span style="font-size: 0.8em;">&#9662;</span></a>
                    <div class="dropdown-content">
                        <?php foreach ($genres as $g): ?>
                            <a href="index.php?page=category&genre=<?php echo urlencode($g); ?>"><?php echo ucfirst($g); ?></a>
                        <?php endforeach; ?>
                    </div>
                </li>
                <li><a href="index.php?page=contact">Contact</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <!-- Account Section for Logged-in Users -->
                    <li class="dropdown">
                        <a href="#" class="dropbtn">Account <span style="font-size: 0.8em;">&#9662;</span></a>
                        <div class="dropdown-content">
                            <a href="index.php?page=profile">Profile</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <!-- Login Section for Guests -->
                    <li><a href="index.php?page=login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
</body>
</html>
<?php
include 'db.php';
if (!$conn || $conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    $genres = [];
} else {
    $genreQuery = "SELECT name FROM genres ORDER BY name ASC";
    $genreResult = $conn->query($genreQuery);
    $genres = [];
    if ($genreResult) {
        while ($row = $genreResult->fetch_assoc()) {
            $genres[] = $row['name'];
        }
    }
}
?>

<header class="header">
    <div class="nav-container" style="display: flex;">
        <a href="<?php echo BASE_URL; ?>home" class="logo" style="text-decoration: none;">🎵 Midnight Audio 🎶</a>
        <button class="menu-toggle" aria-label="Toggle navigation">☰</button>

        <nav class="navbar" style="margin-right: 180px;">
            <ul>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="<?php echo BASE_URL; ?>logout">Logout</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin-home">Admin Home</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>home">Home</a></li>
                    <li class="dropdown">
                        <a href="<?php echo BASE_URL; ?>category" class="dropbtn">Categories <span style="font-size: 0.8em;">▾</span></a>
                        <div class="dropdown-content">
                            <?php foreach ($genres as $g): ?>
                                <a href="<?php echo BASE_URL; ?>category?genre=<?php echo urlencode($g); ?>"><?php echo ucfirst($g); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <li><a href="<?php echo BASE_URL; ?>contact">Contact</a></li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <li class="dropdown">
                            <a href="#" class="dropbtn">Account <span style="font-size: 0.8em;">▾</span></a>
                            <div class="dropdown-acc">
                                <p style="padding: 1vw 15px; color: aliceblue; font-size: 21px; text-align: center; letter-spacing: 2px;">Username: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                <form method="POST" action="<?php echo BASE_URL; ?>logout" style="margin: 0; padding: 0;">
                                    <button style="display: block; margin: 0 auto;" type="submit" class="logout-button">Logout</button>
                                </form>
                            </div>
                        </li>
                        <li><a href="<?php echo BASE_URL; ?>cart">🛒 Cart</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>login">Log in</a></li>
                        <li><a href="<?php echo BASE_URL; ?>signup">Sign up</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<style>
.logout-button {
    background-color: rgb(14, 4, 125);
    border: none;
    color: white;
    border-radius: 0.8vw;
    cursor: pointer;
    font-size: 18px;
    padding: 0.8vw 1vw;
    transition: background-color 0.3s;
    text-align: center;
}
.logout-button:hover {
    background-color: rgb(61, 58, 114);
}
.dropdown-acc {
    display: none;
    position: absolute;
    background-color: rgb(79, 98, 153);
    width: 250px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.3);
    z-index: 1;
    padding: 0.8vw 1.5vw;
    box-sizing: border-box;
}
.dropdown:hover .dropdown-acc {
    display: block;
}
.menu-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: white;
    cursor: pointer;
}
@media (max-width: 768px) {
    .menu-toggle {
        display: block;
    }
    .navbar {
        display: none;
        flex-direction: column;
        background-color: #272745;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        z-index: 1000;
    }
    .navbar ul {
        flex-direction: column;
        gap: 0;
    }
    .navbar ul li {
        text-align: center;
    }
    .navbar ul li a {
        padding: 15px;
        display: block;
    }
    .navbar.active {
        display: flex;
    }
}
</style>

<script>
    // Toggle Navbar for Mobile
    const menuToggle = document.querySelector('.menu-toggle');
    const navbar = document.querySelector('.navbar');

    menuToggle.addEventListener('click', () => {
        navbar.classList.toggle('active');
    });
</script>
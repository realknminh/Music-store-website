<?php
include 'db.php';
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];

    $query = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $error = "Database error: " . $conn->error;
    } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['id'];

                if ($user['role'] === 'admin') {
                    header('Location: ' . BASE_URL . 'admin-home');
                } else {
                    header('Location: ' . BASE_URL . 'home');
                }
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    }
}
?>

<link rel="stylesheet" href="css/login.css">
<section id="login">
    <div class="login-container">
        <div class="emo-header">
            <img width="120" src="<?php echo BASE_URL; ?>images/ava.png" alt="Login Icon" class="login-icon">
            <h2>Log in</h2>
        </div>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="<?php echo BASE_URL; ?>login">
            <p class="form-label">Username</p>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>
            <p class="form-label">Password</p>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <input type="submit" name="login" value="Login" class="btn-login">
            <p class="signup-link">Don't have an account? <a href="<?php echo BASE_URL; ?>signup">Sign up</a></p>
        </form>
    </div>
</section>
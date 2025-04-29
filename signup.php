<?php
include 'db.php';
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error = "Username must be 3-20 characters and contain only letters, numbers, or underscores.";
    } else {
        // Check duplicate username or email
        $checkQuery = "SELECT id FROM users WHERE username = ? OR email = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert user into the db
            $query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                $error = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("sss", $username, $email, $hashed_password);
                if ($stmt->execute()) {
                    $success = "Signup successful! <a href=\"" . BASE_URL . "login\">Log in here</a>";
                } else {
                    $error = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        $checkStmt->close();
    }
}
?>

<link rel="stylesheet" href="css/style.css">
<section id="signup">
    <div class="signup-container">
        <h2>✩ Sign Up ✩</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (isset($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>
        <form method="POST" action="<?php echo BASE_URL; ?>signup">
            <p class="form-label">Username</p>
            <input type="text" id="username" name="username" placeholder="Enter your username" required pattern="[a-zA-Z0-9_]{3,20}" title="Username must be 3-20 characters and contain only letters, numbers, or underscores.">
            <p class="form-label">Email</p>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            <p class="form-label">Password</p>
            <input type="password" id="password" name="password" placeholder="Enter your password" minlength="8" required>
            <p class="form-label">Confirm Password</p>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" minlength="8" required>
            <input type="submit" name="reg" value="Sign up" class="btn-reg">
            <p class="login-link">Already have an account? <a href="<?php echo BASE_URL; ?>login">Log in</a></p>
        </form>
    </div>
</section>
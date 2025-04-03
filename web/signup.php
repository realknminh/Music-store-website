<?php
// filepath: c:\xampp\htdocs\lab1\signup.php
@include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash the pw

        // Insert user into the database
        $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $success = "Signup successful! <a href='login.php'>Log in here</a>";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main>
        <section id="signup">
            <div class="signup-container">
                <h2>✩ Sign Up ✩</h2>
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php elseif (isset($success)): ?>
                    <p style="color: green;"><?php echo $success; ?></p>
                <?php endif; ?>
                <form method="POST" action="">
                    <p class="form-label">Username</p>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    <br>
                    <p class="form-label">Email</p>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    <br>
                    <p class="form-label">Password</p>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <br>
                    <p class="form-label">Confirm Password</p>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                    <br>
                    <button type="submit">Signup</button>
                    <p>Already have an account? <a style="text-decoration: none; color: rgb(30, 69, 242);" href="login.php">Log in</a></p>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
<?php
// filepath: c:\xampp\htdocs\lab1\login.php
session_start();
@include 'db.php';
@include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Use a prepared statement to prevent SQL injection
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Assuming you have a 'role' column
            $_SESSION['user_id'] = $user['id'];

            // Redirect to the home page
            header('Location: home.php');
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main>
        <section id="login">
            <div class="login-container">
                <h2>✩ Log in ✩</h2>
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <form method="POST" action="">
                    <p class="form-label">Username</p>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    <br>
                    <p class="form-label">Password</p>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <br>
                    <button type="submit">Login</button>
                    <p style="margin-top: 18px ">Don't have an account? <a style="text-decoration: none; color: rgb(30, 69, 242);" href="signup.php">Sign up</a></p>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
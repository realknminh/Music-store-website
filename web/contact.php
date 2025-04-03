<?php
@include 'db.php';
@include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My New Website</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>  
    <main>
        <section id="contact">
        <div class="contact-container">
            <h2>Send us message 💌</h2>
                <p class="form-label";  style="margin-top: 35px;">Name</p>
                <input type="text" id="name" name="name" required>
                <br>
                <p class="form-label">Email</p>
                <input type="email" id="email" name="email" required>
                <br>
                <p class="form-label">Message</p>
                <textarea id="message" name="message" rows="4" required></textarea>
                <br>
                <button type="submit">Send</button>
        </div>
        </section>
    </main>
<?php
@include 'footer.php';
?>
</body>
</html>
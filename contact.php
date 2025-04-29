<link rel="stylesheet" href="css/contact.css">
<section class="contact-section">
    <div class="contact-wrapper">
        <!-- Contact Form -->
        <div class="contact-container">
            <h2> Send us a message 💌</h2>
            <p class="subtitle">We’d love to hear from you — whether it’s feedback, questions, or a shout-out!</p>

            <form action="send_message.php" method="post" class="contact-form">
                <label for="name">Your Name</label>
                <input type="text" name="name" id="name" placeholder="Enter your name" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Your email" required>

                <label for="message">Your Message</label>
                <textarea name="message" id="message" rows="5" placeholder="Write your message here..." required></textarea>

                <input type="submit" name="reg" value="SEND" class="btn-contact">
            </form>
        </div>

        <!-- Google Map -->
        <div class="map-container">
            <h3 class="map-title">📍 Our Location</h3>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.1465556425215!2d106.66030017480588!3d10.798895689345712!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ec8517614b1%3A0x4be632d4aab1229f!2zMjY4IEzDvSBUaMaw4bujbmcgS2nhu4d0LCBQaMaw4budbmcgMTAsIFF14bqtbiAxMCwgUXXhuq1uIDMsIEjDoCBO4buZaCBDaMOtIE1pbmgsIFZpZXRuYW0!5e0!3m2!1sen!2s!4v1713272933135!5m2!1sen!2s" 
                width="100%" 
                height="300" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
</section>

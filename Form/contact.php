<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact</title>
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>
<?php include '../Include/header.php'; ?>
<!-- Kontaktu form -->
<div class="contact-page-container">
    <div class="form-container">
        <h2>CONTACT</h2>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error">
                <?= htmlspecialchars($_GET['error']) ?>
            </p>
        <?php } ?>
        
        <?php if (isset($_GET['success'])) { ?>
            <p class="success">
                <?= htmlspecialchars($_GET['success']) ?>
            </p>
        <?php } ?>
        
        <form action="form.php" method="POST">
            <input type="text" name="name" placeholder="Your Name">
            <input type="email" name="email" placeholder="Your Email" required>
            <input type="text" name="subject" placeholder="Subject" required>
            <textarea name="text" rows="5" placeholder="Your Message"></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>

    <div class="assistance-text">
        <h2>🤝 Need Help? We're Here for You!</h2>
        <p>Have questions, feedback, or need help with your workout plan? Whether you're curious about a specific sport, experiencing technical issues, or just want to say hello - we're ready to assist.</p>
        <p>Feel free to reach out to us. We typically respond within 24-48 hours.</p>
        <p>Common reasons to contact us:</p>
        <ul>
            <li>Trouble accessing your account</li>
            <li>Questions about sport-specific training plans</li>
            <li>Suggestions for new features or sports</li>
            <li>Reporting bugs or errors</li>
        </ul>
        <p><strong>We're here to make your fitness journey better, every step of the way.</strong></p>
    </div>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>
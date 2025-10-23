<?php
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $message = sanitize($_POST['message']);
    
    // 这里可以添加邮件发送逻辑
    $_SESSION['message'] = "Thank you for your message! We'll get back to you soon.";
    redirect('contact.php');
}
?>

<?php include 'includes/header.php'; ?>

<div class="contact-container">
    <h1>Contact Us</h1>
    
    <form method="POST" class="contact-form">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
        <button type="submit">Send Message</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
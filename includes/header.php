<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <h1><a href="index.php">FurniCraft</a></h1>
            </div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="products/">Products</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                
                <?php if (isLoggedIn()): ?>
                    <a href="cart/">Cart (<span id="cart-count"><?php echo getCartCount(); ?></span>)</a>
                    <a href="user/profile.php">Profile</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin/">Admin</a>
                    <?php endif; ?>
                    <a href="auth/logout.php">Logout</a>
                <?php else: ?>
                    <a href="auth/login.php">Login</a>
                    <a href="auth/register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
<?php
require_once 'config/config.php';

// 获取特色产品
$stmt = $pdo->query("SELECT * FROM products WHERE is_active = TRUE ORDER BY created_at DESC LIMIT 6");
$featured_products = $stmt->fetchAll();

// 获取分类
$stmt = $pdo->query("SELECT * FROM categories LIMIT 4");
$categories = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<section class="hero">
    <div class="hero-content">
        <h1>Welcome to FurniCraft</h1>
        <p>Discover premium furniture for your home and office</p>
        <a href="products/" class="cta-button">Shop Now</a>
    </div>
</section>

<section class="categories">
    <h2>Shop by Category</h2>
    <div class="category-grid">
        <?php foreach ($categories as $category): ?>
            <div class="category-card">
                <div class="category-image">
                    <img src="assets/images/categories/<?php echo $category['image_url'] ?? 'default.png'; ?>" alt="<?php echo $category['name']; ?>">
                </div>
                <h3><?php echo $category['name']; ?></h3>
                <a href="products/?category=<?php echo $category['id']; ?>">Explore</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="featured-products">
    <h2>Featured Products</h2>
    <div class="product-grid">
        <?php foreach ($featured_products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/products/<?php echo $product['image_url'] ?? 'default.png'; ?>" alt="<?php echo $product['name']; ?>">
                </div>
                <h3><?php echo $product['name']; ?></h3>
                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                <?php if (isLoggedIn()): ?>
                    <button class="add-to-cart" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
                <?php else: ?>
                    <a href="auth/login.php" class="login-to-buy">Login to Purchase</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
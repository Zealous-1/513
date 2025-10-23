<?php
require_once '../config/config.php';

if (!isset($_GET['id'])) {
    redirect('index.php');
}

$product_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.id = ? AND p.is_active = TRUE");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['message'] = "Product not found";
    redirect('index.php');
}
?>

<?php include '../includes/header.php'; ?>

<div class="product-detail">
    <div class="product-images">
        <img src="../assets/images/products/<?php echo $product['image_url'] ?? 'default.png'; ?>" alt="<?php echo $product['name']; ?>">
    </div>
    
    <div class="product-info">
        <h1><?php echo $product['name']; ?></h1>
        <p class="category">Category: <?php echo $product['category_name']; ?></p>
        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
        
        <div class="product-specs">
            <h3>Specifications</h3>
            <p><strong>Dimensions:</strong> <?php echo $product['dimensions']; ?></p>
            <p><strong>Material:</strong> <?php echo $product['material']; ?></p>
            <p><strong>Color:</strong> <?php echo $product['color']; ?></p>
            <p><strong>Stock:</strong> <?php echo $product['stock_quantity']; ?> available</p>
        </div>
        
        <div class="product-description">
            <h3>Description</h3>
            <p><?php echo $product['description']; ?></p>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <div class="purchase-options">
                <div class="quantity-selector">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                </div>
                <button class="add-to-cart" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
            </div>
        <?php else: ?>
            <a href="../auth/login.php" class="login-to-buy">Please login to purchase this item</a>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
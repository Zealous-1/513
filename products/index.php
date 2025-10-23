<?php
require_once '../config/config.php';

$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.is_active = TRUE";

$params = [];

if ($category_filter) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_filter;
}

if ($search_query) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// 获取所有分类用于筛选
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="products-header">
    <h1>Our Products</h1>
    
    <form method="GET" class="search-filter">
        <input type="text" name="search" placeholder="Search products..." value="<?php echo $search_query; ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                    <?php echo $cat['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter</button>
    </form>
</div>

<div class="product-grid">
    <?php if (empty($products)): ?>
        <p class="no-products">No products found matching your criteria.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="../assets/images/products/<?php echo $product['image_url'] ?? 'default.png'; ?>" alt="<?php echo $product['name']; ?>">
                </div>
                <h3><?php echo $product['name']; ?></h3>
                <p class="category"><?php echo $product['category_name']; ?></p>
                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                <p class="dimensions"><?php echo $product['dimensions']; ?></p>
                
                <?php if (isLoggedIn()): ?>
                    <button class="add-to-cart" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
                    <a href="view.php?id=<?php echo $product['id']; ?>" class="view-details">View Details</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="login-to-buy">Login to Purchase</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
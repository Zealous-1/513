<?php
require_once '../config/config.php';

if (!isLoggedIn()) {
    $_SESSION['message'] = "Please login to access your cart";
    redirect('../auth/login.php');
}

// 初始化购物车
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 处理添加/删除操作
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
    } elseif (isset($_POST['remove_item'])) {
        $product_id = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
    }
    
    // 持久化购物车到数据库
    saveCartToDatabase();
}

// 获取购物车产品详情
$cart_items = [];
$total_amount = 0;

if (!empty($_SESSION['cart'])) {
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total_amount += $subtotal;
        
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

function saveCartToDatabase() {
    global $pdo;
    if (isset($_SESSION['user_id']) && !empty($_SESSION['cart'])) {
        $cart_data = json_encode($_SESSION['cart']);
        $stmt = $pdo->prepare("REPLACE INTO carts (user_id, cart_data) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $cart_data]);
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="cart-container">
    <h1>Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <p>Your cart is empty</p>
            <a href="../products/" class="cta-button">Continue Shopping</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <img src="../assets/images/products/<?php echo $item['product']['image_url'] ?? 'default.png'; ?>" alt="<?php echo $item['product']['name']; ?>">
                        </div>
                        
                        <div class="item-details">
                            <h3><?php echo $item['product']['name']; ?></h3>
                            <p class="price">$<?php echo number_format($item['product']['price'], 2); ?></p>
                        </div>
                        
                        <div class="item-quantity">
                            <input type="number" name="quantities[<?php echo $item['product']['id']; ?>]" 
                                   value="<?php echo $item['quantity']; ?>" min="1" 
                                   max="<?php echo $item['product']['stock_quantity']; ?>">
                        </div>
                        
                        <div class="item-subtotal">
                            $<?php echo number_format($item['subtotal'], 2); ?>
                        </div>
                        
                        <div class="item-actions">
                            <button type="submit" name="remove_item" class="remove-btn">Remove</button>
                            <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="total-amount">
                    <h3>Total: $<?php echo number_format($total_amount, 2); ?></h3>
                </div>
                
                <div class="cart-actions">
                    <button type="submit" name="update_cart" class="update-btn">Update Cart</button>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
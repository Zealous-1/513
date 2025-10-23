<?php
require_once '../config/config.php';

if (!isLoggedIn()) {
    $_SESSION['message'] = "Please login to checkout";
    redirect('../auth/login.php');
}

if (empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Your cart is empty";
    redirect('index.php');
}

// 获取用户信息
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// 处理结账
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = sanitize($_POST['shipping_address']);
    $payment_method = sanitize($_POST['payment_method']);
    
    // 验证库存
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, name, stock_quantity FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll();
    
    $errors = [];
    foreach ($products as $product) {
        if ($product['stock_quantity'] < $_SESSION['cart'][$product['id']]) {
            $errors[] = "Insufficient stock for {$product['name']}";
        }
    }
    
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // 计算总金额
            $total_amount = 0;
            foreach ($products as $product) {
                $total_amount += $product['price'] * $_SESSION['cart'][$product['id']];
            }
            
            // 创建订单
            $order_number = 'ORD' . date('YmdHis') . $_SESSION['user_id'];
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $order_number, $total_amount, $shipping_address, $payment_method]);
            $order_id = $pdo->lastInsertId();
            
            // 添加订单项
            foreach ($products as $product) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $product['id'], $_SESSION['cart'][$product['id']], $product['price']]);
                
                // 更新库存
                $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
                $stmt->execute([$_SESSION['cart'][$product['id']], $product['id']]);
            }
            
            $pdo->commit();
            
            // 清空购物车
            $_SESSION['cart'] = [];
            unset($_SESSION['cart']);
            
            $_SESSION['message'] = "Order placed successfully! Order #: $order_number";
            redirect('../user/orders.php');
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Order failed: " . $e->getMessage();
        }
    }
}

// 获取购物车总金额
$total_amount = 0;
if (!empty($_SESSION['cart'])) {
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $total_amount += $product['price'] * $_SESSION['cart'][$product['id']];
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="checkout-container">
    <h1>Checkout</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="checkout-content">
        <div class="order-summary">
            <h2>Order Summary</h2>
            <?php foreach ($_SESSION['cart'] as $product_id => $quantity): ?>
                <?php 
                $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();
                ?>
                <div class="order-item">
                    <span><?php echo $product['name']; ?> x <?php echo $quantity; ?></span>
                    <span>$<?php echo number_format($product['price'] * $quantity, 2); ?></span>
                </div>
            <?php endforeach; ?>
            <div class="order-total">
                <strong>Total: $<?php echo number_format($total_amount, 2); ?></strong>
            </div>
        </div>
        
        <form method="POST" class="checkout-form">
            <h2>Shipping Information</h2>
            
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" value="<?php echo $user['full_name'] ?? ''; ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?php echo $user['email']; ?>" readonly>
            </div>
            
            <div class="form-group">
                <label for="shipping_address">Shipping Address:*</label>
                <textarea id="shipping_address" name="shipping_address" required><?php echo $user['address'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="payment_method">Payment Method:*</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            
            <button type="submit" class="place-order-btn">Place Order</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
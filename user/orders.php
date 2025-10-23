<?php
require_once '../config/config.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// 获取用户订单
$stmt = $pdo->prepare("SELECT o.*, COUNT(oi.id) as item_count 
                      FROM orders o 
                      LEFT JOIN order_items oi ON o.id = oi.order_id 
                      WHERE o.user_id = ? 
                      GROUP BY o.id 
                      ORDER BY o.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="user-orders">
    <h1>My Orders</h1>
    
    <?php if (empty($orders)): ?>
        <p>You have no orders yet.</p>
        <a href="../products/" class="cta-button">Start Shopping</a>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <h3>Order #<?php echo $order['order_number']; ?></h3>
                            <p>Date: <?php echo date('M j, Y', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="order-status">
                            <span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                            <p class="total">$<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                    </div>
                    
                    <div class="order-details">
                        <p><strong>Items:</strong> <?php echo $order['item_count']; ?></p>
                        <p><strong>Shipping Address:</strong> <?php echo $order['shipping_address']; ?></p>
                    </div>
                    
                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="view-details">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
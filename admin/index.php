<?php
require_once '../config/config.php';

if (!isAdmin()) {
    $_SESSION['message'] = "Access denied";
    redirect('../index.php');
}

// 获取统计数据
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_revenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'paid'")->fetchColumn();

// 获取最近订单
$recent_orders = $pdo->query("SELECT o.*, u.username 
                             FROM orders o 
                             LEFT JOIN users u ON o.user_id = u.id 
                             ORDER BY o.created_at DESC 
                             LIMIT 5")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Products</h3>
            <p><?php echo $total_products; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Orders</h3>
            <p><?php echo $total_orders; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?php echo $total_users; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <p>$<?php echo number_format($total_revenue, 2); ?></p>
        </div>
    </div>
    
    <div class="admin-sections">
        <div class="admin-section">
            <h2>Quick Actions</h2>
            <div class="action-links">
                <a href="products.php" class="action-btn">Manage Products</a>
                <a href="orders.php" class="action-btn">Manage Orders</a>
                <a href="users.php" class="action-btn">Manage Users</a>
                <a href="categories.php" class="action-btn">Manage Categories</a>
            </div>
        </div>
        
        <div class="admin-section">
            <h2>Recent Orders</h2>
            <div class="recent-orders">
                <?php foreach ($recent_orders as $order): ?>
                    <div class="order-item">
                        <span>#<?php echo $order['order_number']; ?></span>
                        <span><?php echo $order['username']; ?></span>
                        <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                        <span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
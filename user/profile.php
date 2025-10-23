<?php
require_once '../config/config.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// 获取用户信息
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $address = sanitize($_POST['address']);
    $phone = sanitize($_POST['phone']);
    
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, address = ?, phone = ? WHERE id = ?");
    if ($stmt->execute([$full_name, $email, $address, $phone, $_SESSION['user_id']])) {
        $_SESSION['message'] = "Profile updated successfully";
        $_SESSION['email'] = $email;
    }
    
    redirect('profile.php');
}
?>

<?php include '../includes/header.php'; ?>

<div class="user-profile">
    <h1>My Profile</h1>
    
    <form method="POST" class="profile-form">
        <div class="form-group">
            <label>Username:</label>
            <input type="text" value="<?php echo $user['username']; ?>" readonly>
        </div>
        
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo $user['full_name'] ?? ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea id="address" name="address"><?php echo $user['address'] ?? ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" value="<?php echo $user['phone'] ?? ''; ?>">
        </div>
        
        <button type="submit">Update Profile</button>
    </form>
    
    <div class="profile-actions">
        <a href="orders.php" class="action-btn">View My Orders</a>
        <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
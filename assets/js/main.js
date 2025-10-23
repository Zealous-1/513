// 购物车功能
document.addEventListener('DOMContentLoaded', function() {
    // 添加到购物车按钮
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantity = document.getElementById('quantity') ? 
                            document.getElementById('quantity').value : 1;
            
            addToCart(productId, quantity);
        });
    });
    
    // 更新购物车数量显示
    updateCartCount();
});

function addToCart(productId, quantity = 1) {
    fetch('cart/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            showMessage('Product added to cart!', 'success');
        } else {
            showMessage('Failed to add product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred', 'error');
    });
}

function updateCartCount() {
    fetch('cart/get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = data.count;
        }
    });
}

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert ${type}`;
    messageDiv.textContent = message;
    
    document.querySelector('main').prepend(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

// 表单验证
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'red';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }
    });
    
    return isValid;
}
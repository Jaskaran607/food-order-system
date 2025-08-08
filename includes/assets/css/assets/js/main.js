document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
        });
    }
    
    // Initialize cart count
    updateCartCount();
    
    // Add to cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const dishId = this.getAttribute('data-dish-id');
            const restaurantId = this.getAttribute('data-restaurant-id');
            
            addToCart(dishId, restaurantId);
        });
    });
    
    // Cart functions
    function addToCart(dishId, restaurantId) {
        fetch('api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add&dish_id=${dishId}&restaurant_id=${restaurantId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.totalItems);
                showToast('Item added to cart');
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }
    
    function updateCartCount() {
        fetch('api/cart.php?action=get_count')
        .then(response => response.json())
        .then(data => {
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(el => {
                el.textContent = data.totalItems;
                el.style.display = data.totalItems > 0 ? 'flex' : 'none';
            });
        });
    }
    
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
});

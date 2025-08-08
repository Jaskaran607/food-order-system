// assets/js/cart.js
$(document).ready(function() {
    // Add to cart
    $('.add-to-cart').click(function() {
        const dishId = $(this).data('dish-id');
        const restaurantId = $(this).data('restaurant-id');
        
        $.ajax({
            url: 'api/cart.php',
            method: 'POST',
            data: { action: 'add', dish_id: dishId, restaurant_id: restaurantId },
            success: function(response) {
                updateCartCount(response.totalItems);
                showToast('Item added to cart');
            }
        });
    });
    
    // Update cart count display
    function updateCartCount(count) {
        $('.cart-count').text(count);
    }
    
    // Show toast notification
    function showToast(message) {
        // Implementation for toast notification
    }
});

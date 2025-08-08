<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: ../login.php');
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: ../restaurants/browse.php');
    exit;
}

$db = new Database();

// Get user info
$db->query("SELECT * FROM users WHERE user_id = :id");
$db->bind(':id', $_SESSION['user_id']);
$user = $db->single();

// Get restaurant info
$restaurantId = $_SESSION['cart_restaurant'];
$db->query("SELECT * FROM restaurants WHERE restaurant_id = :id");
$db->bind(':id', $restaurantId);
$restaurant = $db->single();

// Calculate order total
$subtotal = 0;
foreach ($_SESSION['cart'] as $dishId => $item) {
    $db->query("SELECT price FROM dishes WHERE dish_id = :id");
    $db->bind(':id', $dishId);
    $dish = $db->single();
    $subtotal += $dish->price * $item['quantity'];
}

$deliveryFee = 2.99;
$tax = $subtotal * 0.08;
$total = $subtotal + $deliveryFee + $tax;
?>

<div class="container">
    <h1>Checkout</h1>
    
    <div class="checkout-grid">
        <div class="delivery-info">
            <h2>Delivery Information</h2>
            <form id="checkout-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user->name); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user->phone); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" required><?php echo htmlspecialchars($user->address); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="instructions">Delivery Instructions (Optional)</label>
                    <textarea id="instructions" name="instructions" placeholder="e.g., Leave at front door"></textarea>
                </div>
                
                <h2>Payment Method</h2>
                <div class="payment-methods">
                    <div class="payment-option">
                        <input type="radio" id="cod" name="payment_method" value="COD" checked>
                        <label for="cod">Cash on Delivery</label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" id="online" name="payment_method" value="Online">
                        <label for="online">Online Payment (Credit/Debit Card)</label>
                    </div>
                </div>
                
                <div id="card-details" style="display: none;">
                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiry">Expiry Date</label>
                            <input type="text" id="expiry" name="expiry" placeholder="MM/YY">
                        </div>
                        
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn">Place Order</button>
            </form>
        </div>
        
        <div class="order-summary">
            <h2>Order Summary</h2>
            <div class="restaurant-summary">
                <h3><?php echo $restaurant->name; ?></h3>
                <p><?php echo $restaurant->cuisine_type; ?></p>
            </div>
            
            <div class="order-items">
                <?php foreach ($_SESSION['cart'] as $dishId => $item): ?>
                <?php
                    $db->query("SELECT * FROM dishes WHERE dish_id = :id");
                    $db->bind(':id', $dishId);
                    $dish = $db->single();
                ?>
                <div class="order-item">
                    <div class="item-quantity"><?php echo $item['quantity']; ?>x</div>
                    <div class="item-name"><?php echo $dish->name; ?></div>
                    <div class="item-price">$<?php echo number_format($dish->price * $item['quantity'], 2); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="order-totals">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="total-row">
                    <span>Delivery Fee</span>
                    <span>$<?php echo number_format($deliveryFee, 2); ?></span>
                </div>
                <div class="total-row">
                    <span>Tax</span>
                    <span>$<?php echo number_format($tax, 2); ?></span>
                </div>
                <div class="total-row grand-total">
                    <span>Total</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide card details based on payment method
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cardDetails = document.getElementById('card-details');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            cardDetails.style.display = this.value === 'Online' ? 'block' : 'none';
        });
    });
    
    // Handle form submission
    const form = document.getElementById('checkout-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        formData.append('restaurant_id', '<?php echo $restaurantId; ?>');
        formData.append('total_amount', '<?php echo $total; ?>');
        
        fetch('../api/orders.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '../user/orders.php?order_id=' + data.order_id;
            } else {
                alert(data.message || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>

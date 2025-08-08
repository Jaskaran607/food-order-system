
<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: ../restaurants/browse.php');
    exit;
}

$db = new Database();

// Get restaurant info
$restaurantId = $_SESSION['cart_restaurant'];
$db->query("SELECT * FROM restaurants WHERE restaurant_id = :id");
$db->bind(':id', $restaurantId);
$restaurant = $db->single();

// Prepare cart items with full dish info
$cartItems = [];
$subtotal = 0;

foreach ($_SESSION['cart'] as $dishId => $item) {
    $db->query("SELECT * FROM dishes WHERE dish_id = :id");
    $db->bind(':id', $dishId);
    $dish = $db->single();
    
    if ($dish) {
        $item['name'] = $dish->name;
        $item['price'] = $dish->price;
        $item['description'] = $dish->description;
        $item['image'] = $dish->image_path;
        $cartItems[] = $item;
        $subtotal += $dish->price * $item['quantity'];
    }
}

$deliveryFee = 2.99; // Example delivery fee
$tax = $subtotal * 0.08; // Example tax rate
$total = $subtotal + $deliveryFee + $tax;
?>

<div class="container">
    <h1>Your Cart</h1>
    <p class="restaurant-name">Ordering from: <?php echo $restaurant->name; ?></p>
    
    <div class="cart-items">
        <?php foreach ($cartItems as $item): ?>
        <div class="cart-item">
            <img src="../assets/images/dishes/<?php echo $item['image'] ?: 'default.jpg'; ?>" alt="<?php echo $item['name']; ?>">
            <div class="item-details">
                <h3><?php echo $item['name']; ?></h3>
                <p class="description"><?php echo $item['description']; ?></p>
                <div class="quantity-controls">
                    <button class="quantity-btn" data-dish-id="<?php echo $item['dish_id']; ?>" data-action="decrease">-</button>
                    <span class="quantity"><?php echo $item['quantity']; ?></span>
                    <button class="quantity-btn" data-dish-id="<?php echo $item['dish_id']; ?>" data-action="increase">+</button>
                </div>
            </div>
            <div class="item-price">
                <p>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                <button class="remove-btn" data-dish-id="<?php echo $item['dish_id']; ?>">Remove</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="cart-summary">
        <div class="summary-row">
            <span>Subtotal</span>
            <span>$<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <div class="summary-row">
            <span>Delivery Fee</span>
            <span>$<?php echo number_format($deliveryFee, 2); ?></span>
        </div>
        <div class="summary-row">
            <span>Tax</span>
            <span>$<?php echo number_format($tax, 2); ?></span>
        </div>
        <div class="summary-row total">
            <span>Total</span>
            <span>$<?php echo number_format($total, 2); ?></span>
        </div>
    </div>
    
    <div class="checkout-actions">
        <a href="../restaurants/browse.php" class="btn btn-secondary">Continue Shopping</a>
        <a href="checkout.php" class="btn">Proceed to Checkout</a>
    </div>
</div>

<script src="../assets/js/cart.js"></script>
<?php require_once '../includes/footer.php'; ?>

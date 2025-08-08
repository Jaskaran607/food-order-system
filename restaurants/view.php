<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if (!isset($_GET['id'])) {
    header('Location: browse.php');
    exit;
}

$restaurantId = $_GET['id'];

$db = new Database();
$db->query("SELECT * FROM restaurants WHERE restaurant_id = :id AND is_approved = 1");
$db->bind(':id', $restaurantId);
$restaurant = $db->single();

if (!$restaurant) {
    header('Location: browse.php');
    exit;
}

$db->query("SELECT * FROM dishes WHERE restaurant_id = :id AND is_available = 1 ORDER BY category, name");
$db->bind(':id', $restaurantId);
$dishes = $db->resultSet();

// Group dishes by category
$categories = [];
foreach ($dishes as $dish) {
    if (!isset($categories[$dish->category])) {
        $categories[$dish->category] = [];
    }
    $categories[$dish->category][] = $dish;
}
?>

<div class="restaurant-header">
    <div class="container">
        <div class="restaurant-info">
            <img src="../assets/images/restaurants/<?php echo $restaurant->image_path ?: 'default.jpg'; ?>" alt="<?php echo $restaurant->name; ?>">
            <div class="info-text">
                <h1><?php echo $restaurant->name; ?></h1>
                <p class="cuisine"><?php echo $restaurant->cuisine_type; ?></p>
                <p class="address"><?php echo $restaurant->address; ?></p>
                <p class="hours">Open: <?php echo date('g:i a', strtotime($restaurant->opening_time)); ?> - <?php echo date('g:i a', strtotime($restaurant->closing_time)); ?></p>
                <p class="phone"><?php echo $restaurant->phone; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="menu-section">
        <?php foreach ($categories as $category => $items): ?>
        <h2 class="category-title"><?php echo $category ?: 'Other'; ?></h2>
        <div class="menu-items">
            <?php foreach ($items as $dish): ?>
            <div class="menu-item">
                <div class="item-info">
                    <h3><?php echo $dish->name; ?></h3>
                    <p class="description"><?php echo $dish->description; ?></p>
                    <p class="price">$<?php echo number_format($dish->price, 2); ?></p>
                </div>
                <div class="item-actions">
                    <button class="btn add-to-cart" data-dish-id="<?php echo $dish->dish_id; ?>" data-restaurant-id="<?php echo $restaurant->restaurant_id; ?>">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

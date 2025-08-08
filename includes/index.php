<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$db = new Database();
$db->query("SELECT * FROM restaurants WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 6");
$restaurants = $db->resultSet();

$db->query("SELECT d.*, r.name as restaurant_name FROM dishes d 
            JOIN restaurants r ON d.restaurant_id = r.restaurant_id 
            WHERE d.is_available = 1 AND r.is_approved = 1 
            ORDER BY d.created_at DESC LIMIT 8");
$dishes = $db->resultSet();
?>

<div class="hero">
    <div class="container">
        <h1>Order Food Online</h1>
        <p>From your favorite restaurants delivered to your doorstep</p>
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="query" placeholder="Search for restaurants or dishes...">
            <button type="submit">Search</button>
        </form>
    </div>
</div>

<section class="featured-restaurants">
    <div class="container">
        <h2>Featured Restaurants</h2>
        <div class="restaurant-grid">
            <?php foreach($restaurants as $restaurant): ?>
            <div class="restaurant-card">
                <img src="assets/images/restaurants/<?php echo $restaurant->image_path ?: 'default.jpg'; ?>" alt="<?php echo $restaurant->name; ?>">
                <h3><?php echo $restaurant->name; ?></h3>
                <p class="cuisine"><?php echo $restaurant->cuisine_type; ?></p>
                <a href="restaurants/view.php?id=<?php echo $restaurant->restaurant_id; ?>" class="btn">View Menu</a>
            </div>
            <?php endforeach; ?>
        </div>
        <a href="restaurants/browse.php" class="btn view-all">View All Restaurants</a>
    </div>
</section>

<section class="popular-dishes">
    <div class="container">
        <h2>Popular Dishes</h2>
        <div class="dish-grid">
            <?php foreach($dishes as $dish): ?>
            <div class="dish-card">
                <img src="assets/images/dishes/<?php echo $dish->image_path ?: 'default.jpg'; ?>" alt="<?php echo $dish->name; ?>">
                <div class="dish-info">
                    <h3><?php echo $dish->name; ?></h3>
                    <p class="restaurant"><?php echo $dish->restaurant_name; ?></p>
                    <p class="price">$<?php echo number_format($dish->price, 2); ?></p>
                    <button class="btn add-to-cart" data-dish-id="<?php echo $dish->dish_id; ?>" data-restaurant-id="<?php echo $dish->restaurant_id; ?>">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

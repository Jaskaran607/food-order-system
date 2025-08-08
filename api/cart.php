<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

session_start();

header('Content-Type: application/json');

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) {
    if ($_GET['action'] === 'get_count') {
        $totalItems = isset($_SESSION['cart']) ? array_reduce($_SESSION['cart'], function($carry, $item) {
            return $carry + $item['quantity'];
        }, 0) : 0;
        
        echo json_encode(['totalItems' => $totalItems]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $dishId = $_POST['dish_id'];
        $restaurantId = $_POST['restaurant_id'];
        
        // Check if cart exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
            $_SESSION['cart_restaurant'] = $restaurantId;
        }
        
        // Check if adding from same restaurant
        if ($_SESSION['cart_restaurant'] != $restaurantId) {
            echo json_encode([
                'success' => false,
                'message' => 'You can only order from one restaurant at a time. Clear your cart or finish your current order first.'
            ]);
            exit;
        }
        
        // Get dish details
        $db->query("SELECT * FROM dishes WHERE dish_id = :dish_id");
        $db->bind(':dish_id', $dishId);
        $dish = $db->single();
        
        if (!$dish) {
            echo json_encode(['success' => false, 'message' => 'Dish not found']);
            exit;
        }
        
        // Add to cart or update quantity
        if (isset($_SESSION['cart'][$dishId])) {
            $_SESSION['cart'][$dishId]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$dishId] = [
                'dish_id' => $dishId,
                'name' => $dish->name,
                'price' => $dish->price,
                'quantity' => 1,
                'image' => $dish->image_path
            ];
        }
        
        // Calculate total items
        $totalItems = array_reduce($_SESSION['cart'], function($carry, $item) {
            return $carry + $item['quantity'];
        }, 0);
        
        echo json_encode(['success' => true, 'totalItems' => $totalItems]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>

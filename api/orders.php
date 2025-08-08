<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

session_start();

header('Content-Type: application/json');

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to place an order']);
        exit;
    }
    
    // Check if cart exists
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
        exit;
    }
    
    // Validate required fields
    $required = ['name', 'phone', 'address', 'payment_method'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
            exit;
        }
    }
    
    // Start transaction
    try {
        $db->beginTransaction();
        
        // Create order
        $db->query("INSERT INTO orders (user_id, restaurant_id, total_amount, delivery_address, contact_phone, payment_method, status) 
                    VALUES (:user_id, :restaurant_id, :total_amount, :delivery_address, :contact_phone, :payment_method, 'Pending')");
        
        $db->bind(':user_id', $_SESSION['user_id']);
        $db->bind(':restaurant_id', $_POST['restaurant_id']);
        $db->bind(':total_amount', $_POST['total_amount']);
        $db->bind(':delivery_address', $_POST['address']);
        $db->bind(':contact_phone', $_POST['phone']);
        $db->bind(':payment_method', $_POST['payment_method']);
        
        $db->execute();
        $orderId = $db->lastInsertId();
        
        // Add order items
        foreach ($_SESSION['cart'] as $dishId => $item) {
            $db->query("INSERT INTO order_items (order_id, dish_id, quantity, price) 
                        VALUES (:order_id, :dish_id, :quantity, (SELECT price FROM dishes WHERE dish_id = :dish_id))");
            
            $db->bind(':order_id', $orderId);
            $db->bind(':dish_id', $dishId);
            $db->bind(':quantity', $item['quantity']);
            
            $db->execute();
        }
        
        // Clear cart
        unset($_SESSION['cart']);
        unset($_SESSION['cart_restaurant']);
        
        $db->commit();
        
        echo json_encode(['success' => true, 'order_id' => $orderId]);
        exit;
        
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Order failed: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>

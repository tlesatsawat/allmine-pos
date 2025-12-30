<?php
require_once 'db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Save new order
        $input = json_decode(file_get_contents('php://input'), true);
        $queueNumber = $input['queue_number'] ?? '';
        $totalPrice = $input['total_price'] ?? 0;
        $paymentMethod = $input['payment_method'] ?? '';
        $memberId = $input['member_id'] ?? null;
        $items = $input['items'] ?? [];
        
        if (empty($queueNumber) || $totalPrice <= 0 || empty($paymentMethod)) {
            http_response_code(400);
            echo json_encode(['error' => 'Queue number, total price, and payment method are required']);
            exit;
        }
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Generate queue number (daily reset)
        $today = date('Y-m-d');
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = ?");
        $stmt->execute([$today]);
        $count = $stmt->fetch()['count'];
        $newQueueNumber = 'A' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (queue_number, total_price, payment_method, member_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$newQueueNumber, $totalPrice, $paymentMethod, $memberId]);
        $orderId = $pdo->lastInsertId();
        
        // Insert order items
        foreach ($items as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items 
                (order_id, product_name, size, sweetness, toppings_json, price, quantity) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $orderId,
                $item['name'],
                $item['size'] ?? null,
                $item['sweetness'] ?? '100%',
                json_encode($item['toppings'] ?? []),
                $item['price'],
                $item['quantity'] ?? 1
            ]);
        }
        
        // Update member points if applicable
        if ($memberId) {
            // For this example, we'll add 1 point for every 50 THB spent
            $pointsToAdd = floor($totalPrice / 50);
            if ($pointsToAdd > 0) {
                $stmt = $pdo->prepare("UPDATE members SET points = points + ? WHERE id = ?");
                $stmt->execute([$pointsToAdd, $memberId]);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'queue_number' => $newQueueNumber
        ]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get today's orders summary
        $date = $_GET['date'] ?? date('Y-m-d');
        
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_orders,
                SUM(total_price) as total_sales,
                AVG(total_price) as avg_order_value
            FROM orders 
            WHERE DATE(created_at) = ?
        ");
        $stmt->execute([$date]);
        $summary = $stmt->fetch();
        
        // Get best selling items
        $stmt = $pdo->prepare("
            SELECT 
                product_name,
                SUM(quantity) as total_quantity
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE DATE(o.created_at) = ?
            GROUP BY product_name
            ORDER BY total_quantity DESC
            LIMIT 5
        ");
        $stmt->execute([$date]);
        $bestSellers = $stmt->fetchAll();
        
        echo json_encode([
            'summary' => $summary,
            'best_sellers' => $bestSellers
        ]);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    $pdo->rollback();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
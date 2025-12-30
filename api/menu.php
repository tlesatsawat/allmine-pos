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
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get all categories
        $stmt = $pdo->query("
            SELECT c.id, c.name as category_name,
                   JSON_ARRAYAGG(
                       JSON_OBJECT(
                           'id', p.id,
                           'name', p.name,
                           'price', pp.price,
                           'image', p.image,
                           'is_active', p.is_active,
                           'sizes', (
                               SELECT JSON_ARRAYAGG(
                                   JSON_OBJECT(
                                       'id', s.id,
                                       'name', s.name,
                                       'price', pp2.price
                                   )
                               )
                               FROM sizes s
                               JOIN product_prices pp2 ON s.id = pp2.size_id
                               WHERE pp2.product_id = p.id
                           )
                       )
                   ) as products
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id
            LEFT JOIN product_prices pp ON p.id = pp.product_id AND pp.size_id = 1
            WHERE p.is_active = 1
            GROUP BY c.id, c.name
            ORDER BY c.id
        ");
        
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the response
        $result = [];
        foreach ($categories as $category) {
            $products = json_decode($category['products'], true);
            // Filter out null values
            $products = array_filter($products, function($p) {
                return $p['id'] !== null;
            });
            
            $result[] = [
                'id' => $category['id'],
                'name' => $category['category_name'],
                'products' => array_values($products)
            ];
        }
        
        echo json_encode($result);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
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
        // Get all settings
        $stmt = $pdo->query("SELECT key_name, value FROM shop_settings");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        echo json_encode($settings);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update settings
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input data']);
            exit;
        }
        
        $pdo->beginTransaction();
        
        foreach ($input as $key => $value) {
            // Check if setting exists
            $stmt = $pdo->prepare("SELECT id FROM shop_settings WHERE key_name = ?");
            $stmt->execute([$key]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Update existing setting
                $stmt = $pdo->prepare("UPDATE shop_settings SET value = ? WHERE key_name = ?");
                $stmt->execute([$value, $key]);
            } else {
                // Insert new setting
                $stmt = $pdo->prepare("INSERT INTO shop_settings (key_name, value) VALUES (?, ?)");
                $stmt->execute([$key, $value]);
            }
        }
        
        $pdo->commit();
        
        echo json_encode(['success' => true]);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    $pdo->rollback();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
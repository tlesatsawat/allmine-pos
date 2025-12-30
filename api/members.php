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
        // Get member by phone number
        $phone = $_GET['phone'] ?? '';
        
        if (empty($phone)) {
            http_response_code(400);
            echo json_encode(['error' => 'Phone number is required']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT id, phone, name, points FROM members WHERE phone = ?");
        $stmt->execute([$phone]);
        $member = $stmt->fetch();
        
        if ($member) {
            echo json_encode($member);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Member not found']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create or update member
        $input = json_decode(file_get_contents('php://input'), true);
        $phone = $input['phone'] ?? '';
        $name = $input['name'] ?? '';
        
        if (empty($phone) || empty($name)) {
            http_response_code(400);
            echo json_encode(['error' => 'Phone and name are required']);
            exit;
        }
        
        // Check if member exists
        $stmt = $pdo->prepare("SELECT id FROM members WHERE phone = ?");
        $stmt->execute([$phone]);
        $existingMember = $stmt->fetch();
        
        if ($existingMember) {
            // Update existing member
            $stmt = $pdo->prepare("UPDATE members SET name = ? WHERE phone = ?");
            $stmt->execute([$name, $phone]);
            
            $stmt = $pdo->prepare("SELECT id, phone, name, points FROM members WHERE phone = ?");
            $stmt->execute([$phone]);
            $member = $stmt->fetch();
        } else {
            // Create new member
            $stmt = $pdo->prepare("INSERT INTO members (phone, name, points) VALUES (?, ?, 0)");
            $stmt->execute([$phone, $name]);
            
            $memberId = $pdo->lastInsertId();
            
            $stmt = $pdo->prepare("SELECT id, phone, name, points FROM members WHERE id = ?");
            $stmt->execute([$memberId]);
            $member = $stmt->fetch();
        }
        
        echo json_encode($member);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
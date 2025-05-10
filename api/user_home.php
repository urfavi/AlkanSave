<?php
require_once '../includes/config.php';
session_start();

header('Content-Type: application/json');

try {
    // 1. Authentication
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // 2. Fetch data (same as previous example)
    $userStmt = $pdo->prepare("SELECT FirstName...");
    // ... [rest of your data fetching logic] ...

    // 3. Return JSON
    echo json_encode([
        'user' => [
            'name' => $user['FirstName'] . ' ' . $user['LastName'],
            'quote' => $dailyQuote
        ],
        'savings' => [
            'total' => $savings['total_saved'] ?? 0,
            'progress' => round($savingsProgress, 2)
        ],
        'deadlines' => $deadlines
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?> 
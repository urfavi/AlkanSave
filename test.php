<?php
require_once 'includes/config.php'; // Load database connection

try {
    // Test query: Fetch 1 record from User table
    $stmt = $pdo->query("SELECT * FROM User LIMIT 1");
    $result = $stmt->fetch();
    
    echo "<h1>Database Connection Successful!</h1>";
    echo "<pre>";
    print_r($result); // Show the fetched data
    echo "</pre>";
} catch (PDOException $e) {
    die("<h1>Database Error:</h1> <p>" . $e->getMessage() . "</p>");
}
?>
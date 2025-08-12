<?php
$host = 'localhost';
$dbname = 'u687661100_udho_db';
$username = 'u687661100_admin';
$password = 'Udhodbms01';



try {
    // Create PDO connection with additional options
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $username, 
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    // Uncomment to verify connection (for debugging only)
    // echo "Database connection successful!";
    
} catch (PDOException $e) {
    // Log the full error for debugging
    error_log("Database Connection Error: " . $e->getMessage());
    
    // Display user-friendly message
    die("Could not connect to the database. Please try again later.");
}
?>
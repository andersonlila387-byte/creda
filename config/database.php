<?php
/**
 * Scriptly Database PDO Connection Singleton
 */
require_once __DIR__ . '/brand.php';

function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $host = 'localhost';
        $db   = 'creda_db';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // Log connection failure and throw clean response
            error_log('Database Connection Error: ' . $e->getMessage());
            throw new Exception('Unable to connect to the database. Please try again later.');
        }
    }
    
    return $pdo;
}

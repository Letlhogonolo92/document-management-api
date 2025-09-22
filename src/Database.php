<?php

namespace App;

use PDO;
use PDOException;

/**
 * Database connection singleton.
 * Ensures only one PDO instance is created.
 */
class Database {
    private static ?PDO $instance = null;

    /**
     * Get the PDO connection instance.
     *
     * @return PDO
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            // Load DB config
            $config = require __DIR__ . '/../config/database.php';

            try {
                self::$instance = new PDO(
                    "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
                    $config['username'],
                    $config['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                // Return JSON error and stop execution
                http_response_code(500);
                echo json_encode([
                    'error' => 'Database connection failed',
                    'details' => $e->getMessage()
                ]);
                exit;
            }
        }

        return self::$instance;
    }
}
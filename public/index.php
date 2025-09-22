<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\DocumentController;
use App\Models\Document;

// Set common headers for JSON API and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Basic routing
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$controller = new DocumentController();

switch (true) {
    // List documents
    case $uri === '/api/documents' && $method === 'GET':
        $controller->index();
        break;

    // Upload document
    case $uri === '/api/documents' && $method === 'POST':
        $controller->store();
        break;

    // Search documents
    case $uri === '/api/search' && $method === 'GET':
        $query = $_GET['keyword'] ?? '';
        echo json_encode($query ? Document::searchDocumentByContent($query) : []);
        break;

    // Show or delete a specific document
    case preg_match('#^/api/documents/(\d+)$#', $uri, $matches):
        $id = (int)$matches[1];
        if ($method === 'GET') {
            $controller->show($id);
        } elseif ($method === 'DELETE') {
            $controller->destroy($id);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
        break;

    // Fallback for undefined routes
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
}
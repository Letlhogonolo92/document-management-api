<?php
namespace App\Controllers;

use App\Models\Document;

class DocumentController {

    /** List documents with pagination */
    public function index(): void {
        $page  = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 5);

        echo json_encode(Document::all($page, $limit));
    }

    /** Upload a new document */
    public function store(): void {
        if (!isset($_FILES['document'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        try {
            $id = Document::create($_FILES['document']);
            http_response_code(201);
            echo json_encode([
                'id' => $id,
                'message' => 'Document uploaded successfully'
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Upload failed',
                'details' => $e->getMessage()
            ]);
        }
    }

    /** Show a single document by ID */
    public function show(int $id): void {
        $doc = Document::find($id);

        if (!$doc) {
            http_response_code(404);
            echo json_encode(['error' => 'Document not found']);
            return;
        }

        echo json_encode($doc);
    }

    /** Delete a document by ID */
    public function destroy(int $id): void {
        if (Document::delete($id)) {
            echo json_encode(['message' => 'Document deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Document not found']);
        }
    }
}
<?php
namespace App\Models;

use App\Cache\Cache;
use App\Database;
use PDO;
use Smalot\PdfParser\Parser;

class Document {

    /**
     * Get paginated list.
     */
    public static function all(int $page = 1, int $limit = 5): array {
        $db = Database::getConnection();
        $offset = ($page - 1) * $limit;

        $stmt = $db->prepare("SELECT * FROM documents ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a document by ID.
     */
    public static function find(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM documents WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Create a new document from uploaded file.
     */
    public static function create(array $file): int {
        $db        = Database::getConnection();
        $uploadDir = __DIR__ . '/../../uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $path     = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            throw new \RuntimeException("Failed to move uploaded file.");
        }

        $content = self::extractContent($path, $file['name']);

        $stmt = $db->prepare("INSERT INTO documents (name, path, body) VALUES (:name, :path, :body)");
        $stmt->execute([
            ':name' => $file['name'],
            ':path' => $path,
            ':body' => $content
        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * Extract text content from a file (txt/pdf).
     */
    private static function extractContent(string $path, string $originalName): string {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        try {
            return match ($ext) {
                'txt' => file_get_contents($path),
                'pdf' => (new Parser())->parseFile($path)->getText(),
                default => ''
            };
        } catch (\Exception) {
            return '';
        }
    }

    /**
     * Full-text search in documents content.
     */
    public static function searchDocumentByContent(string $query): array {
        $cache = new Cache();

        // Clean expired cache files first
        $cache->deleteExpiredCachedFiles();

        // Check cache first
        $cached = $cache->get("search_" . $query);
        if ($cached !== null) {
            return $cached;
        }

        // Live DB query if no data was cached
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, path, body, created_at,
               MATCH(body) AGAINST(:q IN NATURAL LANGUAGE MODE) AS relevance
        FROM documents
        WHERE MATCH(body) AGAINST(:q IN NATURAL LANGUAGE MODE)
        ORDER BY relevance DESC
        LIMIT 50
    ");
        $stmt->execute([':q' => $query]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Highlight matching terms
        foreach ($results as &$doc) {
            $pattern = '/' . preg_quote($query, '/') . '/i';
            $doc['name'] = preg_replace($pattern, '<mark>$0</mark>', $doc['name']);
            $doc['body'] = preg_replace($pattern, '<mark>$0</mark>', $doc['body']);
        }

        $cache->set("search_" . $query, $results);

        return $results;
    }


    /**
     * Delete document by ID.
     */
    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM documents WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
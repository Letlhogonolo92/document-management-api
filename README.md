# Document Search API System

A simple Document Management application with file upload, search, and pagination built with PHP backend and Angular frontend.

## Features

- Drag & drop file upload
- Document listing with pagination
- Delete documents
- Search with highlighted results
- Responsive UI using Angular Material
- Loading and error handling

## Requirements

- PHP 8+
- Composer
- Node.js 20+
- Angular CLI
- MySQL

## Setup

### Backend

- cd document-management-api
- composer install
- php -S localhost:8000 -t public

### MySQL Setup Backend

- Open a new terminal tab
- cd document-management-api
  
- **MacOS (Homebrew):**

- brew install mysql
- brew services start mysql
- mysql -u root

**Linux (Ubuntu/Debian):**

- sudo apt update
- sudo apt install mysql-server -y
- sudo systemctl start mysql
- sudo systemctl enable mysql
- sudo mysql_secure_installation
- sudo mysql -u root -p

Inside the MySQL shell (mysql> prompt), paste:

> CREATE DATABASE documents;

> USE documents;

> CREATE TABLE documents (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    path VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    body TEXT,
    FULLTEXT KEY body_fulltext (body)
);

Verify Table Structure
> DESCRIBE documents;

Exit MySQL shell:
> exit;

### Test Endpoints

- GET http://localhost:8000/api/documents
- POST http://localhost:8000/api/documents (upload file via Postman/form-data)
- GET http://localhost:8000/api/documents/1
- DELETE http://localhost:8000/api/documents/1

### Postman Collectio for convinience
- Here’s a ready-to-import Postman Json collection. You can import it directly into Postman and start testing all endpoints immediately

{
  "info": {
    "name": "Documents management",
    "_postman_id": "d5a8f4c7-1234-5678-9abc-0def12345678",
    "description": "Test collection for simple Documents REST API in vanilla PHP",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Get All Documents",
      "request": {
        "method": "GET",
        "header": [],
        "url": {
          "raw": "http://localhost:8000/api/documents",
          "protocol": "http",
          "host": ["localhost"],
          "port": "8000",
          "path": ["api", "documents"]
        }
      }
    },
    {
      "name": "Get Single Document",
      "request": {
        "method": "GET",
        "header": [],
        "url": {
          "raw": "http://localhost:8000/api/documents/1",
          "protocol": "http",
          "host": ["localhost"],
          "port": "8000",
          "path": ["api", "documents", "1"]
        }
      }
    },
    {
      "name": "Create Document (File Upload)",
      "request": {
        "method": "POST",
        "header": [],
        "body": {
          "mode": "formdata",
          "formdata": [
            {
              "key": "document",
              "type": "file",
              "src": ""
            }
          ]
        },
        "url": {
          "raw": "http://localhost:8000/api/documents",
          "protocol": "http",
          "host": ["localhost"],
          "port": "8000",
          "path": ["api", "documents"]
        }
      }
    },
    {
      "name": "Delete Document",
      "request": {
        "method": "DELETE",
        "header": [],
        "url": {
          "raw": "http://localhost:8000/api/documents/1",
          "protocol": "http",
          "host": ["localhost"],
          "port": "8000",
          "path": ["api", "documents", "1"]
        }
      }
    },
    {
      "name": "Search Documents",
      "request": {
        "method": "GET",
        "header": [],
        "url": {
          "raw": "http://localhost:8000/api/search?keyword=example",
          "protocol": "http",
          "host": ["localhost"],
          "port": "8000",
          "path": ["api", "search"],
          "query": [
            {
              "key": "keyword",
              "value": "example"
            }
          ]
        }
      }
    }
  ]
}

### Now let's set up the Angular front end to see all of this magic in action !!! 
- Repo ( https://github.com/Letlhogonolo92/document-management-frontend )

### Frontend Usage
- Upload files via drag & drop or file selector
- Search documents using the input box
- View details or delete documents using action buttons
- Pagination available at the bottom of the list


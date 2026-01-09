<?php
// index.php
require_once 'config.php';

// Simple Router
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove query strings and trailing slashes for cleaner matching
// Adjust based on how you serve it (e.g. php -S localhost:8000)
// Assuming relative paths for simplicity:
// If running in subfolder, might need adjustment.
// For `php -S`, path defaults to /

// Helper to check auth
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Routing
switch ($path) {
    case '/':
    case '/index.php':
        if (is_logged_in()) {
            header('Location: /dashboard');
        } else {
            include 'views/login.php';
        }
        break;

    case '/login':
        if (is_logged_in()) {
            header('Location: /dashboard');
        } else {
            include 'views/login.php';
        }
        break;

    case '/dashboard':
        if (!is_logged_in()) {
            header('Location: /login');
            exit;
        }
        include 'views/editor.php';
        break;

    case '/api/send-otp':
        require 'api/auth.php'; // Will handle the POST request
        break;
        
    case '/api/verify-otp':
        require 'api/auth.php';
        break;

    case '/api/save-invoice':
        if (!is_logged_in()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        require 'api/save_invoice.php';
        break;

    case '/download-pdf':
         // Allow download if public? Or only if creator?
         // Requirement says "Users must be able to download the invoice".
         // Usually safest if only creator, but maybe shareable link?
         // For now, restrict to session.
         if (!is_logged_in()) {
             header('Location: /login');
             exit;
         }
         require 'generate_pdf.php';
         break;

    case '/logout':
        session_destroy();
        header('Location: /login');
        break;

    default:
        // Serve static assets if they exist (for php built-in server)
        $file = __DIR__ . $path;
        if (file_exists($file) && !is_dir($file)) {
            $mime = mime_content_type($file);
            // Fix CSS mime type if not detected correctly (common issue)
            if (strpos($file, '.css') !== false) $mime = 'text/css';
            if (strpos($file, '.js') !== false) $mime = 'application/javascript';
            
            header("Content-Type: $mime");
            readfile($file);
            exit;
        }
        
        http_response_code(404);
        echo "404 Not Found";
        break;
}

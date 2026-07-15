<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    // Jalankan index.php asli dari folder public
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>Fatal Error Caught in api/index.php</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

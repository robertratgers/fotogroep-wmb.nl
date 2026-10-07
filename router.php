<?php
declare(strict_types=1);

/**
 * Router voor de PHP built-in server:
 * php -S localhost:8000 router.php
 *
 * Verbergt .php in de URL en houdt alle menu-/sectiepaden op de homepage.
 */

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rawurldecode(is_string($uriPath) ? $uriPath : '/');
$file = __DIR__ . $uri;

// Bestaande bestanden/mappen (assets, css, pdf, …) gewoon serveren.
if ($uri !== '/' && is_file($file)) {
    return false;
}

// API zonder .php
if (preg_match('#^/api/(challenge|contact|zegel)/?$#', $uri, $matches) === 1) {
    require __DIR__ . '/api/' . $matches[1] . '.php';
    return true;
}

// Sectie-aliassen en overige paden → homepage (voorkomt “directory not found”).
require __DIR__ . '/index.php';
return true;

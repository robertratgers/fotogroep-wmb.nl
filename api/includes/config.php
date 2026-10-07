<?php
declare(strict_types=1);

/**
 * Fotogroep Windmolenbroek — contactconfiguratie.
 */

const WMB_CONTACT_EMAIL = 'fotogroepwmb@gmail.com';
const WMB_MAIL_FROM_NAME = 'Fotogroep WMB website';
const WMB_CONTACT_NAME = 'Fotogroep Windmolenbroek';

/** Basispad van de site (leeg op domeinroot, anders bijv. /subdir). */
function wmb_base_path(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $dir = dirname($script);

    // API-scripts hangen onder /api — basis is één niveau hoger.
    if (str_ends_with($dir, '/api')) {
        $dir = dirname($dir);
    }

    // Built-in server via router.php
    if (str_ends_with($script, '/router.php')) {
        $dir = dirname($script);
    }

    if ($dir === '/' || $dir === '.' || $dir === '\\') {
        return '';
    }

    return rtrim($dir, '/');
}

/** Root-relatieve URL, werkt ook vanaf schone sectiepaden als /contact. */
function wmb_url(string $path = ''): string
{
    $base = wmb_base_path();
    $path = ltrim($path, '/');

    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }

    return ($base === '' ? '' : $base) . '/' . $path;
}

function wmb_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function wmb_mail_host(): string
{
    $host = preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['SERVER_NAME'] ?? '')) ?: '';

    return $host !== '' ? strtolower($host) : 'localhost';
}

/** From-adres van de website (nooit gelijk aan WMB_CONTACT_EMAIL). */
function wmb_mail_from(): string
{
    return 'noreply@' . wmb_mail_host();
}

function wmb_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function wmb_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

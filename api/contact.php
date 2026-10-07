<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/zegel.php';
require_once __DIR__ . '/includes/smtp.php';

function wmb_contact_line(string $value): string
{
    return trim(str_replace(["\r", "\n", "\0"], ' ', $value));
}

function wmb_contact_header_word(string $value): string
{
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function wmb_contact_store_backup(string $name, string $email, string $phone, string $subject, string $message, string $intent): void
{
    $dir = __DIR__ . '/berichten';
    if (!is_dir($dir) && !@mkdir($dir, 0750, true)) {
        return;
    }
    if (!is_writable($dir)) {
        return;
    }

    $body = "Bericht via Fotogroep WMB\n"
        . 'Datum: ' . date('d-m-Y H:i:s') . "\n"
        . 'Type: ' . $intent . "\n"
        . 'Naam: ' . $name . "\n"
        . 'E-mail: ' . $email . "\n"
        . 'Telefoon: ' . ($phone !== '' ? $phone : '—') . "\n"
        . 'Onderwerp: ' . $subject . "\n\n"
        . $message . "\n";

    @file_put_contents($dir . '/' . date('Y-m-d_His') . '_' . bin2hex(random_bytes(3)) . '.txt', $body);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    wmb_json(['ok' => false, 'status' => 'method', 'message' => 'Alleen POST is toegestaan.'], 405);
}

wmb_session();

$name = wmb_contact_line((string) ($_POST['name'] ?? ''));
$email = wmb_contact_line((string) ($_POST['email'] ?? ''));
$phone = wmb_contact_line((string) ($_POST['phone'] ?? ''));
$subject = wmb_contact_line((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$intent = wmb_contact_line((string) ($_POST['intent'] ?? 'vraag'));
$zegel = (string) ($_POST['zegel'] ?? '');

if (!in_array($intent, ['kennismaking', 'vraag'], true)) {
    $intent = 'vraag';
}

// Honeypot: bots vullen dit; echte bezoekers niet.
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    wmb_json(['ok' => true, 'status' => 'sent', 'message' => 'Bedankt! Je bericht is verstuurd.']);
}

if (!wmb_zegel_verify($zegel)) {
    wmb_zegel_issue(true);
    wmb_json([
        'ok' => false,
        'status' => 'zegel',
        'message' => 'Dat was niet het goede zegel. Klik het gouden zegel met de letters WMB en probeer het opnieuw.',
        'seals' => wmb_zegel_choices(),
        'letters' => WMB_ZEGEL_LETTERS,
        'hint' => 'Klik het gouden zegel met de letters WMB.',
    ], 400);
}

$phoneValid = $phone === '' || (strlen($phone) <= 40 && preg_match('/^[0-9+\s()\-\/]{7,40}$/', $phone) === 1);

$valid = $name !== ''
    && $subject !== ''
    && $message !== ''
    && $phoneValid
    && strlen($name) <= 400
    && strlen($subject) <= 600
    && strlen($message) <= 20000
    && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;

if (!$valid) {
    wmb_zegel_issue(true);
    wmb_json([
        'ok' => false,
        'status' => 'error',
        'message' => 'Het bericht kon niet worden verstuurd. Controleer of alle velden goed zijn ingevuld.',
        'seals' => wmb_zegel_choices(),
        'letters' => WMB_ZEGEL_LETTERS,
        'hint' => 'Klik het gouden zegel met de letters WMB.',
    ], 400);
}

wmb_contact_store_backup($name, $email, $phone, $subject, $message, $intent);

$mailFrom = wmb_mail_from();
$host = wmb_mail_host();
$prefix = $intent === 'kennismaking' ? '[FGWMB kennismaking] ' : '[FGWMB vraag] ';
$mailSubject = wmb_contact_header_word($prefix . $subject);
$plainBody =
    "Contactformulier Fotogroep Windmolenbroek\n"
    . 'Voor: ' . WMB_CONTACT_NAME . "\n"
    . 'Type: ' . $intent . "\n\n"
    . "Naam: {$name}\n"
    . "E-mail: {$email}\n"
    . 'Telefoon: ' . ($phone !== '' ? $phone : '—') . "\n"
    . "Onderwerp: {$subject}\n\n"
    . "{$message}\n";
$mailBody = chunk_split(base64_encode($plainBody));

$replyTo = $name !== ''
    ? wmb_contact_header_word($name) . ' <' . $email . '>'
    : $email;

$headerLines = [
    'Date: ' . date(DATE_RFC2822),
    'From: ' . wmb_contact_header_word(WMB_MAIL_FROM_NAME) . ' <' . $mailFrom . '>',
    'Reply-To: ' . $replyTo,
    'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $host . '>',
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: base64',
];

$errors = [];
$sent = false;
$smtpMessage = implode("\r\n", array_merge(
    ['To: <' . WMB_CONTACT_EMAIL . '>', 'Subject: ' . $mailSubject],
    $headerLines
)) . "\r\n\r\n" . $mailBody;

foreach (wmb_smtp_hosts(WMB_CONTACT_EMAIL) as $smtpHost) {
    $smtpError = '';
    if (wmb_smtp_send($smtpHost, 25, $mailFrom, WMB_CONTACT_EMAIL, $smtpMessage, $smtpError)) {
        $sent = true;
        break;
    }
    $errors[] = $smtpError;
}

if (!$sent) {
    $headers = implode("\r\n", $headerLines);
    $sent = @mail(WMB_CONTACT_EMAIL, $mailSubject, $mailBody, $headers, '-f' . $mailFrom)
        || @mail(WMB_CONTACT_EMAIL, $mailSubject, $mailBody, $headers);
    if (!$sent) {
        $errors[] = 'mail(): ' . (error_get_last()['message'] ?? 'geen PHP-melding');
    }
}

if (!$sent) {
    error_log('[fotogroep-wmb contact] versturen mislukt: ' . implode(' | ', $errors));
    wmb_zegel_issue(true);
    wmb_json([
        'ok' => false,
        'status' => 'mail',
        'message' => 'Er ging iets mis bij het versturen. Probeer het later nog eens via het contactformulier.',
        'seals' => wmb_zegel_choices(),
        'letters' => WMB_ZEGEL_LETTERS,
        'hint' => 'Klik het gouden zegel met de letters WMB.',
    ], 500);
}

wmb_json([
    'ok' => true,
    'status' => 'sent',
    'message' => 'Bedankt! Je bericht is verstuurd naar Fotogroep Windmolenbroek. Wij reageren zo snel mogelijk.',
]);

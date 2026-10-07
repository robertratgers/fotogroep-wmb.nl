<?php
declare(strict_types=1);

/**
 * Zegel-captcha voor Fotogroep WMB: klik het gouden zegel met WMB.
 * Gebaseerd op de Art by Renee / Van Zwol-stempelcaptcha.
 */

const WMB_ZEGEL_TTL = 900;
const WMB_ZEGEL_COUNT = 4;
const WMB_ZEGEL_LETTERS = 'WMB';

/** @return array<string, array{light: string, mid: string, dark: string, ink: string, rim: string}> */
function wmb_zegel_palettes(): array
{
    return [
        'goud' => ['light' => '#fbe7a1', 'mid' => '#d4a52c', 'dark' => '#7a5410', 'ink' => '#4a3208', 'rim' => '#fff4cc'],
        'zilver' => ['light' => '#f4f6f8', 'mid' => '#aab3bd', 'dark' => '#56606b', 'ink' => '#2c333a', 'rim' => '#ffffff'],
        'koper' => ['light' => '#f3c1a0', 'mid' => '#b8683a', 'dark' => '#5e2c12', 'ink' => '#3a1a08', 'rim' => '#ffe3d0'],
        'rood' => ['light' => '#e7777a', 'mid' => '#a3202a', 'dark' => '#520c12', 'ink' => '#2e0508', 'rim' => '#ffd6d8'],
    ];
}

/** Maakt of hergebruikt een set zegels. $force=true na een poging of fout. */
function wmb_zegel_issue(bool $force = false): void
{
    wmb_session();
    $current = $_SESSION['wmb_zegel'] ?? null;
    if (!$force && is_array($current) && (int) ($current['expires'] ?? 0) > time()) {
        return;
    }

    $decoys = [
        ['color' => 'zilver', 'letters' => WMB_ZEGEL_LETTERS],
        ['color' => 'koper', 'letters' => WMB_ZEGEL_LETTERS],
        ['color' => 'rood', 'letters' => WMB_ZEGEL_LETTERS],
        ['color' => 'goud', 'letters' => 'FGW'],
        ['color' => 'goud', 'letters' => 'MWB'],
        ['color' => 'goud', 'letters' => 'WBM'],
        ['color' => 'goud', 'letters' => 'BMW'],
    ];
    shuffle($decoys);
    $picked = array_slice($decoys, 0, WMB_ZEGEL_COUNT - 1);

    $hasOtherColor = false;
    $hasOtherLetters = false;
    foreach ($picked as $seal) {
        $hasOtherColor = $hasOtherColor || $seal['color'] !== 'goud';
        $hasOtherLetters = $hasOtherLetters || $seal['letters'] !== WMB_ZEGEL_LETTERS;
    }
    if (!$hasOtherColor) {
        $picked[0] = ['color' => 'zilver', 'letters' => WMB_ZEGEL_LETTERS];
    } elseif (!$hasOtherLetters) {
        $picked[0] = ['color' => 'goud', 'letters' => 'FGW'];
    }

    $seals = $picked;
    $seals[] = ['color' => 'goud', 'letters' => WMB_ZEGEL_LETTERS, 'answer' => true];
    shuffle($seals);

    $answer = '';
    foreach ($seals as $i => $seal) {
        $token = bin2hex(random_bytes(8));
        $seals[$i]['token'] = $token;
        $seals[$i]['rotate'] = random_int(-18, 18);
        $seals[$i]['seed'] = random_int(1, 999);
        if (!empty($seal['answer'])) {
            $answer = $token;
        }
        unset($seals[$i]['answer']);
    }

    $_SESSION['wmb_zegel'] = [
        'seals' => $seals,
        'answer' => $answer,
        'expires' => time() + WMB_ZEGEL_TTL,
        'nonce' => bin2hex(random_bytes(6)),
    ];
}

/** @return list<array{token: string, src: string}> */
function wmb_zegel_choices(): array
{
    wmb_zegel_issue();
    $state = $_SESSION['wmb_zegel'];
    $choices = [];
    foreach ($state['seals'] as $i => $seal) {
        $choices[] = [
            'token' => (string) $seal['token'],
            'src' => wmb_url('api/zegel') . '?i=' . $i . '&v=' . rawurlencode((string) $state['nonce']),
        ];
    }

    return $choices;
}

/** Eenmalige controle: de set wordt na elke poging vervangen. */
function wmb_zegel_verify(string $token): bool
{
    wmb_session();
    $state = $_SESSION['wmb_zegel'] ?? null;
    unset($_SESSION['wmb_zegel']);

    if (!is_array($state) || (int) ($state['expires'] ?? 0) < time()) {
        return false;
    }

    $answer = (string) ($state['answer'] ?? '');

    return $answer !== '' && $token !== '' && hash_equals($answer, $token);
}

function wmb_zegel_f(float $n): string
{
    return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.') ?: '0';
}

function wmb_zegel_svg(int $index): string
{
    wmb_session();
    $seal = $_SESSION['wmb_zegel']['seals'][$index] ?? null;
    if (!is_array($seal)) {
        $seal = ['color' => 'zilver', 'letters' => '??', 'rotate' => 0, 'seed' => 1];
    }

    $palettes = wmb_zegel_palettes();
    $p = $palettes[$seal['color']] ?? $palettes['zilver'];
    $letters = (string) $seal['letters'];
    $dot = "\u{00B7}";
    $cx = 120.0;
    $cy = 120.0;

    $parts = [];
    $parts[] = '<?xml version="1.0" encoding="UTF-8"?>';
    $parts[] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 240 240" width="240" height="240">';
    $parts[] = '<defs>';
    $parts[] = '<radialGradient id="m" cx="36%" cy="30%" r="75%">';
    $parts[] = '<stop offset="0%" stop-color="' . $p['light'] . '"/>';
    $parts[] = '<stop offset="55%" stop-color="' . $p['mid'] . '"/>';
    $parts[] = '<stop offset="100%" stop-color="' . $p['dark'] . '"/>';
    $parts[] = '</radialGradient>';
    $parts[] = '<filter id="g" x="-10%" y="-10%" width="120%" height="120%">';
    $parts[] = '<feTurbulence type="fractalNoise" baseFrequency="0.85" numOctaves="2" seed="' . (int) $seal['seed'] . '"/>';
    $parts[] = '<feColorMatrix type="matrix" values="0 0 0 0 0.2  0 0 0 0 0.15  0 0 0 0 0.05  0 0 0 0.22 0"/>';
    $parts[] = '<feComposite in2="SourceGraphic" operator="in"/>';
    $parts[] = '</filter>';
    $parts[] = '<path id="r" d="M 120,120 m -84,0 a 84,84 0 1,1 168,0 a 84,84 0 1,1 -168,0"/>';
    $parts[] = '</defs>';
    $parts[] = '<g transform="rotate(' . (int) $seal['rotate'] . ' 120 120)">';

    $scallops = 20;
    for ($i = 0; $i < $scallops; $i++) {
        $a = (2 * M_PI * $i) / $scallops;
        $parts[] = '<circle cx="' . wmb_zegel_f($cx + cos($a) * 100) . '" cy="' . wmb_zegel_f($cy + sin($a) * 100) . '" r="17" fill="' . $p['dark'] . '"/>';
    }

    $parts[] = '<circle cx="120" cy="120" r="102" fill="url(#m)"/>';
    $parts[] = '<circle cx="120" cy="120" r="102" fill="#000" filter="url(#g)"/>';
    $parts[] = '<circle cx="120" cy="120" r="94" fill="none" stroke="' . $p['rim'] . '" stroke-width="3" stroke-opacity="0.8"/>';
    $parts[] = '<circle cx="120" cy="120" r="72" fill="none" stroke="' . $p['ink'] . '" stroke-width="1.6" stroke-opacity="0.7"/>';
    $parts[] = '<text fill="' . $p['ink'] . '" font-family="Georgia, \'Times New Roman\', serif" font-size="11" font-weight="700" letter-spacing="2">';
    $parts[] = '<textPath xlink:href="#r" href="#r">FOTOGROEP WINDMOLENBROEK ' . $dot . ' FGWMB ' . $dot . ' FOTOGROEP WINDMOLENBROEK ' . $dot . ' FGWMB ' . $dot . '</textPath>';
    $parts[] = '</text>';
    // Eenvoudig windmolenmotief.
    $parts[] = '<path d="M120 78 L128 108 L112 108 Z" fill="' . $p['ink'] . '" fill-opacity="0.85"/>';
    $parts[] = '<path d="M120 78 L108 98 L92 88 Z" fill="' . $p['ink'] . '" fill-opacity="0.7"/>';
    $parts[] = '<path d="M120 78 L132 98 L148 88 Z" fill="' . $p['ink'] . '" fill-opacity="0.7"/>';
    $parts[] = '<line x1="120" y1="108" x2="120" y2="122" stroke="' . $p['ink'] . '" stroke-width="3" stroke-linecap="round" stroke-opacity="0.85"/>';

    $first = htmlspecialchars(substr($letters, 0, 1), ENT_XML1, 'UTF-8');
    $rest = htmlspecialchars(substr($letters, 1), ENT_XML1, 'UTF-8');
    $fontSize = strlen($letters) > 2 ? '48' : '60';
    $parts[] = '<text x="121.5" y="164.5" text-anchor="middle" font-family="Georgia, \'Times New Roman\', serif" font-size="' . $fontSize . '" font-weight="700" fill="' . $p['rim'] . '" fill-opacity="0.55">'
        . '<tspan font-style="italic">' . $first . '</tspan>' . $rest . '</text>';
    $parts[] = '<text x="120" y="163" text-anchor="middle" font-family="Georgia, \'Times New Roman\', serif" font-size="' . $fontSize . '" font-weight="700" fill="' . $p['ink'] . '">'
        . '<tspan font-style="italic">' . $first . '</tspan>' . $rest . '</text>';

    for ($n = 0; $n < 3; $n++) {
        $x1 = 60 + random_int(0, 40);
        $y1 = 70 + random_int(0, 100);
        $x2 = 140 + random_int(0, 40);
        $y2 = 70 + random_int(0, 100);
        $qx = 80 + random_int(0, 80);
        $qy = 60 + random_int(0, 120);
        $parts[] = '<path d="M ' . $x1 . ' ' . $y1 . ' Q ' . $qx . ' ' . $qy . ' ' . $x2 . ' ' . $y2 . '" fill="none" stroke="' . $p['ink'] . '" stroke-width="1" opacity="0.3"/>';
    }

    $parts[] = '</g>';
    $parts[] = '</svg>';

    return implode('', $parts);
}

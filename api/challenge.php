<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/zegel.php';

wmb_zegel_issue(isset($_GET['refresh']));
$choices = wmb_zegel_choices();

wmb_json([
    'ok' => true,
    'letters' => WMB_ZEGEL_LETTERS,
    'hint' => 'Klik het gouden zegel met de letters WMB.',
    'seals' => $choices,
]);

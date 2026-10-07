<?php

declare(strict_types=1);

require_once __DIR__ . '/api/includes/config.php';
require_once __DIR__ . '/includes/agenda.php';

header('Content-Type: text/html; charset=UTF-8');

const WMB_LEDENLOGIN_URL = 'https://fotogroepwmb.duckdns.org/login?next=%2F';
const WMB_BRAND_BLUE = '#143883';

const ASSET_URLS = [
    'logo' => 'assets/logo-fgwmb.webp',
    'cover_1' => 'assets/cover/cover-landschap.webp',
    'cover_2' => 'assets/cover/cover-sfeer.webp',
    'portrait_august' => 'assets/portfolio/august-grouw.webp',
    'portrait_ed' => 'assets/portfolio/ed-koekebacker.webp',
    'portrait_edwin' => 'assets/portfolio/edwin-stunnenberg.webp',
    'portrait_ellis' => 'assets/portfolio/ellis.webp',
    'portrait_harrie' => 'assets/portfolio/harrie-perik.webp',
    'portrait_herwin' => 'assets/portfolio/herwin-kleinjan.webp',
    'portrait_jan' => 'assets/portfolio/jan-nijeboer.webp',
    'portrait_rene' => 'assets/portfolio/rene-mondeel.webp',
    'portrait_roald' => 'assets/portfolio/roald-rakers.webp',
    'portrait_robert' => 'assets/portfolio/robert-ratgers.webp',
    'portrait_toon' => 'assets/portfolio/toon-tijink.webp',
    'portrait_wouter' => 'assets/portfolio/wouter-nijeboer.webp',
    'theme' => 'assets/themes/panning.webp',
    'privacy_pdf' => 'assets/Privacyreglement-Fotogroep-Windmolenbroek.pdf',
];

$navItems = [
    ['label' => 'Home', 'path' => '', 'section' => 'home'],
    ['label' => 'Portfolio’s', 'path' => 'portfolios', 'section' => 'portfolios'],
    ['label' => 'Thema’s', 'path' => 'themes', 'section' => 'themes'],
    ['label' => 'Agenda', 'path' => 'agenda', 'section' => 'agenda'],
    ['label' => 'Over FGWMB', 'path' => 'over-fgwmb', 'section' => 'over-fgwmb'],
    ['label' => 'Nieuws', 'path' => 'nieuws', 'section' => 'nieuws'],
    ['label' => 'Contact', 'path' => 'contact', 'section' => 'contact'],
];

$agenda = wmb_load_agenda(__DIR__ . '/pdf/fotogroep-agenda.csv', '2026-10-07');

$news = [
    [
        'date' => '2026-10-07',
        'date_label' => '7 oktober 2026',
        'eyebrow' => 'Clubnieuws',
        'title' => 'Agenda rest van 2026 bijgewerkt',
        'lead' => 'Vanwege enkele wijzigingen ziet het programma voor de rest van dit jaar er zo uit:',
        'items' => [
            ['when' => '19 okt', 'text' => 'Bespreking themafoto’s <em>Van binnen naar buiten of omgekeerd</em>, plus introductie van het thema <em>Herfst</em>.'],
            ['when' => '24 okt', 'text' => 'Fototrip Amsterdamse Waterleidingduinen. Aanmelden bij Jan vóór 20 oktober.'],
            ['when' => '25 okt', 'text' => 'Fototrip Lelystad (Nederlandse Mini clubdag). Aanmelden bij Roald vóór 20 oktober.'],
            ['when' => '2 nov', 'text' => 'Bespreking themafoto’s <em>Reflectie</em> en introductie <em>Ochtendnevel en mist</em> door Roald.'],
            ['when' => '16 nov', 'text' => 'Doe-avond binnen: High key / Low key.'],
            ['when' => '30 nov', 'text' => 'Bespreking <em>Herfst</em> + <em>Ochtendnevel en mist</em> (en eventueel vrijwerk), plus introductie <em>Spel/spelen/spelletje</em> door Ed.'],
            ['when' => '14 dec', 'text' => 'Bespreking <em>Spel/spelen/spelletje</em> en resultaten High key / Low key; eventueel introductie van het eerste thema 2027 (n.t.b.).'],
        ],
        'footer' => 'Het actuele overzicht staat ook onder Agenda.',
        'link' => ['label' => 'Bekijk de agenda', 'section' => 'agenda', 'url' => 'agenda'],
        'featured' => true,
    ],
    [
        'date' => '2026-10-07',
        'date_label' => '7 oktober 2026',
        'eyebrow' => 'Lezing',
        'title' => 'Landschapsfotografie met Guido van de Water',
        'lead' => 'De afdeling Twente van de Fotobond organiseert op 8 oktober van 20:00 tot 22:00 uur een lezing over landschapsfotografie in het Kulturhus in Borne, verzorgd door Guido van de Water.',
        'items' => [
            ['when' => 'Prijs', 'text' => 'Gratis voor leden van de Fotobond; € 7,50 voor niet-leden.'],
            ['when' => 'Aanmelden', 'text' => 'Via de website van Fotobond afdeling Twente.'],
        ],
        'footer' => '',
        'image' => ASSET_URLS['cover_1'],
        'image_caption' => 'Landschap uit de clubcollectie',
        'meta' => [
            '8 oktober 2026 · 20:00–22:00',
            'Kulturhus Borne',
            'Gratis voor Fotobond-leden · € 7,50 voor niet-leden',
        ],
        'link' => [
            'label' => 'Naar Fotobond afdeling Twente',
            'section' => '',
            'url' => 'https://www.fotobondafdelingtwente.nl',
            'external' => true,
        ],
        'featured' => false,
    ],
];

$themes = [
    [
        'title' => 'Panning',
        'subtitle' => 'Beweging en snelheid',
        'period' => 'Najaar 2026',
        'description' => 'Meebewegen met je onderwerp. De achtergrond laten vervagen. Ontdek hoe je snelheid voelbaar maakt in één beeld.',
        'credit' => 'Fotografie: Toon Tijink',
        'image' => ASSET_URLS['theme'],
    ],
];

$photographers = [
    ['first' => 'August', 'last' => 'Grouw', 'image' => ASSET_URLS['portrait_august']],
    ['first' => 'Ed', 'last' => 'Koekebacker', 'image' => ASSET_URLS['portrait_ed']],
    ['first' => 'Edwin', 'last' => 'Stunnenberg', 'image' => ASSET_URLS['portrait_edwin']],
    ['first' => 'Ellis', 'last' => 'Nijeboer', 'image' => ASSET_URLS['portrait_ellis']],
    ['first' => 'Harrie', 'last' => 'Perik', 'image' => ASSET_URLS['portrait_harrie']],
    ['first' => 'Herwin', 'last' => 'Kleinjan', 'image' => ASSET_URLS['portrait_herwin']],
    ['first' => 'Jan', 'last' => 'Nijeboer', 'image' => ASSET_URLS['portrait_jan']],
    ['first' => 'René', 'last' => 'Mondeel', 'image' => ASSET_URLS['portrait_rene']],
    ['first' => 'Roald', 'last' => 'Rakers', 'image' => ASSET_URLS['portrait_roald']],
    ['first' => 'Robert', 'last' => 'Ratgers', 'image' => ASSET_URLS['portrait_robert']],
    ['first' => 'Toon', 'last' => 'Tijink', 'image' => ASSET_URLS['portrait_toon']],
    ['first' => 'Wouter', 'last' => 'Nijeboer', 'image' => ASSET_URLS['portrait_wouter']],
];

$featuredGallery = [
    ['key' => 'ellis', 'name' => 'Ellis Nijeboer', 'image' => ASSET_URLS['portrait_ellis'], 'size' => 'large'],
    ['key' => 'edwin', 'name' => 'Edwin Stunnenberg', 'image' => ASSET_URLS['portrait_edwin'], 'size' => 'mid'],
    ['key' => 'august', 'name' => 'August Grouw', 'image' => ASSET_URLS['portrait_august'], 'size' => 'tall'],
];

$board = [
    ['role' => 'Voorzitter', 'name' => 'Ed Koekebacker'],
    ['role' => 'Secretaris', 'name' => 'Jan Nijeboer'],
    ['role' => 'Penningmeester', 'name' => 'Herwin Kleinjan'],
];

$links = [
    ['label' => 'Fotobond', 'url' => 'https://fotobond.nl'],
    ['label' => 'Fotobond afdeling Twente', 'url' => 'https://www.fotobondafdelingtwente.nl'],
    ['label' => 'Photofacts', 'url' => 'https://www.photofacts.nl/fotografie/'],
    ['label' => 'Zoom.nl cursussen', 'url' => 'http://zoom.nl/soort/cursussen/index.html'],
    ['label' => 'Digitale fotografietips', 'url' => 'http://www.digitalefotografietips.nl'],
];

$socialLinks = [
    ['label' => 'Facebook', 'url' => 'https://www.facebook.com/groups/199246356810739/', 'note' => 'Groep'],
    ['label' => 'Instagram', 'url' => 'https://www.instagram.com/wmbfotogroep/', 'note' => 'Actueel'],
    ['label' => 'Instagram (archief)', 'url' => 'https://www.instagram.com/fotogroepwmb/', 'note' => 'Ouder account'],
    ['label' => 'Flickr', 'url' => 'https://www.flickr.com/groups/14877191@N21/', 'note' => 'Fotogroep'],
];

$clubFacts = [
    ['value' => '2008', 'label' => 'Het begin van onze fotogroep'],
    ['value' => '25', 'label' => 'Maximaal aantal leden'],
    ['value' => '€ 50', 'label' => 'Contributie per jaar · 2026'],
    ['value' => 'Om de week', 'label' => 'Een maandagavond vol fotografie'],
];

$clubValues = [
    [
        'icon' => 'scan-eye',
        'title' => 'Leren door te kijken',
        'text' => 'Samen foto’s bespreken levert nieuwe inzichten op. Niet alleen in techniek, maar ook in wat je wilt vertellen.',
    ],
    [
        'icon' => 'camera',
        'title' => 'Jouw blik, niet je apparatuur',
        'text' => 'Iedereen met een zekere basiskennis is welkom. Een dure camera is niet nodig; je plezier in fotografie telt.',
    ],
    [
        'icon' => 'users',
        'title' => 'Een kleine groep, korte lijnen',
        'text' => 'Met maximaal 25 leden is er ruimte voor persoonlijke aandacht, vragen en het uitwisselen van ideeën.',
    ],
];

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function asset(string $key): string
{
    return e(wmb_url(ASSET_URLS[$key] ?? ''));
}

function photographer_name(array $photographer): string
{
    $name = trim($photographer['first'] . ' ' . $photographer['last']);

    return $name !== '' ? $name : $photographer['first'];
}

function nav_href(array $item): string
{
    return wmb_url((string) ($item['path'] ?? ''));
}

function icon(string $name, int $size = 20): string
{
    $src = e(wmb_url('assets/icons/' . $name . '.svg'));

    return '<img class="icon" src="' . $src . '" alt="" width="' . $size . '" height="' . $size . '" aria-hidden="true">';
}

$featuredNews = null;
$sideNews = null;
foreach ($news as $article) {
    if (!empty($article['featured']) && $featuredNews === null) {
        $featuredNews = $article;
    } else {
        $sideNews = $article;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fotogroep Windmolenbroek (FGWMB) is een fotoclub in Almelo. Deel kennis en passie voor fotografie in een gezellige sfeer.">
    <meta name="theme-color" content="<?= e(WMB_BRAND_BLUE) ?>">
    <meta property="og:title" content="Fotogroep Windmolenbroek — FGWMB">
    <meta property="og:description" content="Fotoclub in Almelo: van elkaar leren en steeds betere foto’s maken.">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="nl_NL">
    <title>Fotogroep Windmolenbroek — FGWMB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(wmb_url('styles.css')) ?>">
    <script>
        window.WMB_BASE = <?= json_encode(wmb_base_path(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
</head>
<body>
    <a class="skip-link" href="#main-content">Ga naar inhoud</a>
    <header class="site-header" data-site-header>
        <a class="brand" href="<?= e(wmb_url('')) ?>" aria-label="Fotogroep Windmolenbroek home">
            <img src="<?= asset('logo') ?>" alt="" width="62" height="62">
            <span class="brand-text">
                <span class="brand-name">Fotogroep</span>
                <span class="brand-place">Windmolenbroek</span>
            </span>
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-navigation">
            <span></span><span></span><span></span>
            <span class="sr-only">Menu openen</span>
        </button>
        <nav class="main-nav" id="main-navigation" aria-label="Hoofdnavigatie">
            <?php foreach ($navItems as $index => $item): ?>
                <a href="<?= e(nav_href($item)) ?>" data-section="<?= e($item['section']) ?>"<?= $index === 0 ? ' class="is-active"' : '' ?>><?= e($item['label']) ?></a>
            <?php endforeach; ?>
            <a class="member-link" href="<?= e(WMB_LEDENLOGIN_URL) ?>" target="_blank" rel="noopener noreferrer">
                <?= icon('lock', 16) ?>
                <span>Ledenlogin</span>
            </a>
        </nav>
    </header>

    <main id="main-content">
        <section class="hero" id="home">
            <div class="shell hero-intro">
                <div class="hero-copy">
                    <p class="section-label">Hobbyfotograaf? Fotogroep WMB.</p>
                    <h1>Samen kijken.<br>Meer zien.</h1>
                    <p class="hero-promise">Deel je kennis en passie voor fotografie met anderen!</p>
                    <p class="hero-lead">Een eigen blik. Nieuwe inspiratie. En mensen die net zo graag fotograferen als jij. Welkom bij Fotogroep Windmolenbroek in Almelo.</p>
                    <div class="hero-actions">
                        <a class="button" href="<?= e(wmb_url('over-fgwmb')) ?>" data-section="over-fgwmb">
                            Ontdek FGWMB
                            <?= icon('arrow-up-right', 18) ?>
                        </a>
                        <a class="text-link" href="<?= e(wmb_url('portfolios')) ?>" data-section="portfolios">
                            Bekijk de foto’s
                            <?= icon('arrow-right', 20) ?>
                        </a>
                    </div>
                    <p class="hero-location">
                        <?= icon('map-pin', 16) ?>
                        <span>Almelo · Samen fotograferen sinds 2008</span>
                    </p>
                </div>
                <div class="hero-visual" data-protect-images>
                    <div class="hero-main">
                        <img src="<?= asset('cover_1') ?>" alt="Vogel op hekpaal in mistig landschap" fetchpriority="high" draggable="false">
                        <div class="hero-badge" aria-hidden="true">
                            <strong>FGWMB</strong>
                            <span>ALMELO</span>
                        </div>
                    </div>
                    <p class="hero-credit">Uit de clubcollectie · FGWMB</p>
                    <figure class="hero-detail">
                        <img src="<?= asset('cover_2') ?>" alt="Fotografie in Almelo en omstreken" loading="lazy" draggable="false">
                        <figcaption>Stilgezet. Niet stilgestaan.</figcaption>
                    </figure>
                </div>
            </div>
            <div class="shell club-facts">
                <?php foreach ($clubFacts as $fact): ?>
                    <div class="club-fact">
                        <strong><?= e($fact['value']) ?></strong>
                        <span><?= e($fact['label']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section portfolios" id="portfolios">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <p class="section-label">De blik van onze leden</p>
                        <h2>Eén club. Veel perspectieven.</h2>
                    </div>
                    <a class="text-link" href="#leden-overzicht">
                        Alle portfolio’s
                        <?= icon('arrow-right', 20) ?>
                    </a>
                </div>

                <div class="gallery-grid" data-protect-images>
                    <?php foreach ($featuredGallery as $piece): ?>
                        <article class="gallery-piece gallery-piece--<?= e($piece['size']) ?>">
                            <div class="gallery-image">
                                <img src="<?= e(wmb_url($piece['image'])) ?>" alt="Foto van <?= e($piece['name']) ?>" loading="lazy" draggable="false">
                            </div>
                            <div class="gallery-credit">
                                <span><?= e($piece['name']) ?></span>
                                <?= icon('arrow-up-right', 18) ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="members-overview" id="leden-overzicht">
                    <div class="members-overview-head">
                        <h3>Ontmoet onze fotografen</h3>
                        <p><?= count($photographers) ?> portfolio’s · ieder een eigen verhaal</p>
                    </div>
                    <div class="member-grid" data-protect-images>
                        <?php foreach ($photographers as $photographer): ?>
                            <?php $fullName = photographer_name($photographer); ?>
                            <a class="member-card" href="<?= e(wmb_url('portfolios')) ?>" data-section="portfolios" aria-label="Portfolio van <?= e($fullName) ?>">
                                <img src="<?= e(wmb_url($photographer['image'])) ?>" alt="" loading="lazy" draggable="false">
                                <span class="member-card-copy">
                                    <strong><?= e($fullName) ?></strong>
                                    <em>Bekijk portfolio</em>
                                </span>
                                <?= icon('arrow-up-right', 18) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <p class="muted">Een openbaar portfolio is niet verplicht. Leden kunnen hier maximaal 20 foto’s laten zien.</p>
                </div>
            </div>
        </section>

        <section class="section about-teaser" id="over-fgwmb">
            <div class="shell about-teaser-grid">
                <div class="about-story">
                    <p class="section-label">Fotografie verbindt</p>
                    <h2>Je hoeft niet alles te weten. Alleen nieuwsgierig te zijn.</h2>
                    <p>Sinds begin 2008 brengt FGWMB hobbyfotografen samen. We delen kennis, bekijken elkaars werk en helpen elkaar betere foto’s te maken. In een gezellige, ongedwongen sfeer.</p>
                    <a class="text-link" href="<?= e(wmb_url('over-fgwmb-detail')) ?>" data-section="over-fgwmb-detail">
                        Meer over onze fotogroep
                        <?= icon('arrow-right', 20) ?>
                    </a>
                </div>
                <div class="value-list">
                    <?php foreach ($clubValues as $value): ?>
                        <article class="value-item">
                            <div class="value-icon"><?= icon($value['icon'], 24) ?></div>
                            <div>
                                <h3><?= e($value['title']) ?></h3>
                                <p><?= e($value['text']) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section themes" id="themes">
            <div class="shell">
                <div class="section-head section-head--compact">
                    <p class="section-label">Samen uitproberen</p>
                    <a class="text-link" href="<?= e(wmb_url('themes')) ?>" data-section="themes">
                        Alle thema’s
                        <?= icon('arrow-right', 20) ?>
                    </a>
                </div>
                <?php foreach ($themes as $theme): ?>
                    <div class="theme-feature" data-protect-images>
                        <div class="theme-image">
                            <img src="<?= e(wmb_url($theme['image'])) ?>" alt="<?= e($theme['title']) ?>" loading="lazy" draggable="false">
                        </div>
                        <article class="theme-panel">
                            <p class="theme-season"><?= e($theme['period']) ?></p>
                            <h2><?= e($theme['title']) ?></h2>
                            <p class="theme-subtitle"><?= e($theme['subtitle']) ?></p>
                            <p><?= e($theme['description']) ?></p>
                            <a class="text-link light" href="<?= e(wmb_url('themes')) ?>" data-section="themes">
                                Ontdek het thema
                                <?= icon('arrow-right', 20) ?>
                            </a>
                            <p class="theme-credit"><?= e($theme['credit']) ?></p>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section club-nights" id="clubavonden" aria-labelledby="club-nights-title">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <p class="section-label">Van foto naar gesprek</p>
                        <h2 id="club-nights-title">Tot maandagavond?</h2>
                    </div>
                    <button class="button outline" type="button" data-intent="kennismaking" data-scroll-form>
                        Plan een kennismaking
                        <?= icon('arrow-up-right', 18) ?>
                    </button>
                </div>
                <div class="info-cards">
                    <article class="info-card">
                        <?= icon('clock', 26) ?>
                        <p class="info-label">Wanneer</p>
                        <h3>19:30–22:00</h3>
                        <p>Op maandag in de oneven weken, iedere twee weken.</p>
                        <p class="muted">We komen niet samen tijdens de schoolvakanties.</p>
                    </article>
                    <article class="info-card">
                        <?= icon('map-pin', 26) ?>
                        <p class="info-label">Waar</p>
                        <h3>Wijkcentrum Möll’nwiek</h3>
                        <p>Leemhorst 9<br>7609 LA Almelo</p>
                        <p class="muted">Onze vaste plek om foto’s, kennis en inspiratie te delen.</p>
                    </article>
                    <article class="info-card">
                        <?= icon('camera', 26) ?>
                        <p class="info-label">Kennismaken</p>
                        <h3>Kom vrijblijvend kijken</h3>
                        <p>Bezoek een clubavond en ontdek of onze fotogroep bij je past.</p>
                        <p class="muted">Neem vooraf contact op. Je hoeft nog geen lid te zijn.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section agenda" id="agenda">
            <div class="shell agenda-layout">
                <div>
                    <p class="section-label">Agenda</p>
                    <h2>Op de planning</h2>
                </div>
                <?php if ($agenda === []): ?>
                    <div class="agenda-empty">
                        <div class="agenda-empty-icon"><?= icon('calendar', 28) ?></div>
                        <div>
                            <p class="agenda-empty-title">Momenteel geen aankomende activiteiten in de agenda.</p>
                            <p class="muted">Zodra er nieuwe clubavonden of fototrips gepland zijn, verschijnen ze hier.</p>
                            <a class="text-link" href="<?= e(wmb_url('contact')) ?>" data-section="contact">
                                Vragen over een avond?
                                <?= icon('arrow-right', 20) ?>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="agenda-list">
                        <?php foreach ($agenda as $item): ?>
                            <article class="agenda-item">
                                <time datetime="<?= e($item['iso']) ?>">
                                    <strong><?= e($item['day']) ?></strong>
                                    <span><?= e($item['month']) ?></span>
                                </time>
                                <div class="agenda-content">
                                    <span class="agenda-type"><?= e(strtoupper($item['type'])) ?></span>
                                    <h3><?= e($item['title']) ?></h3>
                                    <?php if (($item['detail'] ?? '') !== ''): ?>
                                        <p class="agenda-detail"><?= e($item['detail']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= e(wmb_url('contact')) ?>" data-section="contact" aria-label="Meer over <?= e($item['title']) ?>">
                                    <?= icon('arrow-up-right', 18) ?>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section about-detail" id="over-fgwmb-detail" aria-labelledby="over-detail-title">
            <div class="shell">
                <div class="section-head stack">
                    <p class="section-label">Wie wij zijn</p>
                    <h2 id="over-detail-title">Over Fotogroep Windmolenbroek</h2>
                    <p class="section-intro">Doel van onze fotoclub is het bevorderen van de fotografie als vrijetijdsbesteding om daardoor, zowel individueel als in clubverband, veel plezier te beleven aan deze gezamenlijke en interessante hobby. Onze clubavonden zijn niet alleen inhoudelijk leerzaam maar ook gezellig en altijd met de doelstelling om keer op keer betere foto’s te maken.</p>
                </div>
                <div class="about-detail-grid">
                    <article>
                        <h3>Naam &amp; bereik</h3>
                        <p>Wij danken onze naam aan de wijk Windmolenbroek, waar wij onze clubavonden houden, maar zijn een fotoclub voor iedereen uit Almelo en omstreken.</p>
                    </article>
                    <article>
                        <h3>Clubavonden</h3>
                        <p>Wij hebben iedere twee weken een clubavond, behalve tijdens de schoolvakanties. In de oneven weken komen we bij elkaar op maandagavond van 19:30 tot 22:00 uur. Locatie: wijkcentrum ‘Möll’nwiek’, <a class="inline-link" href="https://www.openstreetmap.org/search?query=Leemhorst%209%207609%20LA%20Almelo" rel="noopener noreferrer" target="_blank">Leemhorst 9, 7609 LA Almelo</a>.</p>
                    </article>
                    <article>
                        <h3>Wat we doen</h3>
                        <p>We delen kennis tijdens fotobesprekingen, doe-avonden, themapresentaties, lezingen, excursies en exposities. Thema’s worden zo’n zes weken van tevoren geïntroduceerd. Ook vrijwerk is welkom.</p>
                    </article>
                    <article>
                        <h3>Lidmaatschap</h3>
                        <p>FGWMB telt maximaal 25 leden en is geen lid van de Fotobond. De contributie bedraagt EUR 50 per jaar (tarief 2026). Lidmaatschap loopt van 1 januari tot en met 31 december. Opzeggen kan tot 1 december.</p>
                    </article>
                </div>
                <div class="board-block">
                    <p class="section-label">Bestuur</p>
                    <ul class="board-list">
                        <?php foreach ($board as $member): ?>
                            <li>
                                <strong><?= e($member['name']) ?></strong>
                                <span><?= e($member['role']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section news" id="nieuws">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <p class="section-label">Uit de club</p>
                        <h2>Nieuws &amp; inspiratie</h2>
                    </div>
                    <a class="text-link" href="<?= e(wmb_url('nieuws')) ?>" data-section="nieuws">
                        Al het nieuws
                        <?= icon('arrow-right', 20) ?>
                    </a>
                </div>
                <div class="news-layout">
                    <?php if ($featuredNews !== null): ?>
                        <article class="news-feature">
                            <header class="news-meta">
                                <span><?= e($featuredNews['eyebrow']) ?></span>
                                <time datetime="<?= e($featuredNews['date']) ?>"><?= e($featuredNews['date_label']) ?></time>
                            </header>
                            <h3><?= e($featuredNews['title']) ?></h3>
                            <p><?= e($featuredNews['lead']) ?></p>
                            <?php if (!empty($featuredNews['items'])): ?>
                                <ul class="news-agenda">
                                    <?php foreach ($featuredNews['items'] as $item): ?>
                                        <li>
                                            <span><?= e($item['when']) ?></span>
                                            <div><?= $item['text'] ?></div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <?php if (($featuredNews['footer'] ?? '') !== ''): ?>
                                <p class="muted"><?= e($featuredNews['footer']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($featuredNews['link'])): ?>
                                <a class="text-link" href="<?= e(wmb_url($featuredNews['link']['url'])) ?>" data-section="<?= e($featuredNews['link']['section']) ?>">
                                    <?= e($featuredNews['link']['label']) ?>
                                    <?= icon('arrow-right', 20) ?>
                                </a>
                            <?php endif; ?>
                        </article>
                    <?php endif; ?>

                    <?php if ($sideNews !== null): ?>
                        <article class="news-side" data-protect-images>
                            <?php if (!empty($sideNews['image'])): ?>
                                <div class="news-side-image">
                                    <img src="<?= e(wmb_url($sideNews['image'])) ?>" alt="" loading="lazy" draggable="false">
                                </div>
                            <?php endif; ?>
                            <div class="news-side-body">
                                <?php if (!empty($sideNews['image_caption'])): ?>
                                    <p class="news-caption"><?= e($sideNews['image_caption']) ?></p>
                                <?php endif; ?>
                                <header class="news-meta">
                                    <span><?= e($sideNews['eyebrow']) ?></span>
                                    <time datetime="<?= e($sideNews['date']) ?>"><?= e($sideNews['date_label']) ?></time>
                                </header>
                                <h3><?= e($sideNews['title']) ?></h3>
                                <p><?= e($sideNews['lead']) ?></p>
                                <?php if (!empty($sideNews['meta'])): ?>
                                    <div class="news-side-meta">
                                        <?php foreach ($sideNews['meta'] as $line): ?>
                                            <p><?= e($line) ?></p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($sideNews['link'])): ?>
                                    <?php if (!empty($sideNews['link']['external'])): ?>
                                        <a class="text-link" href="<?= e($sideNews['link']['url']) ?>" rel="noopener noreferrer" target="_blank">
                                            <?= e($sideNews['link']['label']) ?>
                                            <?= icon('arrow-up-right', 18) ?>
                                        </a>
                                    <?php else: ?>
                                        <a class="text-link" href="<?= e(wmb_url($sideNews['link']['url'])) ?>" data-section="<?= e($sideNews['link']['section']) ?>">
                                            <?= e($sideNews['link']['label']) ?>
                                            <?= icon('arrow-right', 20) ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="contact" id="contact">
            <div class="shell">
                <p class="section-label">Je bent welkom</p>
                <div class="contact-layout">
                    <aside class="contact-invite">
                        <h2>Een nieuwe blik begint met kennismaken.</h2>
                        <p>Benieuwd naar onze club? Kom vrijblijvend een clubavond bezoeken. Vertel ons wie je bent, dan plannen we samen een kennismaking.</p>
                        <div class="contact-direct">
                            <?= icon('mail', 26) ?>
                            <p>Liever rechtstreeks mailen?</p>
                            <a href="mailto:<?= e(WMB_CONTACT_EMAIL) ?>"><?= e(WMB_CONTACT_EMAIL) ?></a>
                        </div>
                        <div class="contact-dues">
                            <strong>€ 50 per jaar</strong>
                            <p>Contributie 2026 · maximaal 25 leden<br>Een dure camera is niet nodig.</p>
                        </div>
                    </aside>

                    <div class="contact-form-wrap" id="contact-formulier">
                        <div class="contact-tabs" role="tablist" aria-label="Contactkeuze">
                            <button type="button" class="contact-tab is-active" role="tab" aria-selected="true" data-intent="kennismaking" data-contact-tab>Plan een kennismaking</button>
                            <button type="button" class="contact-tab" role="tab" aria-selected="false" data-intent="vraag" data-contact-tab>Stel een vraag</button>
                        </div>
                        <p class="contact-intro" data-contact-intro>Laat een bericht achter. We nemen contact met je op om een bezoek af te spreken.</p>
                        <div id="contact-alert" class="contact-alert" hidden></div>
                        <form id="contact-form" class="contact-form" action="<?= e(wmb_url('api/contact')) ?>" method="post" novalidate>
                            <input type="hidden" name="intent" id="contact-intent" value="kennismaking">
                            <div class="contact-form-grid">
                                <label class="contact-field">
                                    <span>Naam</span>
                                    <input type="text" name="name" autocomplete="name" required maxlength="400" placeholder="Je naam">
                                </label>
                                <label class="contact-field">
                                    <span>E-mailadres</span>
                                    <input type="email" name="email" autocomplete="email" required placeholder="naam@voorbeeld.nl">
                                </label>
                                <label class="contact-field contact-field--full">
                                    <span>Telefoonnummer <em>(optioneel)</em></span>
                                    <input type="tel" name="phone" autocomplete="tel" maxlength="40" inputmode="tel" placeholder="Je telefoonnummer">
                                </label>
                                <label class="contact-field contact-field--full">
                                    <span>Onderwerp</span>
                                    <select name="subject" id="contact-subject" required>
                                        <option value="Kennismaking met Fotogroep Windmolenbroek">Kennismaking op een clubavond</option>
                                        <option value="Algemene vraag">Algemene vraag</option>
                                    </select>
                                </label>
                                <label class="contact-field contact-field--full">
                                    <span>Bericht</span>
                                    <textarea name="message" required maxlength="20000" rows="5" placeholder="Vertel ons iets over jezelf en je interesse in fotografie…"></textarea>
                                </label>
                                <div class="contact-hp" aria-hidden="true">
                                    <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                                </div>
                                <fieldset class="contact-captcha contact-field--full">
                                    <legend>Klik het gouden zegel met de letters WMB</legend>
                                    <p class="contact-captcha-loading" data-captcha-loading hidden>Zegels laden…</p>
                                    <div class="contact-zegels" data-zegels></div>
                                </fieldset>
                                <div class="contact-actions contact-field--full">
                                    <button type="submit" class="button" data-submit>
                                        Bericht versturen
                                        <?= icon('arrow-up-right', 18) ?>
                                    </button>
                                    <p class="contact-privacy">Je gegevens worden alleen gebruikt om te reageren op je bericht. <a href="<?= e(wmb_url('privacy')) ?>" data-section="privacy">Lees onze privacyverklaring</a>.</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="section members" id="ledenlogin">
            <div class="shell narrow">
                <p class="section-label">Leden</p>
                <h2>Ledenlogin</h2>
                <p>Dit deel van de website is bedoeld voor leden en is alleen toegankelijk via userid en wachtwoord. Ieder lid krijgt van de secretaris een uniek userid en een algemeen wachtwoord, dat na de eerste login moet worden aangepast.</p>
                <p>De ledenomgeving wordt nog afgestemd met de tool van Wouter, zodat foto’s (maximaal 20 per lid), clubnieuws, terugkoppelingen van clubavonden en agenda-integratie op één plek samenkomen.</p>
                <a class="button" href="<?= e(WMB_LEDENLOGIN_URL) ?>" target="_blank" rel="noopener noreferrer">
                    Naar de ledenlogin
                    <?= icon('arrow-up-right', 18) ?>
                </a>
                <p class="muted">Vragen over toegang? Neem contact op via de secretaris of het contactformulier.</p>
            </div>
        </section>

        <section class="section links" id="links">
            <div class="shell narrow">
                <p class="section-label">Interessant</p>
                <h2>Websites &amp; links</h2>
                <p class="section-intro">Een selectie nuttige fotografiewebsites. Dit overzicht wordt alleen door de webmaster (of vervanger) bijgewerkt.</p>
                <ul class="link-list">
                    <?php foreach ($links as $link): ?>
                        <li>
                            <a href="<?= e($link['url']) ?>" rel="noopener noreferrer" target="_blank"><?= e($link['label']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <section class="section social" id="sociale-media">
            <div class="shell narrow">
                <p class="section-label">Volg ons</p>
                <h2>Sociale media</h2>
                <ul class="social-list">
                    <?php foreach ($socialLinks as $social): ?>
                        <li>
                            <a href="<?= e($social['url']) ?>" rel="noopener noreferrer" target="_blank">
                                <span><?= e($social['label']) ?></span>
                                <em><?= e($social['note']) ?></em>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <section class="section legal" id="privacy">
            <div class="shell narrow">
                <p class="section-label">Informatie</p>
                <h2>Privacyreglement</h2>
                <?php require __DIR__ . '/includes/privacy-content.php'; ?>
            </div>
        </section>

        <section class="section legal" id="disclaimer">
            <div class="shell narrow">
                <p class="section-label">Informatie</p>
                <h2>Disclaimer &amp; copyright</h2>
                <?php require __DIR__ . '/includes/disclaimer-content.php'; ?>
            </div>
        </section>

        <section class="section legal" id="reglement">
            <div class="shell narrow">
                <p class="section-label">Informatie</p>
                <h2>Huishoudelijk reglement</h2>
                <p>Het huishoudelijk reglement wordt door de webmaster geplaatst (aparte bijlage).</p>
            </div>
        </section>
    </main>

    <button type="button" class="back-to-top" data-back-to-top hidden aria-label="Terug naar boven">↑</button>

    <footer class="site-footer">
        <div class="shell footer-main">
            <div class="footer-brand">
                <div class="brand footer-brand-mark">
                    <img src="<?= asset('logo') ?>" alt="" width="64" height="64">
                    <span class="brand-text">
                        <span class="brand-name">Fotogroep</span>
                        <span class="brand-place">Windmolenbroek</span>
                    </span>
                </div>
                <p>Fotografie delen, bespreken en samen blijven ontdekken in Almelo.</p>
                <a href="mailto:<?= e(WMB_CONTACT_EMAIL) ?>"><?= e(WMB_CONTACT_EMAIL) ?></a>
            </div>
            <div class="footer-column">
                <h2>Ontdekken</h2>
                <a href="<?= e(wmb_url('')) ?>" data-section="home">Home</a>
                <a href="<?= e(wmb_url('portfolios')) ?>" data-section="portfolios">Portfolio’s</a>
                <a href="<?= e(wmb_url('themes')) ?>" data-section="themes">Thema’s</a>
                <a href="<?= e(wmb_url('agenda')) ?>" data-section="agenda">Agenda</a>
                <a href="<?= e(wmb_url('nieuws')) ?>" data-section="nieuws">Nieuws</a>
            </div>
            <div class="footer-column">
                <h2>Bestuur</h2>
                <?php foreach ($board as $member): ?>
                    <p>
                        <strong><?= e($member['name']) ?></strong>
                        <span><?= e($member['role']) ?></span>
                    </p>
                <?php endforeach; ?>
            </div>
            <div class="footer-column">
                <h2>Informatie &amp; contact</h2>
                <a href="<?= e(wmb_url('over-fgwmb')) ?>" data-section="over-fgwmb">Over FGWMB</a>
                <a href="<?= e(wmb_url('privacy')) ?>" data-section="privacy">Privacy</a>
                <a href="<?= e(wmb_url('disclaimer')) ?>" data-section="disclaimer">Disclaimer &amp; copyright</a>
                <a href="<?= e(WMB_LEDENLOGIN_URL) ?>" target="_blank" rel="noopener noreferrer">
                    <?= icon('lock', 15) ?>
                    <span>Ledenlogin</span>
                </a>
                <a href="<?= e(wmb_url('links')) ?>" data-section="links">Fotografiewebsites</a>
            </div>
        </div>
        <div class="shell footer-bottom">
            <span>© <?= date('Y') ?> Fotogroep Windmolenbroek · Almelo</span>
            <span>Foto’s van leden · rechten voorbehouden</span>
        </div>
    </footer>

    <script src="<?= e(wmb_url('assets/site.js')) ?>" defer></script>
</body>
</html>

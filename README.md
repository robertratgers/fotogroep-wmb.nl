# Fotogroep Windmolenbroek (FGWMB)

Responsieve PHP-website voor Fotogroep Windmolenbroek in Almelo.

## Starten (lokaal)

Installeer PHP 8.1 of hoger. Start met de router zodat `.php` niet in de URL hoeft:

```powershell
php -S localhost:8000 router.php
```

Open daarna `http://localhost:8000`.

Schone paden werken ook, bijvoorbeeld:

- `http://localhost:8000/contact`
- `http://localhost:8000/portfolios`
- `http://localhost:8000/api/challenge`

## Bestanden

- `index.php` — publieke pagina
- `router.php` — routing voor de PHP built-in server
- `.htaccess` — schone URL’s op Apache/DreamHost
- `styles.css` — styling
- `assets/portfolio/` — portfoliofoto’s (WebP)
- `assets/cover/` — coverfoto’s (WebP)
- `assets/themes/` — themabeelden (WebP)
- `api/` — contactformulier met honeypot + WMB-zegelcaptcha
- `includes/` — privacy- en disclaimerteksten

Contactmail: `fotogroepwmb@gmail.com`

## Encoding

Alle bronbestanden zijn UTF-8 zonder BOM.

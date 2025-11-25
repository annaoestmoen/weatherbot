# Weatherbot

Weatherbot er en enkel chatbot som gir værinformasjon for ønsket by. Prosjektet har både vanlige brukere og administratorer.

## Funksjoner
- Brukerregistrering og innlogging
- Favorittby for rask tilgang til værdata
- Værdata hentet fra Open-Meteo API
- Klær-anbefaling basert på temperatur, regn/snø og vind
- Admin-dashboard for å se og nullstille feil-chats
- Loggføring av alle chat-meldinger

## Oppsett
1. Installer XAMPP eller tilsvarende PHP/MySQL-miljø.
2. Opprett database `weatherbot` og kjør SQL-scriptet for å lage tabeller:
    - `users`
    - `admins`
    - `chat_logs`
3. Kopier prosjektet til `htdocs` eller web-root.
4. Konfigurer `config/db.php` med dine database-detaljer.
5. Start server og gå til `index.php`.

## Mappestruktur
weatherbot/
├── admin/ # Admin-sider for å se brukere, nullstille låser, slette brukere
│ ├── delete_user.php # Slett bruker
│ ├── download_errors.php # Last ned logg over feilmeldinger
│ ├── index.php # Admin-dashboard
│ ├── logout.php # Logg ut admin
│ ├── reset_lock.php # Nullstill lås på bruker
│ ├── users_overview.php # Oversikt over alle brukere
├── assets/ # CSS, JS og bilder
│ ├── css/
│ │ └── style.css # Hoved-stilark
│ ├── js/
│ └── chat.js # JS for chat-funksjonalitet
├── config/ # Konfigurasjonsfiler
│ ├── config.php # Generelle innstillinger (API, enheter)
│ ├── db.php # Database-tilkobling (PDO)
│ ├── error_handler.php # Custom error/exception handler
├── functions/ # PHP-funksjoner
│ ├── auth.php # Login, logout, admin-/user-sjekk
│ ├── chatfunctions.php # Loggføring av chat
│ ├── validation.php # Validering og sanitizing
│ ├── weather.php # Hent værdata, gi klær-anbefaling
├── user/ # Vanlige bruker-sider
│ ├── chat.php # Chat-side
│ ├── index.php # Hovedside for chat
│ ├── logout.php # Logg ut bruker
│ ├── profile.php # Profilside, favorittby
├── vendor/ # Composer-pakker
├── composer.json # Composer-konfig
├── composer.lock # Composer lock-fil
├── index.php # Innloggingsside
└── register.php # Registreringsside

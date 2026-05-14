# Vamp API — Laravel Backend

RESTful API backend for Vampire League Fantasy Football. Built with Laravel 8, it handles all league management, player rosters, scoring, waivers, draft picks, and NFL schedule data consumed by the [vampire-league](../vampire-league) frontend.

## Tech Stack

- **Laravel 8** (PHP 7.3+ / 8.0+)
- **MySQL** (primary database)
- **Laravel Sanctum** for API token authentication
- **fruitcake/laravel-cors** for CORS handling
- **Laravel Sail** for Docker support

## Prerequisites

- PHP 7.3+ or 8.0+
- Composer
- MySQL
- A local web server (WAMP, XAMPP, Laravel Valet, or Docker via Sail)

## Getting Started

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials, then run migrations:

```bash
php artisan migrate
```

The API will be available at `http://localhost:8080/api` (or whichever host/port your server uses).

## Environment Configuration

Key `.env` values to set for local development:

```
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=root
DB_PASSWORD=
```

CORS is pre-configured to allow requests from `http://localhost:3000` (the React dev server) and `https://vampireleaguefootball.com`.

## API Overview

All routes are prefixed with `/api`. The API exposes 112+ endpoints across 22 controllers.

| Resource | Prefix | Description |
|---|---|---|
| Users | `/users` | Registration, login, profile |
| Leagues | `/leagues` | CRUD, activation |
| League Members | `/league_members` | Membership, team names, FAAB |
| Players | `/players` | Player registration and lookup |
| Rostered Players | `/rostered_players` | Roster assignments |
| Roster Rules | `/roster_rules` | Per-league lineup config |
| Roster Types | `/roster_types` | Regular / Vampire type definitions |
| Scoring Rules | `/scoring_rules` | Per-league scoring config |
| Weekly Stats | `/weekly_stats` | Player weekly performance |
| Yearly Stats | `/yearly_stats` | Aggregated season stats |
| Rostered Player Scores | `/rostered_player_weekly_scores` | Scored output per roster slot |
| Draft Picks | `/draft_picks` | Draft tracking |
| Waiver Claims | `/waiver_claims` | FAAB claim processing |
| Waiver Rules | `/waiver_rules` | Per-league waiver config |
| NFL Schedule | `/nfl_schedules` | NFL game schedule data |
| Fantasy Weeks | `/fantasy_weeks` | Fantasy season week definitions |
| Schedules | `/schedules` | League matchup schedule |
| League Divisions | `/league_divisions` | Division management |
| Logs | `/logs` | Activity audit trail |
| Matchup Page | `/matchups/getMatchupPageData` | Composite matchup endpoint |
| Waiver Page | `/waivers/getWaiverPageData` | Composite waiver endpoint |

## Project Structure

```
app/
├── Http/
│   ├── Controllers/    # 22 controllers
│   └── Middleware/     # CORS, auth, rate limiting
├── Models/             # 19 Eloquent models
└── Providers/
database/
├── migrations/
├── factories/
└── seeders/
routes/
└── api.php             # All API route definitions
```

## Key Models

- **User** / **League** / **LeagueMember** — core identity and membership
- **Player** / **RosteredPlayer** — player pool and roster assignments (Player uses UUID primary key)
- **RosterRule** / **RosterType** — lineup configuration (supports separate Regular and Vampire types)
- **ScoringRule** / **WeeklyStat** / **RosteredPlayerWeeklyScore** — scoring pipeline
- **WaiverClaim** / **WaiverRule** — FAAB waiver system
- **DraftPick** — draft history
- **NFLSchedule** / **FantasyWeek** / **Schedule** — schedule management
- **Log** — per-league activity log

## Rate Limiting

API routes are throttled at **300 requests per minute** per user.

## Running Tests

```bash
php artisan test
```

Tests live in `tests/Unit` and `tests/Feature`. PHPUnit is configured via `phpunit.xml`.

## Docker

A `.env.docker` file and Laravel Sail configuration are included for containerized development:

```bash
./vendor/bin/sail up
```

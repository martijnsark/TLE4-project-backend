# TLE4 Project Backend

This repository contains the backend for the TLE4 project — a Laravel API-driven application providing articles, polls, memes, reactions, tags and related features.

## Table of Contents

- Project Overview
- ERD (Entity Relationship Diagram)
- Frontend Implementation
- Installation
- Deployment
- Configuration (.env)
- Usage & Testing
- Edge Cases & Decisions

## Project Overview

Impakt is a news platform that delivers news in a lighter, sometimes humorous way to help reduce pessimistic future outlooks among 18–23-year-olds in the Randstad region of the Netherlands, while still keeping them informed about important news.

## ERD (Entity Relationship Diagram)

The following Mermaid ER diagram summarizes the main entities and relationships in the system.

```mermaid
erDiagram
    USERS o|--o{ ARTICLES : authors
    USERS ||--o{ REACTIONS : makes
    USERS ||--o{ POLL_VOTES : votes
    USERS o|--o{ ARTICLE_VIEWS : views
    USERS o|--o{ MEME_VIEWS : views
    USERS ||--o{ GENERATED_CONTENTS : creates
    USERS ||--o{ CONTENT_REVIEWS : reviews
    USERS ||--o{ SAVED_ARTICLES : saves
    USERS ||--o{ SAVED_MEMES : saves
    USERS ||--o{ USER_TAGS : interests

    TAGS ||--o{ USER_TAGS : tagged_by
    ARTICLES ||--o{ ARTICLE_TAGS : tagged_with
    TAGS ||--o{ ARTICLE_TAGS : tags
    ARTICLES ||--o{ ARTICLE_SOURCES : cites
    SOURCES ||--o{ ARTICLE_SOURCES : referenced_by

    ARTICLES ||--o| CALL_TO_ACTIONS : has
    ARTICLES ||--o{ REACTIONS : receives
    ARTICLES ||--o{ POLLS : contains
    ARTICLES o|--o{ MEMES : generates
    ARTICLES ||--o{ ARTICLE_VIEWS : viewed_in
    ARTICLES ||--o{ SAVED_ARTICLES : saved_in

    MEMES ||--o{ MEME_VIEWS : viewed_in
    MEMES ||--o{ SAVED_MEMES : saved_in

    POLLS ||--o{ POLL_OPTIONS : options
    POLLS ||--o{ POLL_VOTES : receives
    POLL_OPTIONS ||--o{ POLL_VOTES : selected

    GENERATED_CONTENTS ||--o{ CONTENT_REVIEWS : reviewed_by

    USERS {
      integer id PK
      string username
      string name
      string email
      string password
      datetime email_verified_at
      string role
      string remember_token
      datetime created_at
      datetime updated_at
    }
    TAGS {
      integer id PK
      string name
      string category
      datetime created_at
      datetime updated_at
    }
    ARTICLES {
      integer id PK
      string title
      text summary
      text content
      string image_url
      string original_url
      string tone
      string status
      integer author_id FK
      datetime published_at
      datetime created_at
      datetime updated_at
    }
    CALL_TO_ACTIONS {
      integer id PK
      integer article_id FK
      string title
      text context_text
      text goal_text
      string target_url
      datetime created_at
      datetime updated_at
    }
    SOURCES {
      integer id PK
      string name
      string url
      integer reliability_score
      datetime created_at
      datetime updated_at
    }
    ARTICLE_TAGS {
      integer id PK
      integer article_id FK
      integer tag_id FK
      datetime created_at
      datetime updated_at
    }
    ARTICLE_SOURCES {
      integer id PK
      integer article_id FK
      integer source_id FK
      string source_url
      boolean is_primary
      datetime created_at
      datetime updated_at
    }
    SAVED_ARTICLES {
      integer id PK
      integer user_id FK
      integer article_id FK
      datetime saved_at
      datetime created_at
      datetime updated_at
    }
    REACTIONS {
      integer id PK
      integer user_id FK
      integer article_id FK
      string reaction
      datetime created_at
      datetime updated_at
    }
    POLLS {
      integer id PK
      integer article_id FK
      string question
      datetime created_at
      datetime updated_at
    }
    POLL_OPTIONS {
      integer id PK
      integer poll_id FK
      string option_text
      datetime created_at
      datetime updated_at
    }
    POLL_VOTES {
      integer id PK
      integer poll_id FK
      integer option_id FK
      integer user_id FK
      datetime voted_at
    }
    MEMES {
      integer id PK
      integer article_id FK
      string title
      string image_url
      text caption
      datetime created_at
      datetime updated_at
    }
    SAVED_MEMES {
      integer id PK
      integer user_id FK
      integer meme_id FK
      datetime saved_at
      datetime created_at
      datetime updated_at
    }
    ARTICLE_VIEWS {
      integer id PK
      integer user_id FK
      integer article_id FK
      datetime viewed_at
      integer reading_time_seconds
    }
    MEME_VIEWS {
      integer id PK
      integer user_id FK
      integer meme_id FK
      datetime viewed_at
      integer viewing_time_seconds
    }
    GENERATED_CONTENTS {
      integer id PK
      integer admin_id FK
      string title
      text generated_text
      string original_news_url
      string status
      datetime created_at
      datetime updated_at
    }
    CONTENT_REVIEWS {
      integer id PK
      integer generated_content_id FK
      integer admin_id FK
      text feedback
      boolean approved
      datetime reviewed_at
      datetime created_at
      datetime updated_at
    }
    USER_TAGS {
      integer id PK
      integer user_id FK
      integer tag_id FK
      datetime created_at
      datetime updated_at
    }

    SESSIONS {
      string id PK
      integer user_id FK
      string ip_address
      text payload
      integer last_activity
    }

    PASSWORD_RESET_TOKENS {
      string email PK
      string token
      datetime created_at
    }

    PERSONAL_ACCESS_TOKENS {
      integer id PK
      integer tokenable_id
      string tokenable_type
      string name
      string token
      datetime created_at
      datetime updated_at
    }
```

## Frontend Implementation (Routes and fields)

All API routes are prefixed with `/api`.

Use `Authorization: Bearer <token>` for protected routes after login.

### General Endpoints

- `POST /api/login`
    - Required fields: `email`, `password`
    - Optional fields: none in the current API controller
    - Returns: `message`, `user`, and `token`
- `POST /api/logout`
    - Required fields: none
    - Optional fields: none
    - Auth: required
    - Returns: a logout confirmation message


### User endpoints

- `GET /api/home`
    - Required fields: none
    - Optional fields: none
    - Returns: an array of active articles ordered from newest to oldest


### Admin endpoints

- `GET /api/articles`
  - Returns: list of articles (public)
- `GET /api/articles/{article}`
  - Returns: a single article by ID (public)
- `POST /api/articles`
  - Auth bearer token: required (admin)
  - Required fields: typical article fields (e.g. `title`, `summary`, `content`, `image_url`, `original_url`, `tone`, `status`)
  - Returns: the created `Article`
- `PUT /api/articles/{article}`
  - Auth bearer token: required (admin)
  - Replaces an article; accepts same fields as `POST`
- `PATCH /api/articles/{article}`
  - Auth bearer token: required (admin)
  - Partial update of an article
- `DELETE /api/articles/{article}`
  - Auth bearer token: required (admin)
  - Deletes the specified article
- `GET /api/articles/{article}/edit`
  - Auth bearer token: required (admin)
  - Returns article data suitable for editing (admin-only)

  ### backend testing

  - `GET /api/me`
    - Required fields: none
    - Optional fields: none
    - Auth: required
    - Returns: the authenticated `user`

### Frontend flow

- Submit `email` and `password` from the login form.
- Store the returned token securely and send it on protected requests.
- Load the home feed from `/api/home` on first render.
- Use `/api/me` to hydrate the current session after refresh.
- Call `/api/logout` to revoke the current token and clear the local session.

### Article response shape

The `/api/home` endpoint returns `Article` records with fields such as:

- `id`
- `title`
- `summary`
- `content`
- `image_url`
- `original_url`
- `tone`
- `status`
- `author_id`
- `published_at`
- `created_at`
- `updated_at`

## Installation

Prerequisites:

- PHP 8.1+ (match composer.json)
- Composer
- MySQL or Postgres
- Redis (recommended for queues)
- Node.js & npm (if front-end assets are built here)

Quick local setup:

```bash
composer install
cp .env.example .env
php artisan key:generate
# configure DB settings in .env
php artisan migrate --seed
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8000
```

For queue workers (local dev):

```bash
php artisan queue:work --tries=3
```

## Deployment

General recommendations for production deployment:

- Use a process manager (Supervisor) to run `php artisan queue:work` and `php artisan horizon` if enabled.
- Serve the app behind Nginx with PHP-FPM. Example Nginx location root: `public/`.
- Use environment variables (secure `.env`) and do not commit secrets.
- Run `php artisan migrate --force` as part of deploy.
- Use `php artisan config:cache`, `route:cache`, and `view:cache` for performance.
- Set up scheduled tasks (`php artisan schedule:run`) via cron.

An example minimal supervisor config for queue workers:

```ini
[program:tle4-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/app/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/laravel-queue.log
```

## Configuration (.env)

Important variables:

- `APP_ENV`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `CACHE_DRIVER`, `QUEUE_CONNECTION`, `SESSION_DRIVER`, `SESSION_LIFETIME`
- `MAIL_MAILER` and mail provider credentials
- `SANCTUM_STATEFUL_DOMAINS` / `SESSION_DOMAIN` if using Sanctum for SPA auth

## Usage & Testing

- API routes live under `routes/api.php`.
- Use Postman or HTTP client to exercise endpoints; authentication uses Laravel Sanctum tokens.
- Run automated tests:

```bash
composer test
# or
./vendor/bin/pest
```

## Edge Cases 





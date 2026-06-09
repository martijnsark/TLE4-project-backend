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

All API routes are prefixed with `http://145.24.237.97/api/`.

Use `Authorization: Bearer <token>` for protected routes after login.

Example JSON bodies below use realistic sample values. For routes with path or query parameters, the example request shows the complete URL.

### public endpoints (all users)

- `POST /api/register`
    - Required fields: `username`, `email`, `password`
    - Optional fields: `name`
    - Example request:
      ```json
      {
        "username": "julia_vermeer",
        "name": "Julia Vermeer",
        "email": "julia.vermeer@example.com",
        "password": "Str0ngPassw0rd!",
        "password_confirmation": "Str0ngPassw0rd!"
      }
      ```
    - Returns: the created `user` and auth token
- `POST /api/login`
    - Required fields: `email`, `password`
    - Optional fields: none in the current API controller
    - Example request:
      ```json
      {
        "email": "julia.vermeer@example.com",
        "password": "Str0ngPassw0rd!"
      }
      ```
    - Returns: `message`, `user`, and `token`
- `POST /api/logout`
    - Required fields: none
    - Optional fields: none
    - Auth: required
    - Example request: `POST /api/logout`
    - Returns: a logout confirmation message
- `GET /api/articles`
    - Returns: paginated list of articles (public)
    - Each article includes `views_count`, which can be used on article cards to show how popular the article is
    - Example requests:
        - `GET /api/articles`
        - `GET /api/articles?search=klimaat&sort=views&date_from=2026-06-01&date_to=2026-06-30`
        - `GET /api/articles?tag_id=1&sort=latest`
    - Query options:
        - `search=klimaat` filters by `title`, `summary`, or `content`
        - `tag_id=1` filters articles linked to tag ID `1`
        - `tag=Klimaat` filters articles linked to the tag name `Klimaat`
        - `sort=latest` sorts newest first
        - `sort=oldest` sorts oldest first
        - `sort=views` sorts by most viewed first
        - `date_from=2026-06-01` only shows articles published on or after that date
        - `date_to=2026-06-30` only shows articles published on or before that date
- `GET /api/happy-feed`
    - Returns: list of active articles that have the `happy` tag, sorted from newest to oldest
    - Example request: `GET /api/happy-feed`
    - Optional filters:
        - `tag_id=1` filters the happy feed by tag ID
        - `tag=Politiek` filters the happy feed by tag name
- `GET /api/articles/{article}`
    - Returns: a single article by ID (public)
    - Registers a view for the article when the article is opened
    - Example request: `GET /api/articles/42`
    - Also returns `views_count`, so the frontend can show how many times the article has been viewed
    - May also return related data when available, such as `tags`, `call_to_action`, and `memes`
- `GET /api/tags`
    - Required fields: none
    - Optional fields: none
    - Example request: `GET /api/tags`
    - Returns: array of `Tag` objects sorted by `category` then `name`
- `GET /api/memes`
    - Required fields: none
    - Optional query: `page=2` to load the next paginated meme feed page
    - Example request: `GET /api/memes?page=2`
    - Returns: paginated list of memes for the humor page
    - Each meme includes `id`, `article_id`, `title`, `image_url`, `caption`, `created_at`, `updated_at`, and an `article` object with at least `id` and `title`
    - Frontend usage: use this endpoint to render the humor/meme feed and navigate from a meme to the related article

- `GET /api/memes/{meme}`
    - Required fields: none
    - Optional fields: none
    - Path parameter: `meme` (the meme ID)
    - Example request: `GET /api/memes/12`
    - Returns: a single meme with its related article
    - Frontend usage: use this when opening a specific meme detail page and use the included `article_id` or `article.id` for the related article button
      <br><br>

### private endpoints (original user only)

- `PUT /api/update-account`
    - Required fields: `username`, `email`, `password`
    - Optional fields: `name`
    - Auth: required
    - Example request:
      ```json
      {
        "username": "julia_vermeer",
        "name": "Julia Vermeer",
        "email": "julia.vermeer.updated@example.com",
        "password": "N3wStr0ngPassw0rd!",
        "password_confirmation": "N3wStr0ngPassw0rd!"
      }
      ```
    - Returns: the updated `user`
- `DELETE /api/delete-account`
    - Required fields: none
    - Optional fields: none
    - Auth: required
    - Example request: `DELETE /api/delete-account`
    - Returns: a confirmation message or deleted account response from the API
- `GET /api/account`
    - Auth: required (`Authorization: Bearer <token>`)
    - Required fields: none
    - Optional fields: none
    - Example request: `GET /api/account`
    - Returns: the authenticated `user` with `savedArticles` loaded
- `POST /api/account/articles/{article}/save`
    - Auth: required (`Authorization: Bearer <token>`)
    - Required fields: none in the request body
    - Optional fields: none
    - Path parameter: `article` (the article ID to save)
    - Example request: `POST /api/account/articles/42/save`
    - Returns: a success `message` and the authenticated `user` with updated `savedArticles`
- `DELETE /api/account/articles/{article}/save`
    - Auth: required (`Authorization: Bearer <token>`)
    - Required fields: none
    - Optional fields: none
    - Path parameter: `article` (the article ID to remove from saved articles)
    - Example request: `DELETE /api/account/articles/42/save`
    - Returns: a success `message` and the authenticated `user` with updated `savedArticles`
- `GET /api/me/tags`
    - Auth: required (`Authorization: Bearer <token>`)
    - Required fields: none
    - Optional fields: none
    - Example request: `GET /api/me/tags`
    - Returns: array of `Tag` objects that the authenticated user has selected as interests (sorted by `category` then `name`)
- `PUT /api/me/tags`
    - Required fields (JSON body): `tag_ids` (array of integers that must exist in `tags.id`)
    - Optional fields: none
    - Auth: required
    - Example request:
      ```json
      {
        "tag_ids": [1, 3, 7]
      }
      ```
    - Returns: a success message and the updated array of the user's interest tags
- `GET /api/polls`
    - Auth: required (`Authorization: Bearer <token>`)
    - Required fields: none
    - Optional fields: none
    - Example request: `GET /api/polls`
    - Returns: list of available polls
- `GET /api/poll-options`
    - Auth: required (`Authorization: Bearer <token>`)
    - Required fields: none
    - Optional fields: none
    - Example request: `GET /api/poll-options`
    - Returns: list of poll options
- `POST /api/poll-votes`
    - Auth: required (`Authorization: Bearer <token>`)
    - Required fields: `poll_id`, `user_id`, `option_id`, `voted_at`
    - Optional fields: none
    - Example request:
      ```json
      {
        "poll_id": 7,
        "user_id": 15,
        "option_id": 31,
        "voted_at": "2026-06-08 14:30:00"
      }
      ```
    - Returns: the created poll vote
- `DELETE /api/poll-votes/{pollVote}`
    - Auth: required (`Authorization: Bearer <token>`)
    - Required fields: none
    - Optional fields: none
    - Path parameter: `pollVote` (for example `1`)
    - Example request: `DELETE /api/poll-votes/18`
    - Returns: a success response after removing the poll vote
- `GET /api/polls/{poll}/results`
    - Auth: required (`Authorization: Bearer <token>`)
    - Required fields: none
    - Optional fields: none
    - Path parameter: `poll` (for example `1`)
    - Example request: `GET /api/polls/7/results`
    - Returns: the poll results summary

<br><br>

### Admin endpoints (admins only)

- `POST /api/articles`
    - Auth bearer token: required (admin)
    - Required fields: `title`, `summary`, `content`, `image_url`, `original_url`, `tone`, `status`
    - Optional fields: `published_at`, `tag_ids`
    - Example request:
      ```json
      {
        "title": "Dutch Railways Introduces New Weekend Service",
        "summary": "A lighter look at the new train schedule changes across the Randstad.",
        "content": "NS is adding a new weekend service pattern to reduce congestion and improve reliability for travelers.",
        "image_url": "https://images.example.com/articles/ns-weekend-service.jpg",
        "original_url": "https://news.example.com/dutch-railways-weekend-service",
        "tone": "light",
        "status": "active",
        "published_at": "2026-06-08 09:00:00",
        "tag_ids": [1, 4, 9]
      }
      ```
    - Returns: the created `Article`
- `PUT /api/articles/{article}`
    - Auth bearer token: required (admin)
    - Required fields: none when updating partially; accepted fields match `POST /api/articles`
    - Optional fields: article fields such as `title`, `summary`, `content`, `image_url`, `original_url`, `tone`, `status`, `published_at`, and `tag_ids`
    - Example request:
      ```json
      {
        "title": "Dutch Railways Expands Weekend Service",
        "summary": "Updated copy for the weekend service article.",
        "content": "The schedule change is now rolling out with a slightly expanded route plan.",
        "image_url": "https://images.example.com/articles/ns-weekend-service-updated.jpg",
        "original_url": "https://news.example.com/dutch-railways-weekend-service-updated",
        "tone": "humorous",
        "status": "active",
        "published_at": "2026-06-08 10:15:00",
        "tag_ids": [1, 4, 9]
      }
      ```
    - Replaces an article; accepts the same fields as `POST`
- `PATCH /api/articles/{article}`
    - Auth bearer token: required (admin)
    - Required fields: none when updating partially; accepted fields match `POST /api/articles`
    - Optional fields: same as `PUT /api/articles/{article}`
    - Example request:
      ```json
      {
        "status": "archived",
        "published_at": "2026-06-08 10:15:00",
        "tag_ids": [1, 4, 9]
      }
      ```
    - Partially updates an article; accepts the same fields as `POST`
- `DELETE /api/articles/{article}`
    - Auth bearer token: required (admin)
    - Example request: `DELETE /api/articles/42`
    - Deletes the specified article
- `GET /api/articles/{article}/edit`
    - Auth bearer token: required (admin)
    - Example request: `GET /api/articles/42/edit`
    - Returns article data suitable for editing (admin-only)
- `GET /api/poll-votes/{pollVote}`
    - Auth: required (`Authorization: Bearer <token>`)
    - Required fields: none
    - Optional fields: none
    - Path parameter: `pollVote` (for example `1`)
    - Example request: `GET /api/poll-votes/18`
    - Returns: a single poll vote record
- `POST /api/articles/{article}/memes`
    - Auth bearer token: required (admin)
    - Required fields: `title`, `image_url`
    - Optional fields: `caption`
    - Path parameter: `article` (the article ID the meme should be attached to)
    - Example request:
      ```json
      {
        "title": "When the train is actually on time",
        "image_url": "https://images.example.com/memes/train-on-time.jpg",
        "caption": "A rare but glorious moment in public transport history."
      }
      ```
    - Returns: a success `message` and the created `meme`

- `PATCH /api/memes/{meme}`
    - Auth bearer token: required (admin)
    - Required fields: none
    - Optional fields: `title`, `image_url`, `caption`
    - Path parameter: `meme` (the meme ID to update)
    - Example request:
      ```json
      {
        "title": "When the train is finally on time",
        "image_url": "https://images.example.com/memes/train-on-time-updated.jpg",
        "caption": "This is what hope looks like."
      }
      ```
    - Returns: a success `message` and the updated `meme`

- `DELETE /api/memes/{meme}`
    - Auth bearer token: required (admin)
    - Required fields: none
    - Optional fields: none
    - Path parameter: `meme` (the meme ID to delete)
    - Example request: `DELETE /api/memes/12`
    - Returns: a success `message` after deleting the meme

- `POST /api/polls`
    - Auth bearer token: required (admin)
    - Required fields: `article_id`, `question`
    - Optional fields: none
    - Example request:
      ```json
      {
        "article_id": 42,
        "question": "What should the next train-focused article cover?"
      }
      ```
    - Returns: a success message after creating the poll

- `PUT /api/polls/{poll}`
    - Auth bearer token: required (admin)
    - Required fields: `article_id`, `question`
    - Optional fields: none
    - Path parameter: `poll` (for example `7`)
    - Example request:
      ```json
      {
        "article_id": 42,
        "question": "What should the updated poll question be?"
      }
      ```
    - Returns: the updated poll

- `PATCH /api/polls/{poll}`
    - Auth bearer token: required (admin)
    - Required fields: `article_id`, `question`
    - Optional fields: none
    - Path parameter: `poll` (for example `7`)
    - Example request:
      ```json
      {
        "article_id": 42,
        "question": "What should the updated poll question be?"
      }
      ```
    - Returns: the updated poll

- `DELETE /api/polls/{poll}`
    - Auth bearer token: required (admin)
    - Required fields: none
    - Optional fields: none
    - Path parameter: `poll` (for example `7`)
    - Example request: `DELETE /api/polls/7`
    - Returns: a success message after deleting the poll

- `POST /api/poll-options`
    - Auth bearer token: required (admin)
    - Required fields: `poll_id`, `option_text`
    - Optional fields: none
    - Example request:
      ```json
      {
        "poll_id": 7,
        "option_text": "More weekend trains"
      }
      ```
    - Returns: the created poll option

- `PUT /api/poll-options/{pollOption}`
    - Auth bearer token: required (admin)
    - Required fields: `poll_id`, `option_text`
    - Optional fields: none
    - Path parameter: `pollOption` (for example `31`)
    - Example request:
      ```json
      {
        "poll_id": 7,
        "option_text": "Fewer delays"
      }
      ```
    - Returns: the updated poll option

- `PATCH /api/poll-options/{pollOption}`
    - Auth bearer token: required (admin)
    - Required fields: `poll_id`, `option_text`
    - Optional fields: none
    - Path parameter: `pollOption` (for example `31`)
    - Example request:
      ```json
      {
        "poll_id": 7,
        "option_text": "Fewer delays"
      }
      ```
    - Returns: the updated poll option

- `DELETE /api/poll-options/{pollOption}`
    - Auth bearer token: required (admin)
    - Required fields: none
    - Optional fields: none
    - Path parameter: `pollOption` (for example `31`)
    - Example request: `DELETE /api/poll-options/31`
    - Returns: the deleted poll option record

<br><br>

### backend testing (testing for backend)

- `GET /api/me`
    - Required fields: none
    - Optional fields: none
    - Auth: required
    - Example request: `GET /api/me`
    - Returns: the authenticated `user`

<br><br>

### retired routes (no longer active)

- `GET /api/home`
    - Required fields: none
    - Optional fields: none
    - Returns: an array of active articles ordered from newest to oldest

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





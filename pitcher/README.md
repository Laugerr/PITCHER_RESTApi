# PITCHER REST API

PITCHER is a Laravel-based REST API for a social-style posting platform with:
- authentication
- users and profiles
- posts and comments
- like/dislike reactions
- categories

## Tech Stack

- PHP 7.3+ / 8.x
- Laravel 8
- Laravel Sanctum (token auth)
- MySQL (or compatible SQL database)

## Project Modules

- `Auth`: register, login, logout, password reset
- `Users`: profile, user list/admin actions, online status
- `Posts`: CRUD, comments, likes/dislikes, category filtering
- `Comments`: CRUD, likes/dislikes
- `Categories`: CRUD, posts by category

## Quick Start

1. Install dependencies
```bash
composer install
```

2. Copy env and generate app key
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure database in `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pitcher
DB_USERNAME=root
DB_PASSWORD=
```

4. Run migrations
```bash
php artisan migrate
```

5. Start server
```bash
php artisan serve
```

Base URL (local): `http://127.0.0.1:8000/api`

## Authentication

Login returns a Sanctum token:

```http
POST /api/auth/login
Content-Type: application/json

{
  "login": "your_login",
  "password": "your_password"
}
```

Use token on protected routes:
```http
Authorization: Bearer <token>
Accept: application/json
```

## API Endpoints

### Auth
- `POST /auth/register`
- `POST /auth/login`
- `POST /auth/logout`
- `POST /auth/password-reset`
- `POST /auth/password-reset/{token}`

### Users
- `GET /users/profile`
- `GET /users` (admin)
- `GET /users/checkstatus` (auth)
- `GET /users/{id}` (auth)
- `POST /users` (admin)
- `POST /users/avatar` (auth)
- `DELETE /users/{id}` (admin)

### Posts
- `GET /posts` (auth)
- `GET /posts/{id}` (auth)
- `POST /posts` (auth)
- `PATCH /posts/{id}` (auth)
- `DELETE /posts/{id}` (auth)
- `POST /posts/{id}/comments` (auth)
- `GET /posts/{id}/comments` (auth)
- `POST /posts/{id}/like` (auth)
- `GET /posts/{id}/like` (auth)
- `DELETE /posts/{id}/like` (auth)
- `GET /posts/{id}/categories` (auth)

### Categories
- `POST /categories` (auth)
- `GET /categories` (auth)
- `GET /categories/{id}` (auth)
- `GET /categories/{id}/posts` (auth)
- `PATCH /categories/{id}` (auth)
- `DELETE /categories/{id}` (auth)

### Comments
- `GET /comments/{id}` (auth)
- `PATCH /comments/{id}` (auth)
- `DELETE /comments/{id}` (auth)
- `GET /comments/{id}/like` (auth)
- `POST /comments/{id}/like` (auth)
- `DELETE /comments/{id}/like` (auth)

## Tests

Run the suite:
```bash
php artisan test
```

Current feature tests include:
- auth login token behavior
- post like/dislike transition correctness
- category create with nullable description

## Notes

- API routes are defined in `routes/api.php`.
- This repo currently stores post categories as a comma-separated string in the `posts.categories` field.

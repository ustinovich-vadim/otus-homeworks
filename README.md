# OTUS homework-1

This project is a Laravel application set up to run in Docker containers. Follow the steps below to get started.

## Prerequisites

Make sure you have the following software installed on your machine:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Git](https://git-scm.com/)

## Installation

1. **Clone the repository**

   ```bash
   git clone --branch feature/feed https://github.com/ustinovich-vadim/otus-homeworks.git
   cd otus-homeworks

2. **Copy the .env.example file to .env**

    ```bash
   cp .env.example .env

3. **Update the .env file with your configuration**

    ```bash
   cp .env.example .env

4. **Update the .env file with your configuration**
   Make sure to set the necessary environment variables, especially the database connection details. Example:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=db
    DB_PORT=5432
    DB_DATABASE=laravel
    DB_USERNAME=laravel
    DB_PASSWORD=secret
    POSTGRESS_PORT=5433
    COUNT_OF_USERS=150

5. **Build and start the Docker containers**
    ```bash
    docker-compose up -d
6. **Install PHP dependencies**
    ```bash
    docker-compose exec app composer install
7. **Run migrations and seeders**
    ```bash
    docker-compose exec app php artisan migrate:fresh --seed

8. **Usage API Endpoints**
 - **Register - POST /api/register**

  **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`

  **Body:**
  ```json
    {
      "name": "John",
      "surname": "Doe",
      "birth_date": "1990-01-01",
      "gender": "male",
      "hobbies": "Reading, Coding",
      "city": "New York",
      "email": "john.doe@example.com",
      "password": "password",
      "password_confirmation": "password"
    }
  ```
    
    
   - **Login - POST /api/login**

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

```json
 {
    "email": "john.doe@example.com",
    "password": "password"
 }
```

- **Get User Profile - GET /api/users/{id}**

  **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`

- **Add Friend - POST /api/friends**

**Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`

```json
  {
      "friend_id": "123"
  }
```

- **Delete Friend - DELETE /api/friends/{friend_id}**

  **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`

- **Get Friends Feed - GET /api/posts/feed**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`

- **Create Post - POST /api/posts/create**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`
```json
  {
    "text": "Test post"
  }
```

- **Get Post - GET /api/posts/get/{id}**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`

- **Update Post - PUT /api/posts/update**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`
```json
  {
    "id": "8409",
    "text": "new text"
  }
```

- **Delete Post - DELETE /api/posts/delete/{id}**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`

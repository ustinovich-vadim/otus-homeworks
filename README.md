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
   git clone --branch feature/microservice-for-dialogs https://github.com/ustinovich-vadim/otus-homeworks.git
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
    DB_DATABASE=postgres
    DB_USERNAME=postgres
    DB_PASSWORD=secret
    POSTGRESS_PORT=5433
    COUNT_OF_USERS=150
    JWT_SECRET=secret_for_token
    DIALOG_MICROSERVICE_URL=http://svc-webserver/api

5. **Create the shared network**
    ```bash
   docker network create otus_network 
    ```
6. **Build and start the Docker containers**
    ```bash
    docker-compose up -d
6. **Install PHP dependencies**
    ```bash
    docker-compose exec app composer install
7. **Run migrations and seeders**
    ```bash
    docker-compose exec app php artisan migrate:fresh --seed

8. **Usage of Dialog Microservice**

   To use the Dialog Microservice, clone the repository and navigate to its directory:
    ```bash
    git clone git@github.com:ustinovich-vadim/otus-microservice-for-dialogs.git
    cd dialog-microservice
   ```
    For setup instructions, refer to the README.md file in the repository to properly configure and run the service.

9. **Usage API Endpoints**
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
- `Authorization': 'Bearer token`

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
  - `Authorization': 'Bearer token`

- **Add Friend - POST /api/friends**

**Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization': 'Bearer token`

```json
  {
      "friend_id": "123"
  }
```

- **Delete Friend - DELETE /api/friends/{friend_id}**

  **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization': 'Bearer token`

- **Get Friends Feed - GET /api/posts/feed**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`
    - `Authorization': 'Bearer token`

- **Create Post - POST /api/posts/create**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`
    - `Authorization': 'Bearer token`
```json
  {
    "text": "Test post"
  }
```

- **Get Post - GET /api/posts/get/{id}**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`
    - `Authorization': 'Bearer token`

- **Update Post - PUT /api/posts/update**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`
    - `Authorization': 'Bearer token`
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
    - `Authorization': 'Bearer token`


- **Send Message to user - POST /api/messages/{user_id}/send**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`
    - `Authorization': 'Bearer token`
```json
  {
    "text": "Text of message"
  }
```

- **Get list of messages from dialog with user - GET /api/messages/{user_id}/list**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`
    - `Authorization': 'Bearer token`

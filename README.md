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
   git clone https://github.com/ustinovich-vadim/otus-homeworks.git
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

- Register - POST /api/register
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

- Login - POST /api/login
   ```json
   {
      "email": "john.doe@example.com",
      "password": "password"
   }
- Get User Profile - GET /api/users/{id}
  ```http
  Headers: 
  Authorization: Bearer your-access-token

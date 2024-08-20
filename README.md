# Time Tracker

## Overview

This project is a Time Tracker application built using:

 - Backend: PHP with Symfony 6
 - Frontend: React.js
 - Database: MySQL 8
 - ORM: Doctrine
 - Environment: Docker/Docker Compose
 - Version Control: Git

The application allows users to track time spent on various tasks, with full CRUD (Create, Read, Update, Delete) functionality for managing tasks and time entries.

## Features

 - User Authentication and Authorization
 - Task Management (CRUD)
 - Time Entry Management (CRUD)
 - Reporting on time spent by task, day, or user
 - Responsive design using React.js
 - API-based architecture for frontend-backend communication

## Requirements

 - Docker and Docker Compose installed
 - Git installed

## Setup

1. **Clone the Repository**

```bash
git clone https://github.com/alexkot9111/time_tracker.git
cd time_tracker
```

2. **Build and Start Docker Containers**

This command starts the Symfony application, MySQL8 database, and other configured services in detached mode:
```bash
docker-compose up -d --build
```

3. **Install Composer Dependencies**

Access the Symfony container and install PHP dependencies using Composer:
```bash
docker-compose exec php-fpm composer install
```

4. **Environment Configuration**

Create a .env file in the Symfony project root to configure your local environment variables (example):
```bash
DATABASE_URL="mysql://username:password@mysql:3306/timetracker?serverVersion=8&charset=utf8mb4"
```

5. **Run Database Migrations**

Apply database migrations to set up the database schema:
```bash
docker-compose exec php-fpm bin/console doctrine:migrations:migrate --no-interaction
```

## Running Tests

To run the PHPUnit tests, use the following command:
```bash
docker-compose exec php-fpm php bin/phpunit
```

## Stopping the Containers

To stop the Docker containers:
```bash
docker-compose down
```
# Blog Application

A simple blog application built using native PHP, MySQL, and PDO.

## Features

- **User Authentication**: Register, log in, and log out.
- **CRUD Operations**: Create, Read, Update, and Delete blog posts.
- **Comments System**: Users can add, and delete comments.
- **Caching**: Posts and paginated results are cached.

## Setup

### Prerequisites
Ensure you have installed
- **Docker**

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/ani7187/blog_app.git
   cd blog_app

- **Set up environment variables:** Copy the .env.example file to .env and configure your environment variables
- **Start the application using Docker:**
    ```bash 
    docker-compose up --build -d
- **Go into container:**
    ```bash
    docker-compose exec php bash
- **Install dependencies:** 
    ```bash
    composer install
- **Set up the database:** Create db schema:
    ```bash
    php migrate.php
- **Seed initial data:(not required)** Seed data:
    ```bash
    php seeder.php
- Access the app at http://localhost:8000.

## Contact
For more details or to report bugs, contact at:
- 📧Email: azizyana02@gmail.com

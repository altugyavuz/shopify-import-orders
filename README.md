# Shopify Order List Application

## Table of Contents

1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [Environment Setup](#environment-setup)
4. [Configuration](#configuration)

## Project Overview

This project introduces the foundational setup for a Laravel-based project, incorporating Shopify order management
functionality.
Added are essential files such as models, controllers, Vue.js components for pagination and orders, and basic
configuration settings.
This establishes the groundwork for a Shopify order processing and importing system, utilizing Inertia.js
for front-end page rendering and Laravel queues for background jobs.

## Technology Stack

- **Backend:** Laravel v11.34.2
- **Database:** MySQL
- **Queue:** Redis
- **Frontend:** Vue 3, Inertia JS
- **Additional Tools:** Guzzle, Horizon

## Environment Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL Database
- Redis
- Node v18.12 or higher

### Installation

To setup the project locally, follow these steps:

1. **Clone the Repository**

    ```bash
    git clone https://github.com/altugyavuz/shopify-import-orders.git
    cd your-repo
    ```

2. **Install Dependencies**
    Install composer dependencies:
    ```bash
    composer install
    ```
   
    Install node packages and build it:
    ```bash
    npm install && npm run build
    ```

3. **Environment Configuration**

   Copy the `.env.example` to `.env`, then configure the `.env` file for your local environment settings:

    ```bash
    cp .env.example .env
    ```

4. **Generate Application Key**

    ```bash
    php artisan key:generate
    ```

5. **Database Migration**

   Ensure your database is up and running, then migrate:

    ```bash
    php artisan migrate
    ```

6. **Start Horizon**

    ```bash
    php artisan horizon
    ```

## Configuration

The system can recognize Shopify credentials in two ways:

1. By adding the following information to the Environment file during installation;
   
    ```
    SHOPIFY_STORE_NAME="..."
    SHOPIFY_API_KEY="..."
    ```

2. If you do not add any data to the environment file, you will see a setup screen the first time you run the project. You can continue by entering the necessary information on this screen.
## Requirement
- PHP 8.1
- MySql 8
- Composer
- Docker

## Getting Started
1. Download the source code
2. Change the database setting at \.env  file
   ```
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=mini_aspire
    DB_USERNAME=sail
    DB_PASSWORD=password
    ```
3. Go to the root folder of project and run composer command:
    ```composer log
    composer install
    ```
4. Run migration to create DB and import admin user:
    ```composer log
    php artisan migrate --seed
    ```
5. Start the sail:
    ```composer log
    ./vendor/bin/sail up
    ```

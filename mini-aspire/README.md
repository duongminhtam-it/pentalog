## Description
1. Create the api module to manage loan
2. This source code is not using on the fact
3. The api which is implemented:
- For authentication
    - Login
    - Logout
    - Register
- For loan management
    - Search loan
    - Get loan
    - Create loan
    - Approve loan
    - Repayment loan

## Requirement
- PHP 8.1
- MySql 8
- Composer
- Docker

## Getting Started
1. Download the source code, install docker
2. Create \.env file from \.env.example file
3. Go to the root folder of project and run composer command:
    ```composer log
    composer install
    ```
4. Start the sail:
    ```composer log
    ./vendor/bin/sail up
    ```
5. Run migration to create DB and import admin user:
    ```composer log
    php artisan migrate --seed
    ```
   - Admin login: 
     - email: admin@gmail.com
     - password: admin

# Get Started
 After installing the project You will need to run the following commands.
 
 #### - Install Composer
```
composer install
```

#### - Create .env file
Copy .enc.example file to root folder and rename to .env and set environment variables.

#### - Run migration command
```
php bin/console doctrine:migrations:migrate
```

#### - Starting the Server
```
php bin/console server:start
```


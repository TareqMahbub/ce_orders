Navigate:

- [Deployment instructions](#deployment-instructions)
- [Deploy using Docker (on any machine)](#deploy-using-docker-on-any-machine)

# Deployment instructions

- Following dependencies must be present to run the application:
    - PHP 8.2+
    - MySQL 8
    - Composer 2.2+
    - Node 23.5+
    - NPM latest

- Create a MySQl database for the Application
- Clone the repository from GitHub
```
git clone git@github.com:TareqMahbub/ce_orders.git
cd ce_orders
```
- Copy .env.example to .env file and 
```
copy .env.example .env (on windows)
```
```
cp .env.example .env (on linux)
```

- Configure database credentials

-  Execute following commands ("Tested & Confirmed" on Windows 11)
```
npm install
npm run build
composer install
php artisan key:generate
php artisan migrate
php artisan test
php artisan orders:sync
php artisan serve --port=8199
```

# Deploy using Docker (on any machine)

- Clone the repository from GitHub

```
git clone git@github.com:TareqMahbub/ce_orders.git
cd ce_orders
```

- Copy .env.example to .env file
```
copy .env.example .env (on windows)
```
```
cp .env.example .env (on linux)
```

- Execute following commands ("Tested & Confirmed" with Docker Desktop 4.37.1)
```
docker-compose run --rm npm install
docker-compose run --rm npm run build
docker-compose run --rm composer install
docker-compose run --rm artisan key:generate
docker-compose up -d --build mysql
docker-compose run --rm artisan migrate # run 2 times if it fails first time
docker-compose run --rm artisan orders:sync
docker-compose up -d server
```

-   if all containers are running, you can visit: http://localhost:8199/

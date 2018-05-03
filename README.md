# Modulr API Laravel Passport


## Table of Contents

- [Install](#install)
- [Dependencies](#dependencies)
- [Routes](#routes)
    - [Authentication](#authentication)


### Install

1. Clone repository
```
$ git clone https://github.com/modulr/api-laravel.git
```

2. Enter folder
```
$ cd api-laravel
```

3. Install composer dependencies
```
~/api-laravel$ composer install
```

4. Generate APP_KEY
```
~/api-laravel$ php artisan key:generate
```

5. Configure .env file, edit file with next command `$ nano .env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database
DB_USERNAME=user
DB_PASSWORD=secret
```

6. Run migrations
```
~/api-laravel$ php artisan migrate
```

7. Create client
```
~/api-laravel$ php artisan passport:install
```


### Dependencies


- [laravolt/avatar](https://github.com/laravolt/avatar) - Generate Avatars


### Routes

##### Authentication

- POST /auth/login
- GET /auth/logout
- POST /auth/signup
- GET /auth/user

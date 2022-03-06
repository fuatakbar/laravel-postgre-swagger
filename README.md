## Prerequisite

- PostgreqSQL
- Composer
- Git

## How to setup :

This is a repository that is used to document the testing of making laravel applications using the postgresql database and api documentation using swagger.

1. Doing repository cloning follow command bellow : <br>
> git clone https://github.com/fuatakbar/laravel-postgre-swagger.git <br>
> composer install <br>

2. Copy .env.example and change name to .env after that run this command : <br>
> php artisan key:generate

3. Open your code editor and edit .env then change database configuration with yours. Example :
```
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=yourdatabase
DB_USERNAME=postgres
DB_PASSWORD=yourpassword
```

4. Add this two key at the bottom of .env then save your .env :
```
JWT_SECRET=buNGd6WFVmmXADuDQ0nn3dxbEyVKlnLfIK11YeiuXL2hCDskTLfg7uKkpMUREtfQ
L5_SWAGGER_GENERATE_ALWAYS=true
```

5. Open your command line on app directory then run this command :
> php artisan migrate <br>
> php artisan l5-swagger:generate <br>
> php artisan serve

## API Documentation

To get a look for API Documentation on this application you can open url :
> http://127.0.0.1:8000/api/documentation

### Note :
- you must create your own account using url above
- after login success, you get secret key token, <br> then you must add it into "Authorize" button right above API Documentation URL

### 3rd Party Lib :

- **[L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)**
- **[jwt-auth](https://github.com/tymondesigns/jwt-auth)**

### Author
> Fuat Akbar | fuatakbars@gmail.com
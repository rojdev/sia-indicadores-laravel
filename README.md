<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Setup con Docker Compose (Recomendado)

Esta es la forma más rápida y sencilla de levantar el proyecto de forma local:

### 1. Preparar las variables de entorno
Copia el archivo de ejemplo para crear tu `.env`:
```bash
cp .env.example .env
```
*(Nota: El archivo `.env` ya viene preconfigurado para conectarse al contenedor de base de datos MySQL `basededatos`)*.

### 2. Construir e iniciar contenedores
Usa docker compose para levantar los servicios (Aplicación, MySQL, phpMyAdmin):
```bash
docker compose up -d
```

### 3. Instalar dependencias de PHP
Ejecuta la instalación de Composer dentro del contenedor ignorando requerimientos de plataforma para evitar problemas con la versión de PHP:
```bash
docker compose run indicadores composer install --ignore-platform-reqs
```

### 4. Generar Key y Configurar Storage
Genera la clave del proyecto y crea los enlaces de storage requeridos:
```bash
docker compose exec indicadores php artisan key:generate
docker compose exec indicadores php artisan storage:link
```

Asegúrate de crear el directorio de avatares en public storage si es necesario:
```bash
docker compose exec indicadores mkdir -p storage/app/public/avatars
```

### 5. Correr Migraciones y Seeders
Ejecuta las migraciones de base de datos junto con el sembrado de datos iniciales:
```bash
docker compose exec indicadores php artisan migrate:fresh --seed
```

### 6. Acceder a la Aplicación
*   **Aplicación web:** [http://localhost:7777](http://localhost:7777)
    *   **Usuario por defecto:** `pataxjose@gmail.com`
    *   **Contraseña:** `123456789`
*   **phpMyAdmin (Administrador DB):** [http://localhost:8080](http://localhost:8080)
    *   **Host:** `basededatos`
    *   **Usuario:** `innovacion`
    *   **Contraseña:** `innovacion123`


## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[CMS Max](https://www.cmsmax.com/)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

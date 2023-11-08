# DotsFood

DotsFood - telegram bot written with laravel and telegram-bot-sdk https://telegram-bot-sdk.com/. This bot implements the main functions of the telegram bot. Such as sending messages, processing callbacks and commands. The bot communicates with the live api dots platform, simulating the creation of food orders from different parts of Ukraine.


## Installation

Clone the repository

```bash
  git clone https://github.com/JuanCote/DotsFood.git
```

Install all the dependencies using composer

```bash
  composer install
```

Copy the example env file and make the required configuration changes in the .env file

```bash
  cp .env.example .env
```

Run the database migrations (Set the database connection in .env before migrating)
```bash
  php artisan migrate
```
Start the local development server
```bash
  php artisan serve
```
## Environment Variables

To run this project, you will need to add the following environment variables to your .env file

`API_TOKEN`

`API_ACCOUNT_TOKEN`

`API_AUTH_TOKEN`

`TELEGRAM_BOT_TOKEN`

`DOTS_HOST`


<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

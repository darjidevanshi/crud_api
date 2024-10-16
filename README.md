# User API Project

## Description
This project implements a set of user APIs , including user registration, login, password management, and profile updates. The application is built using laravel framework and utilizes mysql for data storage.


## Features
- User Signup with OTP verification
- User Login
- Forgot Password functionality
- Reset Password functionality
- Profile Update API

## Technologies Used
- PHP 7.4 Version
- Laravel
- MySQL 

## API Endpoints
| Endpoint                       | Method | Description                           |
|-------------------------------|--------|---------------------------------------|
| `/api/signup`                 | POST   | Registers a new user                 |
| `/api/login`                  | POST   | Authenticates a user                 |
| `/api/forgot-password`        | POST   | Sends a password reset link          |
| `/api/password/reset-password`| POST   | Resets the password using the token   |
| `/api/update-profile`         | POST   | Updates user profile information      |
| `/api/signout`                | POST   | signout user                          |


## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/darjidevanshi/crud_api.git
   cd crud_api


composer install
.env file changes the email credentials

## start the server
php artisan serve


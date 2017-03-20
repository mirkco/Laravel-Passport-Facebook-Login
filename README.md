# Laravel Passport Facebook Login
Provides a new Laravel Passport Grant Client named `facebook_login', allowing you to log a user in with just their Facebook Login token

Note: A new User **will be created** if an existing user was not found for the given token 

## Install

Install with composer...  `composer require mirkco/laravel-passport-facebook-login`

### Versions

* Laravel 5.4 and Passport 2.0 only supported at this time

## Setup

* Add `Mirkco\PassportFacebookLogin\RequestGrantProvider::class` to your list of providers **after** `Laravel\Passport\PassportServiceProvider`.
* Add `Mirkco\PassportFacebookLogin\FacebookLoginTrait` Trait to your `User` model (or whatever model you have configured to work with Passport).
* Add your Facebook App details to your `.env` file
```bash
# file .env
FACEBOOK_APP_ID={app_id from Facebook}
FACEBOOK_APP_SECRET={app_secret from facebook}
```

## How to use

* Make a **POST** request to `https://your-site.com/oauth/token`, just like you would a **Password** or **Refresh** grant.
* The POST body should contain `grant_type` = `facebook_login` and `fb_token` = `{token from facebook login}`.
* An `access_token` and `refresh_token` will be returned if successful.

## Notes:
It is assumed that your `User` model has `first_name` and `last_name` fields. 

## Thanks
This package is based off https://github.com/mikemclin/passport-custom-request-grant
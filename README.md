# JWT Auth API

JSON Web Token Authentication for your OctoberCMS API integrated with RainLab.User

This plugin provides token based authentication to your application. Is based on the awesome package [JSON Web Token Authentication for Laravel & Lumen](https://github.com/tymondesigns/jwt-auth) by Sean Tymon.

### Requirements

RainLab.User plugin

### Installation

1. After plugin installation you need to copy /plugins/vdomah/jwtauth/config/auth.php to {root}/config/auth.php, otherwise you'll got an error.

2. Generate JWT Authentication Secret. It will be used to sign your tokens. You got 2 options:
    - generate using command line: 
        ````$xslt
        php artisan jwt:generate
        ````
        You need to assign the generated value to JWT_SECRET in your .env.
    - go to Backend > Settings > JWTauth settings and click Generate Secret Key and save. 
    This value will override JWT_SECRET value from .env.

## Endpoints 

The plugin provides 4 endpoints:

- /api/login

    Makes attempt to authenticate and returns token if succeeded. Also the basic user info is included in the response.
    By defult expects 2 parameters to receive: email and password. 

- /api/signup

    Tries to create a user and returns token if succeeded. The user info is included in the response.
    By default expects 3 parameters to receive: email, password and password_confirmation.

- /api/refresh

    Tries to refresh the token and return the new token. 
    By default expects 1 parameter: token.

- /api/invalidate

    Tries to invalidate the given token - this can be used as an extra precaution to log the user out.
    By default expects 1 parameter: token. 

## .env options

| Variable        | Default           |
| ------------- |:-------------:|
| JWT_SECRET      |  |
| JWT_TTL      | 60      |
| JWT_REFRESH_TTL | 20160 |
| JWT_ALGO | HS256 |
| JWT_USER_CLASS | RainLab\User\Models\User |
| JWT_IDENTIFIER | id |
| JWT_BLACKLIST_ENABLED | true |
| JWT_PROVIDERS_USER | Tymon\JWTAuth\Providers\User\EloquentUserAdapter |
| JWT_PROVIDERS_JWT | Tymon\JWTAuth\Providers\JWT\NamshiAdapter |
| JWT_PROVIDERS_AUTH | Tymon\JWTAuth\Providers\Auth\IlluminateAuthAdapter |
| JWT_PROVIDERS_STORAGE | Tymon\JWTAuth\Providers\Storage\IlluminateCacheAdapter |


See config/config.php file for default values.

## Extending

### How to use this in another plugin?

Simply add `->middleware('\Tymon\JWTAuth\Middleware\GetUserFromToken')` to the end of the route in the plugin's routes.php

eg: 
```
Route::post('test', function (\Request $request) {
   return response()->json(('The test was successful'));
})->middleware('\Tymon\JWTAuth\Middleware\GetUserFromToken');
```

Then when making the request set the header "Authorization" to "Bearer `{yourToken}`"

### How to define own set of user attributes in response?

For sign up and sign in add corresponding methods getAuthApiSignupAttributes or/and getAuthApiSigninAttributes to User model by extending it in your plugin's boot method:

```
    User::extend(function($model) {
        $model->addDynamicMethod('getAuthApiSignupAttributes', function () use ($model) {
            return [
                'my-attr' => $model->my_attr,
            ];
        });
    });
```
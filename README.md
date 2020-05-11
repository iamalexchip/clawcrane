# Clawcrane

Clawcrane is a laravel package for GraphQl like data fetching on your eloquent models.

Usage example

```php
use Iamalexchip\ClawCrane;

$users = User::get();
$clawcrane = new ClawCrane($users);
$template = '{"username": "", "firstname": "", "email": ""}';
$clawcrane->get($template);

/*
[
    "data" => [
        "users" => [
            [
                "username" => "zerochip",
                "firstname" => "Alex"
            ],
            [
                "username" => "johndoe",
                "firstname" => "John",
                "email" => "johndoe24@mail.com"
            ]
        ]
    ],
    "errors" => [
        "App\\User: attribute [email] access denied"
    ]
]
*/
```

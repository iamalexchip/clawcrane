# Picker: Get Method
----

To get data from a model or collection we use the get method in the `Picker` class

```php
use ClawCrane\Picker;

$template = '{"username": "", "firstname": "", "email": ""}';

Picker::get($template, User::get());
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

The first parameter is the template. this can be json, an object or an associative array. The following code will produce the same result as the snippet above.

```php
$template = [
    "username" => "",
    "firstname" => "",
    "email" => ""
];

Picker::get($template, User::get());
```
The second parameter can be a model or collection. If the second parameter is a paginated collection meta data is include in the result

```php
$template = '{"username": "", "firstname": "", "email": ""}';

Picker::get($template, User::paginate(2));
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
    "meta" => [
        "count" => 2,
        "hasMorePages" => false,
        "per_page" => 2,
        "current_page" => 1,
        "last_page" => 1,
        "total" => 2
    ]
    "errors" => [
        "App\\User: attribute [email] access denied"
    ]
]
*/
```

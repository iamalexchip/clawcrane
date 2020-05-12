# Fetching data
----

## Example
To fetch get data from a model instance or collection we have to initialise a `Clawcrane` instance and use the get method of that instance to fetch data.

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
    ]
]
*/
```

## Class Constructor
The **haystack** (data we are fetching from) should be passed i the object Constructor

```php
$users = User::get();
$clawcrane = new ClawCrane($users);

// another example
$user = User::find(2);
$clawcrane = new ClawCrane($user);
```

## Get Method
The get method aaccepts a json string, associative array or object which will be used as the template. This can be json, an object or an associative array. The following templates will produce the same result as the snippet above.

```php
$template = '{"username": "", "firstname": "", "email": ""}';

// is the same as

$template = [
    "username" => "",
    "firstname" => "",
    "email" => ""
];

// and the same as

$template = (object) [
    "username" => "",
    "firstname" => "",
    "email" => ""
];
```

## Helper function
The library comes with a helper function `clawcrane` which can be used to create a new instance on the fly.

```php
$users = User::get();

// using the class 
$clawcrane = new ClawCrane($users);

// is the same as the following

$clawcrane = clawcrane($users);
```

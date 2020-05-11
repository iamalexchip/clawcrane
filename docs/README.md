# Laravel ClawCrane

ClawCrane is a set off laravel packages for bringing GraphQl like functionality to your Laravel APIs using your eloquent models. The packages allow you to do the following:

- Send object templates to you api endpoints and get responses matching your templates so that you only get what you want from the server

eg sending this

```json
{
    "id": "",
    "title": "",
    "summary": "",
    "author": {
        "username": "",
        "avatar": ""
    }
}
```
and getting the following response

```json
{
    "data": [
        {
            "id": "2",
            "title": "Laravel for beginners",
            "summary": "This a beninners tutorial to laravel...",
            "author": {
                "username": "zerochip",
                "avatar": "/assets/images/gdjr3r39803.png"
            }
        },
        {
            "id": "5",
            "title": "React for beginners",
            "summary": "Starting out in React...",
            "author": {
                "username": "somedude",
                "avatar": "/assets/images/5g44ej4943.png"
            }
        }
    ]
}
```

----

Other parts of the package are still a work in progress.

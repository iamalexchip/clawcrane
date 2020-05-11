# Laravel ClawCrane

Clawcrane is a laravel package for GraphQl like data fetching on your eloquent models. The packages works in the following way:

- Supply an object template

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
- Get the following output which matches you template

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

# Model properties
----

Before being able to us clawcrane on a model we have to define the properties that clawcrane can access in it. This is done by defining a method `clawcraneProps()` in the model. This should return an associative array.

```php
// app/User.php
public function clawcraneProps()
{
    return [
        'username' => ['value' => $this->name],
        'firstname' => ['value' => $this->firstname],
        'email' => [
            'value' => $this->email,
            'check' => false
        ],
    ];
}
```

Lets break down how we define props:
- The base array keys are those which clawcrane will see.
- `value` key in a property tells clawcrane where to get the value for that property. And is a required field
- `check` key in a property is a condition that clawcrane uses to **check** if the field should be shown. 

If we were to use the following template object

```json
{
    "username": "",
    "firstname": "",
    "email": ""
}
```
the result would be the following

{{todo}}[update error message]
```json
{
    "data": {
        "users": [
            {
                "username": "zerochip",
                "firstname": "Alex"
            },
            {
                "username": "johndoe",
                "firstname": "John"
            }
        ]
    }
}
```

Since email is set to false it is not included in the result. A condition can be used to make the email visible only to the account owner.

```php
// app/User.php
public function clawcraneProps()
{
    return [
        'username' => ['value' => $this->name],
        'firstname' => ['value' => $this->firstname],
        'email' => [
            'value' => $this->email,
            'check' => !Auth::guest() && $this->id == Auth::user()->id
        ]
    ];
}
```

Assuming the request was made by someone logged in as johndoe the result will now be as follows:

```json
{
    "data": {
        "users": [
            {
                "username": "zerochip",
                "firstname": "Alex"
            },
            {
                "username": "johndoe",
                "firstname": "John",
                "email": "johndoe24@mail.com"
            }
        ]
    }
}
```

?> Properties can be table columns accessors or relationships defined in the model.

---

Lets say a user has many posts. We also need to define a `clawcraneProps()` method in that model as well.

First we add a posts property to our `User` model.
```php
// app/User.php
public function clawcraneProps()
{
    return [
        'username' => ['value' => $this->name],
        'firstname' => ['value' => $this->firstname],
        'email' => [
            'value' => $this->email,
            'check' => !Auth::guest() && $this->id == Auth::user()->id
        ],
        'posts' => ['value' => $this->posts]
    ];
}
```

Then we define properties for our `Post` model.

```php
// app/Post.php
public function clawcraneProps()
{
    return [
        'title' => ['value' => $this->title],
        'summary' => ['value' => $this->summary],
        'posted_on' => [
            'value' => $this->posted_at->toDateString()
        ]
    ];
}
```

So if we are to use this template

```json
{
    "username": "",
    "posts": "",
}
```
We get

```json
{
    "data": {
        "users": [
            {
                "username": "zerochip",
                "posts": [
                    {
                        "title": "How to build a rest api in laravel",
                        "summary": "A guide on how to buid a rest api in laravel's architecture",
                        "posted_on": "2020-05-11"
                    },
                    {
                        "title": "Laravel from a to z - Controllers",
                        "summary": "A deep dive into laravel's controllers",
                        "posted_on": "2020-05-05"
                    }
                ],
            },
            {
                "username": "johndoe",
                "posts": []
            }
        ]
    }
}
```

Note how all properties in the post model are returned. We can define a nested template so we can fetch only the props that we need.

```json
{
    "username": "",
    "posts": {
        "title": ""
    },
}
```
The result

```json
{
    "data": {
        "users": [
            {
                "username": "zerochip",
                "posts": [
                    {
                        "title": "How to build a rest api"
                    },
                    {
                        "title": "Laravel from a to z - Controllers"
                    }
                ],
            },
            {
                "username": "johndoe",
                "posts": []
            }
        ]
    }
}
```
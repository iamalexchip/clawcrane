# Picker: Attributes
----

This option defines attributes can be accessed in the model. It must be an array with the key being the attribute name and the value being a boolean which sets the visibility

```php
// app/User.php
public function clawcraneAttributes()
{
    return [
        'username' => true,
        'firstname' => true,
        'email' => false
    ];
}
```
If we were to make a request with the following object template

```json
{
    "username": "",
    "firstname": "",
    "email": ""
}
```
the result would be the following

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
    },
    "errors": [
        "App\\User: attribute [created] access denied"
    ]
}
```
Since email is set to false it is not included in the result. A condition can be used to make the email visible only to the account owner.

```php
// app/User.php
public function clawcraneAttributes()
{
    return [
        'username' => true,
        'firstname' => true,
        'email' => !Auth::guest() && $this->id == Auth::user()->id
    ];
}
```
Assuming the request is being made by someone logged in as johndoe the result will now be as follows:

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

?> Attributes can be table columns, custom attributes, accessors or relationships defined in the model. 

The method `clawCraneAttributes` is required in related models.

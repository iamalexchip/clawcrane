# Model access
----
We can set a condition for checking if a model can be accessed. This is done by defining a method `clawcraneAccess()` that returns the access check result (preferably a boolean). In the follwoing example we are going to allow only the authors to view their post.

```php
// app/Post.php
public function clawcraneAccess()
{
    return $this->author_id == Auth::user()->id;
}
```

?> The check is done per model instance

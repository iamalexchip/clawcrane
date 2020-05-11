# Helpers
----

ClawCrane has a helper function and class so you can write less lines of code and keep things complex. Both the class and helper function share the same methods.

## Pick
This method is used to retrieve data from a model or collecton in the format of a given template similar to GraphQL. See docs [here](/picker/).

using class
```php
use ClawCrane\ClawCrane

$result = ClawCrane::pick($template, $collecton);
```

using helper function
```php
$result = clawCrane()->pick($template, $collecton);
```

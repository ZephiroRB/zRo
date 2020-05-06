zRo is a micro framework based on [workerman](https://github.com/walkor/workerman) helps you quickly write APIs with php.

# Install
It's recommended that you use Composer to install zRo.

`composer require zephirorb/zro`

# Usage
index.php
```php
<?php
use zRo\App;

require __DIR__ . '/vendor/autoload.php';

$zro = new App('http://0.0.0.0:3000');

$zro->count = 10; // process count

$zro->any('/', function ($request) {
    return 'Hello world';
});

$zro->get('/hello/{name}', function ($request, $name) {
    return "Hello $name";
});

$zro->start();
```

Run command `php index.php start -d` 

Going to http://127.0.0.1:3000/hello/world will now display "Hello world".

# Available commands
```
php index.php restart -d
php index.php stop
php index.php status
php index.php connections
```

# License
The zRo Framework is licensed under the MIT license. See License File for more information.

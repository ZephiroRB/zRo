zRo is a micro framework based on [workerman](https://github.com/walkor/workerman) helps you quickly write APIs with php.

# Install
It's recommended that you use Composer to install zRo.

`composer require zephirorb/zro`

# Usage
index.php
```php
<?php
require __DIR__ . '/vendor/autoload.php';

use zRo\App;

$zro = new App('http://0.0.0.0:3000');

// 10 processes
$zro->count = 10; 

$zro->any('/', function ($request) {
    return 'Hello world';
});

$zro->post('/articles/create', function ($request) {
    return json_encode(['code'=>0 ,'message' => 'ok']);
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


# Nginx

If you would like to use Nginx as reverse proxy or load balancer for your Comet app, insert into nginx.conf these lines:

```php
http {
 
    upstream app {
        server http://localhost:port;
    }
  
    server {
        listen 80;
         location / {
            proxy_pass         http://app;
            proxy_redirect     off;
        }
    }
}    
```

# License
The zRo Framework is licensed under the MIT license. See License File for more information.

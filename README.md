## API Framework Dewep

### Quick start:
```php
<?php

require_once './../vendor/autoload.php';

use Dewep\Application;
use Dewep\Config;

$config = Config::dirRoot() . '/config.yml';
$app = new Application($config);
$app->bootstrap();
```

### A sample configuration file [config.yml]:
To display in the response file in which the error occurred: 
```yaml
debug: true
```


The response format of the standard options:
```yaml
response: json | xml | html
```
If the response want to see your response format, then use your handler:
```yaml
response:
    head: 'application/json; charset=UTF-8'
    handler: Dewep\Parsers\Response::json
```
For processing routes a request, route support attribute - __/{user}/name__ - then you can use 
them in your code - __$request->getAttribute('user')__ . The request method with a comma
 __DELETE, GET, OPTIONS, PATCH, POST, PUT__ - the handler in the format __CLASS::method__

```yaml
routes:
  /{user}/name:
    GET,POST,PUT: Dewep\Demo::demo
  /:
    GET: Dewep\Demo::home
```
For pre and post processing queries and responses you can use `middleware`, 
each of which must contain a method of the host link 2 parameter - `handle($request, $response)`
```yaml
middleware:
  request:
    - Dewep\Middleware\Auth
  response:
    - Dewep\Middleware\Debug
```

For the realization of unrelated code, use the services providers, 
which can be activated at the moment they are first accessed in your code - `Container::get('mysql')->...`
```yaml
providers:
  logger: Dewep\Providers\LoggerProvider
  mysql: Dewep\Providers\MysqlProvider
```

Database encoder library
========================

This library allows you to fix database encoding.

Who has never seen some accented characters like "ActivÃ©" instead of "Activé"?

**Basic Docs**

* [Installation](#installation)
* [Encode tables](#encode-tables)
* [Encode database](#encode-database)
* [Contribution](#contribution)

<a name="installation"></a>

## Installation

Only 1 step:

### Download DatabaseEncoder using composer

Add DatabaseEncoder in your composer.json:

```js
{
    "require": {
        "mremi/database-encoder": "dev-master"
    }
}
```

Now tell composer to download the library by running the command:

``` bash
$ php composer.phar update mremi/database-encoder
```

Composer will install the library to your project's `vendor/mremi` directory.

**Feel free to dump your database before use this library.**

This library supports *dry run* mode: look at your logs, you will see SQL
queries which will be executed.

<a name="encode-tables"></a>

## Encode tables

```php
<?php

use Mremi\DatabaseEncoder\EncoderHandler;
use Mremi\DatabaseEncoder\MySql\MySqlEncoder;

use Monolog\Logger;

$conn    = new \PDO('mysql:host=localhost;dbname=db_name;charset=utf8;', 'db_user', 'db_password');
$logger  = new Logger('app');
$encoder = new MySqlEncoder($conn, $logger);
$handler = new EncoderHandler($encoder, $logger);

$handler->encodeTables(array(
   'table1' => array('column1_1', 'column1_2'),
   'table2' => array('column2_1', 'column2_2', 'column2_3'),
));
```

<a name="encode-database"></a>

## Encode database

```php
<?php

use Mremi\DatabaseEncoder\EncoderHandler;
use Mremi\DatabaseEncoder\MySql\MySqlEncoder;

use Monolog\Logger;

$conn    = new \PDO('mysql:host=localhost;dbname=db_name;charset=utf8;', 'db_user', 'db_password');
$logger  = new Logger('app');
$encoder = new MySqlEncoder($conn, $logger);
$handler = new EncoderHandler($encoder, $logger);

$handler->encodeDatabase();
```

<a name="contribution"></a>

## Contribution

Any question or feedback? Open an issue and I will try to reply quickly.

A feature is missing here? Feel free to create a pull request to solve it!

I hope this has been useful and has helped you. If so, share it and recommend
it! :)

[@mremitsme](https://twitter.com/mremitsme)

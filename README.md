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

You can also use the command provided by this library, look at the help message:

```bash
$ bin/encoder encode-tables --help
```

Some arguments are mandatory:

```bash
$ bin/encoder encode-tables "mysql:host=localhost;dbname=db_name;charset=utf8;" db_user db_password '{"table1":["column1_1","column1_2"],"table2":["column2_1","column2_2","column2_3"]}'
```

Some options are available:

```bash
$ bin/encoder encode-tables "mysql:host=localhost;dbname=db_name;charset=utf8;" db_user db_password '{"table1":["column1_1","column1_2"],"table2":["column2_1","column2_2","column2_3"]}' --options='{"1000":1}' --encoding=utf8 --dry-run
```

You can increase the log verbosity. The following example allows you to see the
SQL queries without execute them:

```bash
$ bin/encoder encode-tables "mysql:host=localhost;dbname=db_name;charset=utf8;" db_user db_password '{"table1":["column1_1","column1_2"],"table2":["column2_1","column2_2","column2_3"]}' --dry-run -vvv
```

```
[notice] Starting encoding (5 queries)...
[debug] Executed in 0 ms: UPDATE `table1` SET `column1_1` = CONVERT(CAST(CONVERT(`column1_1` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table1` SET `column1_2` = CONVERT(CAST(CONVERT(`column1_2` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table2` SET `column2_1` = CONVERT(CAST(CONVERT(`column2_1` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table2` SET `column2_2` = CONVERT(CAST(CONVERT(`column2_2` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table2` SET `column2_3` = CONVERT(CAST(CONVERT(`column2_3` USING latin1) AS BINARY) USING utf8) []
[notice] Done!
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

You can also use the command provided by this library, look at the help message:

```bash
$ bin/encoder encode-database --help
```

Some arguments are mandatory:

```bash
$ bin/encoder encode-database "mysql:host=localhost;dbname=db_name;charset=utf8;" db_user db_password
```

Some options are available:

```bash
$ bin/encoder encode-database "mysql:host=localhost;dbname=db_name;charset=utf8;" db_user db_password --options='{"1000":1}' --encoding=utf8 --dry-run
```

You can increase the log verbosity. The following example allows you to see the
SQL queries without execute them:

```bash
$ bin/encoder encode-database "mysql:host=localhost;dbname=db_name;charset=utf8;" db_user db_password --dry-run -vvv
```

```
[notice] Retrieving string columns...
[debug] Executed in 0 ms: SELECT DATABASE() []
[debug] Executed in 0 ms: SELECT `TABLE_NAME` AS table_name, `COLUMN_NAME` AS column_name
            FROM `information_schema`.`COLUMNS`
            WHERE
                `TABLE_SCHEMA` = :table_schema
                AND `CHARACTER_SET_NAME` = :character_set_name
            ORDER BY `TABLE_NAME` ["db_name","utf8"]
[notice] Starting encoding (9 queries)...
[debug] Executed in 0 ms: UPDATE `table1` SET `column1_1` = CONVERT(CAST(CONVERT(`column1_1` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table1` SET `column1_2` = CONVERT(CAST(CONVERT(`column1_2` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table2` SET `column2_1` = CONVERT(CAST(CONVERT(`column2_1` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table2` SET `column2_2` = CONVERT(CAST(CONVERT(`column2_2` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table2` SET `column2_3` = CONVERT(CAST(CONVERT(`column2_3` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table3` SET `column3_1` = CONVERT(CAST(CONVERT(`column3_1` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table3` SET `column3_2` = CONVERT(CAST(CONVERT(`column3_2` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table3` SET `column3_3` = CONVERT(CAST(CONVERT(`column3_3` USING latin1) AS BINARY) USING utf8) []
[debug] Executed in 0 ms: UPDATE `table3` SET `column3_4` = CONVERT(CAST(CONVERT(`column3_4` USING latin1) AS BINARY) USING utf8) []
[notice] Done!
```

<a name="contribution"></a>

## Contribution

Any question or feedback? Open an issue and I will try to reply quickly.

A feature is missing here? Feel free to create a pull request to solve it!

I hope this has been useful and has helped you. If so, share it and recommend
it! :)

[@mremitsme](https://twitter.com/mremitsme)

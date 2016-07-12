# Simple authentication and database skeleton

## General

Based of Zend Framework 2 Skeleton Application.
Created for demonstration purposes.

## Overview

Application has two access levels
- admins - can create users and give permissions
- guests - can open doors which are allowed  and check his log events

Implemented main page for admins. Admin can create new user and give him rights to open doors.
For guest is possible only to see his doors and log events which are related to him.

Logs are implemented in two ways:
- simple text log is available in `./data/log/app.log`
- database table `log_table` has records about door events

## Installation

1. Install all composer dependencies with `composer update`
2. Put your database connection credentials to `local.php`
`$dbParams = array(
     'database'  => 'yourdatabase',
     'username'  => 'youruser',
     'password'  => 'yourpassword',
     'hostname'  => '127.0.0.1',
     // buffer_results - only for mysqli buffered queries, skip for others
     'options' => array('buffer_results' => true)
 );`

3. For Doctrine create file `doctrine.local.php` with such contents
`return array(
     'doctrine' => array(
         'connection' => array(
             'orm_default' => array(
                 'driverClass' =>'Doctrine\DBAL\Driver\PDOMySql\Driver',
                 'params' => array(
                     'host'     => '127.0.0.1',
                     'port'     => '3306',
                     'user'     => 'youruser',
                     'password' => 'yourpassword',
                     'dbname'   => 'yourdatabase',
                 )
             )
         ),
     ),
 );`

4. To have working Zend Developer Tools toolbar put file `./vendor/zendframework/zend-developr-tools/config/zenddevelopertools.local.php.dist`
to './config/autoload/zenddevelopertools.local.php'

5. Run Doctrine queries:
- `./vendor/bin/doctrine-module orm:info` - will check your connection and mappings
- `./vendor/bin/doctrine-module orm:schema-tool:update --force` - will create database tables

6. Put initial admin account into database:
`insert into users_table (username, password, role) VALUES('admin', '$2y$10$5xDAll5YJtaFzQC6EhoVSeGrjtc708PApPowv4ounlBgmhuEPC.6S', 'admin')`
This will create admin account *admin:admin*

7. Your application is ready to use.

## PHPUnit

Tests available at `./tests/unit/Test` directory.

## Credits

[Vladyslav Semerenko](mailto:vladyslav.semerenko@gmail.com)
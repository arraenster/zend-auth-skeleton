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

## PHPUnit

Tests available at `./tests/unit/Test` directory.

## Credits

[Vladyslav Semerenko](mailto:vladyslav.semerenko@gmail.com)
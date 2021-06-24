# Changelog

All notable changes to `laravel-searchable` will be documented in this file

## 1.10.0 - 2021-06-24

- Allow searching with special characters (#117)

## 1.9.3 - 2021-may-the-forth

- changed query building order to play nice with forwarded calls (#101)

## 1.9.2 - 2021-05-03

- handle columns with reserved names (#110)

## 1.9.1 - 2020-12-27

- add support for PHP 8.0

## 1.9.0 - 2020-12-09

- add aspect search result limit (#82)

## 1.8.0 - 2020-11-28

- allow to use same query method (#81)

## 1.7.1 - 2020-11-10

- remove backticks in SearchAspect to support PostgreSQL (#85)

## 1.7.0 - 2020-09-08

- add support for Laravel 8

## 1.6.2 - 2020-04-29

- escape searchable attributes (#60)

## 1.6.1 - 2020-03-03

- revert #42

## 1.6.0 - 2020-03-03

- add support for Laravel 7
- fix issue when fuzzy searching model fields (#42)

## 1.5.0 - 2019-12-15

- allow applying query scopes and eager loading relationships (#44)

## 1.4.0 - 2019-09-04

- add support for Laravel 6

## 1.3.0 - 2019-02-27

- drop support for Laravel 5.7 and lower
- drop support for PHP 7.1 and lower

## 1.2.3 - 2019-02-27

- add support for Laravel 5.8
- add support for PHPUnit 8.0

## 1.2.2 - 2019-02-01

- use Str:: and Arr:: instead of helper methods

## 1.2.1 - 2019-01-14

- fix: the closure for registering a model search aspect can no longer be a global function

## 1.2.0 - 2019-01-11

- search with multiple keywords on multiple columns

## 1.1.0 - 2018-12-28

- add preferred `search()` method (alias for `perform()`)

## 1.0.1 - 2018-12-28

- fix passing an array of searchable model attributes to `registerModel`

## 1.0.0 - 2018-12-27

- initial release

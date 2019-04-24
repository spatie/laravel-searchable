# Changelog

All notable changes to `laravel-searchable` will be documented in this file

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

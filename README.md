# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-searchable.svg?style=flat-square)](https://packagist.org/packages/spatie/:package_name)
[![Build Status](https://img.shields.io/travis/spatie/laravel-searchable/master.svg?style=flat-square)](https://travis-ci.org/spatie/:package_name)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-searchable.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/:package_name)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-searchable.svg?style=flat-square)](https://packagist.org/packages/spatie/:package_name)


This is where your description should go. Try and limit it to a paragraph or two.

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-searchable
```

## Usage

Start by registering your search aspects. A search aspect is anything that can be searched through. Typically, you'll have a search aspect for every searchable model. However, search aspects are not limited to models. You can easily add a search aspect for an external API, list of files or an array of values.

### Searching model data

If you only want to search your models, we've made things super easy. You can register a model as a search aspects using the `Search::registerModel()` in the `boot` method of any service provider:

```php
public function boot()
{
    Search::registerModel(User::class, ['name', 'email']);
}
```

By default the properties you provide to the `Search::registerModel()` method will be used to fuzzy search the model's actual database properties. To add a property that's not fuzzy searchable you can use the `addSearchableProperty` method and pass `false` as the second parameter:

```php
Search::registerModel(User::class)
    ->addSearchableProperty('email', false) // only return results that exactly match the e-mail address
    ->addSearchableProperty('username'); // return results for partial matches on usernames
``` 

### Creating custom search aspects

You are not limited to only registering basic models as search aspects. You can easily create your own, custom search aspects by extending the `SearchAspect` class. After that you can register your custom search aspect using the `Search::registerSearchAspect()` method in any service provider.

Consider the following custom search aspect to search an external API:

```php

class OrderSearchAspect extends SearchAspect
{
    public function getResults(string $term, User $user): Collection
    {
        return OrderApi::searchOrders($term);
    }
}
```

```php
Search::registerSearchAspect(OrderSearchAspect::class);
``` 

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Alex Vanderbist](https://github.com/AlexVanderbist)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

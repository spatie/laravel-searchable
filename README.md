# 𝕷𝖆𝖗𝖆𝖛𝖊𝖑 𝕾𝖊𝖆𝖗𝖈𝖍𝖆𝖇𝖑𝖊

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-searchable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-searchable)
[![Build Status](https://img.shields.io/travis/spatie/laravel-searchable/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-searchable)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-searchable.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-searchable)
[![StyleCI](https://github.styleci.io/repos/160661570/shield?branch=master)](https://github.styleci.io/repos/160661570)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-searchable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-searchable)

This package makes it easy to get structured search from a variety of sources. Here's an example where we search through some models. We already did some small preparation on the models themselves.

```php
$searchResults = (new Search();
   ->registerModel(User::class, 'name');
   ->registerModel(BlogPost::class, 'title')
   ->search('john');
```

The search will be performed case insenstive. `$searchResults` now contains all `User` models that contain `john` in the `name` attribute and `BlogPost`s that contain 'john' in the `title` attribute.

In your view you can now loop over the search results:

```html
<h1>Search</h1>

There are {{ $searchResults->count() }} results.

@foreach($searchResults->groupByType() as $modelName => $modelSearchResults)
   <h2>{{ $modelName }}</h2>
   
   @foreach($modelSearchResults as $searchResult)
       <ul>
            <a href="{{ $searchResult->url }}">{{ $searchResult->name }}</a>
       </ul>
   @endforeach
@endforeach
```

In this example we used models, but you can easily add a search aspect for an external API, list of files or an array of values.


## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-searchable
```

## Usage

### Searching models

### Preparing your models

In order to search through models you'll have to let them implement the `Searchable` interface.

```php
namespace Spatie\Searchable;

interface Searchable
{
    public function getSearchResult(): SearchResult;
}
```

You'll only need to add a `getSearchResult` function that must return an instance of `SearchResult`. Here's how it could look like for a a blog post model.

```php
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class BlogPost extends Model implements Searchable
{
     public function getSearchResult(): SearchResult
     {
        $url = route('blogPost.show, $this->slug);
     
         return new \Spatie\Searchable\SearchResult(
            $this,
            $this->title,
            $url,
         );
     }
}
```


### Searching models

With the models prepared you can search them like this:

```php
$searchResults = (new Search();
   ->registerModel(User::class, 'name');
   ->search('john');
```

The search will be performed case insenstive. `$searchResults` now contains all `User` models that contain `john` in the `name` attribute and `BlogPost`s that contain 'john' in the `title` attribute.


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
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

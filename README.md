# Laravel Searchable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-searchable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-searchable)
![Test Status](https://img.shields.io/github/workflow/status/spatie/laravel-searchable/run-tests?label=tests&style=flat-square)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-searchable.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-searchable)
[![StyleCI](https://github.styleci.io/repos/160661570/shield?branch=master)](https://github.styleci.io/repos/160661570)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-searchable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-searchable)

This package makes it easy to get structured search from a variety of sources. Here's an example where we search through some models. We already did some small preparation on the models themselves.

```php
$searchResults = (new Search())
   ->registerModel(User::class, 'name')
   ->registerModel(BlogPost::class, 'title')
   ->search('john');
```

The search will be performed case insensitive. `$searchResults` now contains all `User` models that contain `john` in the `name` attribute and `BlogPost`s that contain 'john' in the `title` attribute.

In your view you can now loop over the search results:

```html
<h1>Search</h1>

There are {{ $searchResults->count() }} results.

@foreach($searchResults->groupByType() as $type => $modelSearchResults)
   <h2>{{ $type }}</h2>
   
   @foreach($modelSearchResults as $searchResult)
       <ul>
            <li><a href="{{ $searchResult->url }}">{{ $searchResult->title }}</a></li>
       </ul>
   @endforeach
@endforeach
```

In this example we used models, but you can easily add a search aspect for an external API, list of files or an array of values.

## Support us

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us). 

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-searchable
```

## Usage

### Preparing your models

In order to search through models you'll have to let them implement the `Searchable` interface.

```php
namespace Spatie\Searchable;

interface Searchable
{
    public function getSearchResult(): SearchResult;
}
```

You'll only need to add a `getSearchResult` method to each searchable model that must return an instance of `SearchResult`. Here's how it could look like for a blog post model.

```php
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class BlogPost extends Model implements Searchable
{
     public function getSearchResult(): SearchResult
     {
        $url = route('blogPost.show', $this->slug);
     
         return new \Spatie\Searchable\SearchResult(
            $this,
            $this->title,
            $url
         );
     }
}
```

### Searching models

With the models prepared you can search them like this:

```php
$searchResults = (new Search())
   ->registerModel(User::class, 'name')
   ->search('john');
```

The search will be performed case insensitive. `$searchResults` now contains all `User` models that contain `john` in the `name` attribute.

You can also pass multiple attributes to search through:

```php
// use multiple model attributes

$searchResults = (new Search())
   ->registerModel(User::class, 'first_name', 'last_name')
   ->search('john');
   
// or use an array of model attributes

$searchResults = (new Search())
   ->registerModel(User::class, ['first_name', 'last_name'])
   ->search('john');
```

To get fine grained control you can also use a callable. This way you can also search for exact matches, apply scopes, eager load relationships, or even filter your query like you would using the query builder.

```php
$search = (new Search())
   ->registerModel(User::class, function(ModelSearchAspect $modelSearchAspect) {
       $modelSearchAspect
          ->addSearchableAttribute('name') // return results for partial matches on usernames
          ->addExactSearchableAttribute('email') // only return results that exactly match the e-mail address
          ->active()
          ->has('posts')
          ->with('roles');
});
```

### Creating custom search aspects

You are not limited to only registering basic models as search aspects. You can easily create your own, custom search aspects by extending the `SearchAspect` class.

Consider the following custom search aspect to search an external API:

```php
class OrderSearchAspect extends SearchAspect
{
    public function getResults(string $term): Collection
    {
        return OrderApi::searchOrders($term);
    }
}
```

This is how you can use it:

```php
$searchResults = (new Search())
   ->registerAspect(OrderSearchAspect::class)
   ->search('john');
```

### Rendering search results

Here's an example on rendering search results:

```html
<h1>Search</h1>

There are {{ $searchResults->count() }} results.

@foreach($searchResults->groupByType() as $type => $modelSearchResults)
   <h2>{{ $type }}</h2>
   
   @foreach($modelSearchResults as $searchResult)
       <ul>
            <a href="{{ $searchResult->url }}">{{ $searchResult->title }}</a>
       </ul>
   @endforeach
@endforeach
```

You can customize the `$type` by adding a public property `$searchableType` on your model or custom search aspect

```php
class BlogPost extends Model implements Searchable
{
    public $searchableType = 'custom named aspect';
}
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Alex Vanderbist](https://github.com/AlexVanderbist)
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

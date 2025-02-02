# Sanitizer

![https://github.com/binary-cats/sanitizer/actions](https://github.com/binary-cats/sanitizer/workflows/Laravel/badge.svg)
![https://github.styleci.io/repos/316763653](https://github.styleci.io/repos/316763653/shield)
![https://scrutinizer-ci.com/g/binary-cats/sanitizer/](https://scrutinizer-ci.com/g/binary-cats/sanitizer/badges/quality-score.png?b=master)
[![Build Status](https://img.shields.io/travis/binary-cats/sanitizer/master.svg?style=flat-square)](https://travis-ci.org/binary-cats/sanitizer)

## Introduction

Sanitizer provides your Laravel application with an easy way to format user input, both through the provided filters or through custom ones that can easily be added to the Sanitizer library.

<p align="center"><img src="https://repository-images.githubusercontent.com/316763653/71c29400-3164-11eb-8a35-d4b16ac34253" width="600"></p>

## Example

Given a data array with the following format:

```php
    $data = [
        'first_name'    =>  'john',
        'last_name'     =>  '<strong>DOE</strong>',
        'email'         =>  '  JOHn@DoE.com',
        'birthdate'     =>  '06/25/1980',
        'jsonVar'       =>  '{"name":"value"}',
        'description'   =>  '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>',
        'phone'         =>  '+08(096)90-123-45q',
        'country'       =>  'GB',
        'postcode'      =>  'ab12 3de',
    ];
```
We can easily format it using our Sanitizer and the some of Sanitizer's default filters:
```php

    use calamar-mihai\Sanitizer\Sanitizer;

    $filters = [
        'first_name'    =>  'trim|escape|capitalize',
        'last_name'     =>  'trim|escape|capitalize',
        'email'         =>  'trim|escape|lowercase',
        'birthdate'     =>  'trim|format_date:m/d/Y, Y-m-d',
        'jsonVar'       =>  'cast:array',
        'description'   =>  'strip_tags',
        'phone'         =>  'digit',
        'country'       =>  'trim|escape|capitalize',
        'postcode'      =>  'trim|escape|uppercase|filter_if:country,GB',
    ];

    $sanitizer  = new Sanitizer($data, $filters);

    var_dump($sanitizer->sanitize());
```

Which will yield:
```php
    [
        'first_name'    =>  'John',
        'last_name'     =>  'Doe',
        'email'         =>  'john@doe.com',
        'birthdate'     =>  '1980-06-25',
        'jsonVar'       =>  '["name" => "value"]',
        'description'   =>  'Test paragraph. Other text',
        'phone'         =>  '080969012345',
        'country'       =>  'GB',
        'postcode'      =>  'AB12 3DE',
    ];
```
It's usage is very similar to Laravel's Validator module, for those who are already familiar with it, although Laravel is not required to use this library.

Filters are applied in the same order they're defined in the $filters array. For each attribute, filters are separered by | and options are specified by suffixing a comma separated list of arguments (see format_date).

## Available filters

The following filters are available out of the box:

 Filter  | Description
:---------|:----------
 **trim**   | Trims a string
 **escape**    | Escapes HTML and special chars using php's filter_var
 **lowercase**    | Converts the given string to all lowercase
 **uppercase**    | Converts the given string to all uppercase
 **capitalize**    | Capitalize a string
 **cast**           | Casts a variable into the given type. Options are: integer, float, string, boolean, object, array and Laravel Collection.
 **format_date**    | Always takes two arguments, the date's given format and the target format, following DateTime notation.
 **strip_tags**    | Strip HTML and PHP tags using php's strip_tags
 **digit**    | Get only digit characters from the string
 **filter_if** | Applies filters if an attribute exactly matches value

## Adding custom filters

You can add your own filters by passing a custom filter array to the Sanitize constructor as the third parameter. For each filter name, either a closure or a full classpath to a Class implementing the `calamar-mihai\Sanitizer\Contracts\Filter` interface must be provided.
```php

use calamar-mihai\Sanitizer\Contracts\Filter;

class RemoveStringsFilter implements Filter
{
    /**
     * Apply filter
     *
     * @param  mixed $value
     * @param  array  $options
     * @return string
     */
    public function apply($value, $options = [])
    {
        return str_replace($options, '', $value);
    }
}
```
Closures must always accept two parameters: $value and an $options array:
```php
$customFilters = [
    'hash'   =>  function($value, $options = []) {
            return sha1($value);
        },
    'remove_strings' => RemoveStringsFilter::class,
];

$filters = [
    'secret'    =>  'hash',
    'text'      =>  'remove_strings:Curse,Words,Galore',
];

$sanitize = new Sanitize($data, $filters, $customFilters);
```

## Install

To install, just run:

    composer require binary-cats/sanitizer

And you're done! If you're using Laravel, your application will autoamtically register Service provider, as well as the Sanitizer Facade:

If you prefer to do that manually, you need to add the values to your `config/app.php`:

```php
    'providers' => [
        ...
        calamar-mihai\Sanitizer\Laravel\SanitizerServiceProvider::class,
    ];

    'aliases' => [
        ...
        'Sanitizer' => calamar-mihai\Sanitizer\Laravel\Facade::class,
    ];
```

## Laravel goodies

If you are using Laravel, you can use the Sanitizer through the Facade:
```php
    $newData = \Sanitizer::make($data, $filters)->sanitize();
```

You can also easily extend the Sanitizer library by adding your own custom filters, just like you would the Validator library in Laravel, by calling extend from a ServiceProvider like so:

```php
    \Sanitizer::extend($filterName, $closureOrClassPath);
```

You may also Sanitize input in your own FormRequests by using the SanitizesInput trait, and adding a *filters* method that returns the filters that you want applied to the input.

```php
    namespace App\Http\Requests;

    use App\Http\Requests\Request;
    use calamar-mihai\Sanitizer\Laravel\SanitizesInput;

    class SanitizedRequest extends Request
    {
        use SanitizesInput;

        public function filters()
        {
            return [
                'name'  => 'trim|capitalize',
                'email' => 'trim',
                'text'  => 'remove_strings:Curse,Words,Galore',
            ];
        }

        public function customFilters()
        {
            return [
                'remove_strings' => RemoveStringsFilter::class,
            ];
        }

        /* ... */
```

To generate a Sanitized Request just execute the included Artisan command:

    php artisan make:sanitized-request TestSanitizedRequest

The only difference with a Laravel FormRequest is that now you'll have an extra 'fields' method in which to enter the input filters you wish to apply, and that input will be sanitized before being validated.

### License

Sanitizer is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Thanks

Many than for the original [WAAVI Sanitizer](https://github.com/Waavi/Sanitizer) which is the source for this repo. Unfortunately it does not appear to be maintained anymore.

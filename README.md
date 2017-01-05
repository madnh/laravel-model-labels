# LaravelModelLabels

Support get label of model's fields, from model::$labels property or Laravel's localization features

## Install
1. Add this package to `composer.json`

```shell
composer require madnh/laravel-model-labels
```

2. In model classes, use `MaDnh\LaravelModelLabels\LabelsTrait`

```php
use MaDnh\LaravelModelLabels\LabelsTrait;

class Country extends Model
{
    use SoftDeletes, LabelFieldTrait;
    
   //Model contents...
```

## Usage
### Label priority

Model cache > Laravel i18n > Model `static::$labels`

### Define labels

Label can store in a static property of model, or in a locale file. Labels in locale files will override model's static property

1. Define labels in model class

Define a static property named `$labels`, it is an array of labels, with key is fields name, value is label.
Label maybe a string or callable value. If it's a callable value, then result of it will used as label, that callable has 1 argument, it is the field need to get label.
  
```php
class Country extends Model
{
    use SoftDeletes, LabelFieldTrait;
    
    //...
    
    public static $labels = [
        'name' => 'Name of country',
        'flag' => function($field){
            return 'Flag ('.$field.')';
    }];
    
    //...
```

2. Define labels in locale files

Add file to site's locale folder, named `model_<model_name_in_snake_case>.php`.
This locale file return an array of string. Labels of model's fields store at a key named is `field`, it is an array of field name and label.

```php
<?php
return [
    'field' => [
        'name' => 'Tên quốc gia'
    ]
];
```

## Get labels

| **Method**                | **Example**                                          | **Description**             |
|---------------------------|------------------------------------------------------|-----------------------------|
| **labels()**              | `Country::labels()`                                  | Get all of lables of fields |
| **labels(<fields name>)** | `Country::labels('name', 'flag', 'code', ['title'])` | Get label of fields         |
| **label(<field name>)**   | `Country::label('name')`                             | Get label of a field        |

## Examples

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Country;

class CreateCountryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required'
        ];
    }
    public function attributes()
    {
        return Country::labels('name');
    }

```
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

## Properties

1. **`static::$label_path`**

Prefix of locale path, default is `model_<model_name_in_snake_case>`. Example:

```php
public static $label_path = 'flag'; //Default is model_flag
```
2. **`static::$labels`**

Model labels. Array of properties name (as keys) and labels (as value)

Example:
```php
public static $labels = [
    'id' => 'ID',
    'full_name' => 'Họ và tên'
];
```

3. **`static::$labels_trans_map`**

Label trans map, use when can't find label of a property in label cached, locale, static $labels.

If the property is not defined in this array, will use the auto convert function - which try to get the label in title case of lower cased property name: id => Id .

Special useful for acronym words like ID, VIP, CMND,..

Example: 
```php
public static $labels_trans_map = ['id' => 'ID']; //Auto convert label is Id 
```
     
4. **`static::$label_cached`**

Cached labels. Priority is highest.



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

Use following methods to get labels

| **Method**                | **Example**                                          | **Description**             |
|---------------------------|------------------------------------------------------|-----------------------------|
| **modelLabel()**          | `Country::modelLabel()`                              | Get label of model class    |
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
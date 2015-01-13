[![Build Status](https://travis-ci.org/imkrimerman/primitive.svg?branch=master)](https://travis-ci.org/imkrimerman/primitive) [![Latest Stable Version](https://poser.pugx.org/im/primitive/v/stable.svg)](https://packagist.org/packages/im/primitive) [![Latest Unstable Version](https://poser.pugx.org/im/primitive/v/unstable.svg)](https://packagist.org/packages/im/primitive) [![License](https://poser.pugx.org/im/primitive/license.svg)](https://packagist.org/packages/im/primitive)
Primitive
=========

PHP implementation of array and string in object-oriented style with features

Check [Wiki](https://github.com/imkrimerman/primitive/wiki) documentation

Installation
------------
Add a dependency to your project's composer.json file if you use [Composer](http://getcomposer.org/) to manage the dependencies of your project:
```json
{
    "require": {
        "im/primitive": "dev-master"
    }
}
```
Features
-----
Can be constructed from array, JSON, Container or even file (json or serialized array)
```php
$json  = '{
  "item": "someItem",
  "other": "otherItem",
  "nested": {
    "some": "thing"
  }
}';

$array = [ 0 => 'someItem',
          'other'  => 'otherItem'
          'nested' => 
                  ['some' => 'thing']
];

$containerEmpty     = new Container;
$containerFromArray = new Container($array);
$containerFromJson  = new Container($json);
$containerFromFile  = new Container('project/path/to/data.json');
```
Container always knows its length
```php
$length = $container->length;
```
Can be reverted to the saved state
```php
$array     = ['foo', ['bar' => 'foobar', 'key' => 'value'], 'bar' => 'baz'];
$container = new Container($array);

$container->forget('bar');
//`$container`
//['foo', ['bar' => 'foobar', 'key' => 'value']]
$container->revert();
//`$container`
//['foo', ['bar' => 'foobar', 'key' => 'value'], 'bar' => 'baz']

$container->where(['key' => 'value']);
//`$container`
//[1 => ['bar' => 'foobar', 'key' => 'value']]
$container->save()->first()->take('bar');
//`$container`
//[0 => 'foobar']

$container->revert();
//`$container`
//[1 => ['bar' => 'foobar', 'key' => 'value']]
```
[And great amount of other cool features](https://github.com/imkrimerman/primitive/wiki)

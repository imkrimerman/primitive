Primitive
=========

PHP implementation of array and string in object-oriented style with features

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
========
Length
------
Container always knows its length
```php
$length = $container->length;
//Result 3
```
You can check
```php
$container->isEmpty();
//or
$container->isNotEmpty();
```
Push
-----
```php
$container->push('value');
//or specify key to push 
$container->push('value', 'nested');
```
Pop
----
```php
$pop = $container->pop();
```
Unshift
-----
```php
$container->unshift('value');
```
Shift
-----
```php
$shift = $container->shift();
```
Find value
----
```php
$index = $container->find('someValue');
```
Has
----
```php
//Check if Container has value
$bool = $container->has('someItem');
```
Has key
----
```php
//Check if Container has key
$bool = $container->hasKey('item');
```
First key
-----
```php
//Get first key
$firstKey = $container->firstKey();
```
Last key
----
```php
//Get last key
$lastKey = $container->lastKey();
```
First value
----
```php
//Get first value and assign to Container (useful for chaining)
$container->first()
//or return first value
$first = $container->first(true);
```
Last value
----
```php
//Get last value and assign to Container (useful for chaining)
$container->last()
//or return last value
$last = $container->last(true);
```
Unique
----
```php
//Remove duplicated values
$container->unique();
```
Keys
----
```php
$keys = $container->keys();
```
Values
----
```php
$values = $container->values();
```
Keys and values divided
-----
```php
$divided = $container->divide();
//Result is new Container with keys and values 
//divided in two arrays with indexes 'keys' and 'values'
```
Shuffle
-----
```php
$container->shuffle();
```
Implode
----
```php
//Container will flatten all items and implode them
$string = $container->implode();
//You can specify delimiter (whitespace by default)
$string = $container->implode(',');
```
Chunk
----
```php
//Returns split Container into chunks each wrapped with new Container
$size = 3;
$chunked = $container->chunk($size);
```
Combine
----
```php
//You can specify what to combine 'keys' or 'values' with the second argument

```

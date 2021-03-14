# AttributeHelper

*AttributeHelper* is a utility class for working with attributes in PHP 8.

The helper methods allow you to quickly access information about what attributes a class, method or parameter has, including filtering via callback, and retrieve instances of those attributes.

You can also bulk execute all methods on a class that match a given list of attributes. This type of meta-programming can sometimes help create projects that are open to future extension without the need to alter existing code.

The same functionality from the static `AttributeHelper` class is also provided as a class trait named `HasAttribute`

### Method list

```php
// Returns the names of an items attributes
// If the attributes resolve to classes, these will be qualified class names
AttributeHelper::getAttributes($item) : array
```

```php
// Returns an instance of the given attribute from an item or null if the item does not have the attribute requested or attribute does not resolve to a class
// Optional index parameter can be provided when dealing with repeated attributes to choose which of the available options should be returned
AttributeHelper::getAttributeInstance($item, $attribute, $index = 0) : ?object
```

```php
// Returns an array with instances of all an items attributes that resolve to classes
AttributeHelper::getAttributeInstances($item) : array
```

```php
// Returns an array with instances of all an items attributes that resolve to classes filtered by the provided callback
AttributeHelper::getAttributeInstancesCallback(mixed $item, callable $callback) : array
```

```php
// Returns the names of methods on the provided class that have the attributes in the provided list
AttributeHelper::getClassMethodsWithAttributes(object|string $objectOrClass, array $attributesList) : array
```

```php
// Returns true / false whether an item has a given attribute
AttributeHelper::hasAttribute(mixed $item, string $attribute) : bool
```

```php
// Returns true / false whether an item has a given attribute and that the provided callback function returns when passed the matched attribute
AttributeHelper::hasAttributeCallback(mixed $item, object|string $attribute, callable $callback) : bool
```

```php
// Returns true / false whether an item has all of the attributes in the provided list
AttributeHelper::hasAllOfTheseAttributes(mixed $item, array $attributesList) : bool
```

```php
// Returns true / false whether an item has one of the attributes in the provided list
AttributeHelper::hasOneOfTheseAttributes(mixed $item, array $attributesList) : bool
```

```php
// Returns true / false whether an item has an exact list of attributes. If the item has additional attributes beyond the list in question, this method returns false.
AttributeHelper::hasExactlyTheseAttributes(mixed $item, array $attributesList) : bool
```

```php
// Returns true when the given item does not have any of the attributes in the provided list.
AttributeHelper::doesNotHaveTheseAttributes(mixed $item, array $attributesList) : bool
```

```php
// Returns true when the given class contains methods which have the attributes in the provided list.
AttributeHelper::classHasMethodsWithAttributes(object|string $objectOrClass, array $attributesList) : bool
```

```php
// Sequentially calls all methods on a given object that have the attributes in the provided list.
// Returns an array, indexed by method name, with the result of each method called.
AttributeHelper::callClassMethodsWithAttributes(object|string $objectOrClass, array $attributesList, array $methodArguments = array()) : array
```

## HasAttributes trait

The *HasAttribute* trait allows you to use the functions as methods on an object.

### Example of using the trait:
``` php

#[\Attribute]
class MyAttribute { }

#[MyAttribute]
class MyClass 
{    
    use HasAttributes;
}

$c = new MyClass();
$c->hasAttribute(MyAttribute::class); // returns true

```
### *HasAttribute* trait methods

```php
getAttributes() : array
```

```php
getAttributeInstance(string $attribute) : ?object
```

```php
getAttributeInstances() : array
```

```php
getAttributeInstancesCallback(callable $callback) : array
```

```php
getMethodsWithAttributes(array $attributes) : array
```

```php
hasAttribute(string $attribute) : bool
```

```php
hasAttributeCallback(string $attribute, callable $callback) : bool
```

```php
hasOneOfTheseAttributes(array $attributes) : bool
```

```php
hasAllOfTheseAttributes(array $attributes) : bool
```

```php
hasExactlyTheseAttributes(array $attributes) : bool
```

```php
doesNotHaveTheseAttributes(array $attributes) : bool
```

```php
hasMethodsWithAttributes(array $attributes) : bool
```

```php
callMethodsWithAttributes(array $attributes, array $methodArgs = array()) : array
```


## Bulk method execution example

Bulk method execution allows you to attach an open ended number of actions to events or conditions defined by attributes.

**Example:**

Imagine you have two classes, `ItemA` and `ItemB`

ItemA looks like this:
```
class ItemA implements Updateable {

    public function update() : void
    {
        // ...
    }

    #[DoAfterUpdate]
    public function sendAnEmail()
    {
        // ...
    }

    #[DoAfterUpdate]
    public function writeToLog()
    {
        // ...
    }
}
```

And ItemB looks like this:

```
class ItemB implements Updateable {

    public function update() : void
    {
        // ...
    }

    #[DoAfterUpdate]
    public function showConfirmation()
    {   
        // ...
    }
}

```

Now you can write a block of code like the following:

``` php
$items = [new ItemA(), new ItemB()];

foreach($items as $item) {
    $item->update();
    AttributeHelper::callMethodsWithAttributes($item, ['DoAfterUpdate]);
}
```

When executed, the second line in the loop will call the methods *sendAnEmail()* and *writeToLog()* on the instance of `ItemA` and *showConfirmation()* on the instance of `ItemB`. 

If at some later time we wanted to add more functionality to be run after update on `ItemB`, we'd only have to write a new class method and assign it the #[DoAfterUpdate] attribute, avoiding the need to alter any surrounding code.




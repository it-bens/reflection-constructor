# The Reflection Constructor
The reflection constructor (or should it be names ConstructorReflection?) can be used 
to find the name of parameter that matches a given type.

Why is that useful? Well, e.g. it can be used to inject data into a payload, if you only know the data
(or its type), but not the name of the payload property.

## How can the ReflectionConstructor be used?
The ReflectionConstructor is constructed with the class name of the object it should reflect.
```php
use ITB\ReflectionConstructor\ReflectionConstructor;

$constructor = new ReflectionConstructor(SomeClass::class);
```

The class provides two methods to extract the parameter name.
```php
$parameterName = $constructor->extractParameterNameForClassName(SomeOtherClass::class);
// or
$someObject = new SomeOtherClass();
$parameterName = $constructor->extractParameterNameForObject($someObject)
```

## What if two parameters share the same type?
Let's imagine there is a class like this:
```php
class SomeClass {
    public function __construct(Type1 $propertyOne, Type2 $propertyTwo, Type2 $propertyThree) {
        // ...
    }
}
```
This would work:
```php
$constructor = new ReflectionConstructor(SomeClass::class);
$parameterName = $constructor->extractParameterNameForClassName(Type1::class);
// $parameterName = 'propertyOne'
```

But this would lead to an exception:
```php
$constructor = new ReflectionConstructor(SomeClass::class);
$parameterName = $constructor->extractParameterNameForClassName(Type2::class);
```
The parameters 'parameterTwo' and 'parameterThree' share the same type. The resulting parameter name would be ambiguous.

That's why a list of excluded/ignored parameters can be passed to the methods. This is working again:
```php
$constructor = new ReflectionConstructor(SomeClass::class);
$parameterName = $constructor->extractParameterNameForClassName(Type2::class, ['propertyTwo']);
// $parameterName = 'propertyThree'
```

## Contributing
I am really happy that the software developer community loves Open Source, like I do! ♥

That's why I appreciate every issue that is opened (preferably constructive)
and every pull request that provides other or even better code to this package.

You are all breathtaking!
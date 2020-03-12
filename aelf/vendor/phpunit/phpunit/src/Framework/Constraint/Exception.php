phpDocumentor\Reflection\Types\String_` and one of type 
`\phpDocumentor\Reflection\Types\Integer`.

The real power of this resolver is in its capability to expand partial class names into fully qualified class names; but in order to do that we need an additional `\phpDocumentor\Reflection\Types\Context` class that will inform the resolver in which namespace the given expression occurs and which namespace aliases (or imports) apply.

### Resolving nullable types

Php 7.1 introduced nullable types e.g. `?string`. Type resolver will resolve the original type without the nullable notation `?`
just like it would do without the `?`. After that the type is wrapped in a `\phpDocumentor\Reflection\Types\Nullable` object.
The `Nullable` type has a method to fetch the actual type. 

## Resolving an FQSEN

A Fully Qualified Structural Element Name is a reference to another element in your code bases and can be resolved using the `\phpDocumentor\Reflection\FqsenResolver` class' `resolve` method, like this:

```php
$fqsenResolver = new \phpDocumentor\Reflection\FqsenResolver();
$fqsen = $fqsenResolver->resolve('\phpDocumentor\Reflection\FqsenResolver::resolve()');
```

In this example we resolve a Fully Qualified Structural Element Name (meaning that it includes the full namespace, class name and element name) and receive a Value Object of type `\phpDocumentor\Reflection\Fqsen`.

The real power of this resolver is in its capability to expand partial element names into Fully Qualified Structural Element Names; but in order to do that we need an additional `\phpDocumentor\Reflection\Types\Context` class that will inform the resolver in which namespace the given expression occurs and which namespace aliases (or imports) apply.

## Resolving partial Classes and Structural Element Names

Perhaps the best feature of this library is that it knows how to resolve partial class names into fully qualified class names.

For example, you have this file:

```php
namespace My\Example;

use phpDocumentor\Reflection\Types;

class Classy
{
    /**
     * @var Types\Context
     * @see Classy::
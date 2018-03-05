# Dancer library

Dancer is an experimental project aiming to automate Entity-to-DTO conversion, 
preserving layer separation and reducing the need to write boilerplate code at the same time.

## Basic usage

```
$dto = Dancer::buildDtoFor($entity);
```

This gives you a simple DTO object of **Morgriem\Dancer\DtoInterface** type suitable for serialization and passing between application layers.

The DTO contains the following fields:
1. Values extracted from entity getter-properties (methods either **getSomething()** or **isSomething()** form).
Field names are lowercased method shortnames, e.g. **$entity->getSomethingImportant()** becomes **$dto->somethingImportant**;
2. Values extracted from entity public fields.

## Advanced usage

Sometimes, you may need to include some computed properties, modify existing properties or exclude some properties from original Entity.
To achieve this goals, you need to declare a custom DTO class:

```
class CustomDto extends Morgriem\Dancer\Dto
{
    //Assuming someProperty is present in entity
    public function getSomeProperty() {
        //return somePropery shadowed value.
    }

    public function getComputedProperty() {
        $entity = $this->originalEntity();
        //...compute and return necessary value;
    }
    
    protected static function excludedProperties(): array {
        return [
            'excludedProperty',
            'anotherExcludedProperty',
        ];
    }
}
```

and then use it with the following call:
```
$dto = Dancer::buildDto($entity, CustomDto::class);
```


The precedence of properties in resulting DTO (in case of overlap) is following (from highest precedence to lowest):
1. Computed/shadowed properties in DTO class
2. Public fields in Entity
3. Getters in Entity

## Future plans

1. Add option to manipulate property name conversions, e.g. to snake case;
2. Consider adding static cache to speed up conversions.
# Upgrade to 3.0

## PHP 8.2 and statemachine 3.0

This version requires PHP 8.2 and `metabor/statemachine` ~3.0.0. All interfaces of
MetaborStd carry native types now, so every implementation in this bridge was adjusted.

## Mapping moved from annotations to attributes

Doctrine ORM 3 does not read annotations any more. All mappings are PHP 8 attributes now,
which both ORM 2.9+ and ORM 3 understand — this package allows `^2.20 || ^3.0`.

If you configure the mapping yourself, change the type:

```yml
doctrine:
    orm:
        mappings:
          statemachine:
            type: attribute   # was: annotation
```

## `Subject::$otherObservers` is no longer persisted

Observers that are not Doctrine entities used to be stored in a column of DBAL type
`object`. That type was removed in DBAL 4: it serialized arbitrary objects and restored
them with `unserialize()` on load. The field is a runtime field now — attach non-entity
observers in code, as before, but they are not written to the database.

**Schema change:** the corresponding column disappears.

## Metadata columns are `json` instead of `array`

`Event::$metadata` and `State::$metadata` used DBAL type `array`, which was removed in
DBAL 4 as well. They use `json` now.

**Schema change:** existing rows written with the `array` type were PHP-serialized and
cannot be read as JSON. There is no automatic migration.

# Upgrade to 1.1

## optional visualizing of the process graph by using clue/graph 

To reduce dependencies, the visualizing of the process graph by using clue/graph is now optional.
If you want to display process-graphs with GraphViz you have to add the clue/graph library to your
composer.json

```JSON
{
    "require": {
        "clue/graph": "~0.7"
    }
}
```

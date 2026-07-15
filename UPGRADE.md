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

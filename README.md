DoctrineBridge
==============

Doctrine implementation of the MetaborStd (Statemachine)

If you want to use it in Symfony add this to your Doctrine configuration:

```yml
# config/packages/doctrine.yaml
doctrine:
    orm:
        mappings:
            statemachine:
                type: attribute
                prefix: Metabor\Bridge\Doctrine
                dir: "%kernel.project_dir%/vendor/metabor/statemachine-doctrine-bridge/src/Metabor/Bridge/Doctrine"
                alias: Statemachine
                is_bundle: false
```

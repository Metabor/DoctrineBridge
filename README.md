DoctrineBridge
==============

Doctrine implementation of the MetaborStd (Statemachine)

If you want to use it in Symfony2 add this to your config.yml:

```yml
# {# app/config/config.yml #}
  doctrine:
      orm:
          mappings:
            statemachine:
              type: annotation
              prefix: Metabor\Bridge\Doctrine
              dir: "%kernel.root_dir%/../vendor/metabor/statemachine-doctrine-bridge/src/Metabor/Bridge/Doctrine"
              alias: Statemachine
              is_bundle: false

# Upgrading to metabor/statemachine-doctrine-bridge 3.0

This document describes breaking changes introduced in version 3.0 and how to update your code.

The jump is large: the previous release is v1.1.2 from 2016. There is no 2.x of this bridge — the version now follows `metabor/statemachine`.

## Requirements

- **PHP 8.2+** (previously PHP 5.3+)
- **`metabor/statemachine` ~3.0.0** (previously ~1.1.0)
- **`metabor/metabor-std` ~3.0.0** (previously >=1.1.4)
- **`doctrine/orm` ^2.20 || ^3.0** (previously >=2.4.0)
- **`doctrine/dbal` 4** comes with ORM 3 and removes two column types this bridge used
- **PHPUnit 11** for development/testing (previously `*`)

---

## Breaking Changes

### 1. Doctrine mapping moved from annotations to attributes

Doctrine ORM 3 does not read annotations. All 44 mappings in this package are PHP 8 attributes now.

This is not cosmetic. Before the change, the classes still loaded — and `AttributeDriver::isTransient()` reported every one of them as *not an entity*. A bridge Doctrine cannot see is silently useless: no error, no warning, no mapping.

```php
// Before
/**
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="class_name", type="string")
 */
class Subject implements \SplSubject
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

// After
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'class_name', type: 'string')]
class Subject implements \SplSubject
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
```

**Attributes do not lock out ORM 2 users.** They are understood from ORM 2.9 onwards, which is why this package allows `^2.20 || ^3.0`.

**If you configure the mapping yourself**, change the driver type:

```yml
# Before
doctrine:
    orm:
        mappings:
          statemachine:
            type: annotation
            dir: "%kernel.root_dir%/../vendor/metabor/statemachine-doctrine-bridge/src/Metabor/Bridge/Doctrine"

# After
doctrine:
    orm:
        mappings:
          statemachine:
            type: attribute
            dir: "%kernel.project_dir%/vendor/metabor/statemachine-doctrine-bridge/src/Metabor/Bridge/Doctrine"
```

---

### 2. `Subject::$otherObservers` is no longer persisted

**Schema change. Data is dropped.**

Observers that are not Doctrine entities were stored in a column of DBAL type `object`. That type was removed in DBAL 4: it serialized arbitrary objects and restored them with `unserialize()` when loading, which is a well-known attack surface. A custom replacement type would put exactly that back.

The field is runtime-only now. Attach non-entity observers in code as before — they are simply not written to the database.

```php
// Before
/**
 * @var \SplObjectStorage
 *
 * @ORM\Column(type="object")
 */
private $otherObservers;

// After
private \SplObjectStorage $otherObservers;
```

**Migration:** drop the corresponding column. If you relied on non-entity observers surviving a reload, make them entities extending `Metabor\Bridge\Doctrine\Observer\Observer` — those are persisted through the `ManyToMany` association and always were.

---

### 3. Metadata columns use `json` instead of `array`

**Schema change. Existing rows are unreadable.**

`Event::$metadata` and `State::$metadata` used DBAL type `array`, removed in DBAL 4 for the same reason as `object`.

```php
// Before
/**
 * @ORM\Column( type="array" )
 */
private $metadata = array();

// After
#[ORM\Column(type: 'json')]
private array $metadata = [];
```

**Migration:** rows written with the `array` type are PHP-serialized and cannot be read as JSON. There is no automatic conversion. To keep existing data, read it with the old library version, then write it back after the upgrade — or convert in SQL with a one-off script that `unserialize()`s the old value and stores `json_encode()` of it.

---

### 4. All implementations carry native type declarations

`MetaborStd` 3.0 gives every interface method native types. This bridge implements eleven of them, so every signature changed. **If you extend or override any class from this package, your signatures must follow.**

#### `Metabor\Bridge\Doctrine\Observer\Subject` (`\SplSubject`)

```php
// Before
public function getId()
public function attach(\SplObserver $observer)
public function detach(\SplObserver $observer)
public function getObservers()
public function notify()

// After
public function getId(): ?int
public function attach(\SplObserver $observer): void
public function detach(\SplObserver $observer): void
public function getObservers(): \Traversable
public function notify(): void
```

#### `Metabor\Bridge\Doctrine\Event\Event` (`EventInterface`, `MetadataInterface`, `\ArrayAccess`)

```php
// Before
public function getName()
public function getInvokeArgs()
final public function __invoke()
public function getMetadata()
public function offsetExists($offset)
public function offsetGet($offset)
public function offsetSet($offset, $value)
public function offsetUnset($offset)

// After
public function getName(): string
public function getInvokeArgs(): array
final public function __invoke(): mixed
public function getMetadata(): array
public function offsetExists(mixed $offset): bool
public function offsetGet(mixed $offset): mixed
public function offsetSet(mixed $offset, mixed $value): void
public function offsetUnset(mixed $offset): void
```

#### `Metabor\Bridge\Doctrine\Statemachine\Process` (`ProcessInterface`)

```php
// Before
public function __construct($name = null, State $initialState = null)
public function getName()
public function getInitialState()
public function getStates()
public function getState($name)
public function hasState($name)
public function addState(State $state)
public function removeState(State $state)

// After
public function __construct(?string $name = null, ?State $initialState = null)
public function getName(): string
public function getInitialState(): StateInterface
public function getStates(): \Traversable
public function getState(string $name): StateInterface
public function hasState(string $name): bool
public function addState(State $state): void
public function removeState(State $state): void
```

#### `Metabor\Bridge\Doctrine\Statemachine\State` (`StateInterface`, `MetadataInterface`, `\ArrayAccess`)

```php
// Before
public function __construct($name = null, Process $process = null)
public function getName()
public function __toString()
public function getEvent($name)
public function getEventNames()
public function getTransitions()
public function hasEvent($name)
public function getMetadata()

// After
public function __construct(?string $name = null, ?Process $process = null)
public function getName(): string
public function __toString(): string
public function getEvent(string $name): EventInterface
public function getEventNames(): \Traversable|array
public function getTransitions(): \Traversable
public function hasEvent(string $name): bool
public function getMetadata(): array
```

#### `Metabor\Bridge\Doctrine\Statemachine\Transition` (`TransitionInterface`, `WeightedInterface`)

```php
// Before
public function __construct(State $sourceState = null, State $targetState = null, Event $event = null, $conditionName = null)
public function getTargetState()
public function getEventName()
public function getConditionName()
public function setEvent(Event $event = null)
public function isActive($subject, \ArrayAccess $context, EventInterface $event = null)
public function getWeight()
public static function setExpressionLanguage(ExpressionLanguage $expressionLanguage = null)

// After
public function __construct(
    ?State $sourceState = null,
    ?State $targetState = null,
    ?Event $event = null,
    ?string $conditionName = null
)
public function getTargetState(): StateInterface
public function getEventName(): ?string
public function getConditionName(): ?string
public function setEvent(?Event $event = null): void
public function isActive(object $subject, \ArrayAccess $context, ?EventInterface $event = null): bool
public function getWeight(): float
public static function setExpressionLanguage(?ExpressionLanguage $expressionLanguage = null): void
```

#### `Metabor\Bridge\Doctrine\Statemachine\Detector` (`ProcessDetectorInterface`, `StateNameDetectorInterface`)

```php
// Before
public function detectProcess($subject)
public function detectCurrentStateName($subject)

// After
public function detectProcess(object $subject): ProcessInterface
public function detectCurrentStateName(object $subject): ?string
```

**Behaviour change:** `detectProcess()` returned `null` for a subject that is not a `StatefulEntity`. `ProcessDetectorInterface::detectProcess()` declares a non-nullable return type, so it throws `\InvalidArgumentException` now. `detectCurrentStateName()` still returns `null` — its interface declares `?string`.

#### `Metabor\Bridge\Doctrine\Statemachine\StatefulEntity` (`\SplObserver`)

```php
// Before
public function update(\SplSubject $subject)
public function triggerEvent($eventName, \ArrayAccess $context = null)
public function checkTransitions(\ArrayAccess $context = null)

// After
public function update(\SplSubject $subject): void
public function triggerEvent(string $eventName, ?\ArrayAccess $context = null): void
public function checkTransitions(?\ArrayAccess $context = null): void
```

#### `Metabor\Bridge\Doctrine\Statemachine\Command` (`\SplObserver`)

```php
// Before
public function update(\SplSubject $subject)

// After
public function update(\SplSubject $subject): void
```

---

## Migration Steps

1. **Update `composer.json`** to require PHP 8.2 and the new package version:
   ```json
   {
       "require": {
           "php": ">=8.2",
           "metabor/statemachine-doctrine-bridge": "~3.0"
       }
   }
   ```

2. **Change your mapping driver from `annotation` to `attribute`** if you configure it yourself. Without this, Doctrine silently maps nothing — the classes still load, so nothing tells you.

3. **Add types to all methods** that extend or override classes from this package. `Transition` and `Command` subclasses are the usual candidates. PHPStan or your IDE will list them.

4. **Handle `detectProcess()` throwing** instead of returning `null` if you pass subjects that are not `StatefulEntity`.

5. **Migrate the metadata columns** from PHP-serialized to JSON, or accept that existing metadata is lost.

6. **Drop the `otherObservers` column** and make any observer that must survive a reload a `Metabor\Bridge\Doctrine\Observer\Observer` entity.

7. **Update your schema** and verify Doctrine actually sees the entities:
   ```bash
   composer update
   php vendor/bin/phpunit
   php bin/console doctrine:schema:update --dump-sql
   ```
   If `--dump-sql` prints nothing for the statemachine tables, the mapping driver is still on `annotation`.

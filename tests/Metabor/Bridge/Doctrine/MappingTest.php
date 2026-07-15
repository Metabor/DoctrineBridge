<?php

namespace Metabor\Bridge\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;

/**
 * Prueft, dass Doctrine die Entities dieser Bruecke ueberhaupt sieht und ein Schema
 * daraus bauen kann.
 *
 * Warum es diesen Test gibt: Die uebrigen Tests dieser Suite fassen Doctrine nicht an —
 * sie pruefen die Klassen als reines PHP. Damit koennen sie die Fehlerklasse nicht sehen,
 * an der diese Bruecke tatsaechlich zerbrochen ist:
 *
 *   - Die Mappings standen als Annotations im Docblock. Doctrine ORM 3 liest die nicht
 *     mehr. Die Klassen luden weiterhin einwandfrei, alle Tests blieben gruen — und
 *     Doctrine sah keine einzige Entity.
 *   - Der DBAL-Typ "object" (Subject::$otherObservers) und "array" (Event::$metadata,
 *     State::$metadata) existieren in DBAL 4 nicht mehr.
 *
 * Beides faellt erst auf, wenn man Doctrine wirklich hochfaehrt. Genau das tut dieser Test.
 */
class MappingTest extends TestCase
{
    private function entityManager(): EntityManager
    {
        $config = ORMSetup::createAttributeMetadataConfiguration([__DIR__ . '/../../../../src'], true);
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $config);

        return new EntityManager($connection, $config);
    }

    public function testDoctrineFindetAlleEntities(): void
    {
        $metadata = $this->entityManager()->getMetadataFactory()->getAllMetadata();

        $namen = array_map(static fn (ClassMetadata $m): string => $m->getName(), $metadata);
        sort($namen);

        $this->assertSame([
            'Metabor\Bridge\Doctrine\Event\Event',
            'Metabor\Bridge\Doctrine\Observer\Observer',
            'Metabor\Bridge\Doctrine\Observer\Subject',
            'Metabor\Bridge\Doctrine\Statemachine\Process',
            'Metabor\Bridge\Doctrine\Statemachine\State',
            'Metabor\Bridge\Doctrine\Statemachine\Transition',
        ], $namen);
    }

    public function testSchemaLaesstSichBauen(): void
    {
        $em = $this->entityManager();
        $sql = (new SchemaTool($em))->getCreateSchemaSql($em->getMetadataFactory()->getAllMetadata());

        $this->assertNotEmpty($sql);

        $tabellen = [];
        foreach ($sql as $anweisung) {
            if (preg_match('/CREATE TABLE (\w+)/', $anweisung, $treffer)) {
                $tabellen[] = $treffer[1];
            }
        }

        // Die Verknuepfungstabelle beweist, dass die ManyToMany-Mappings gelesen wurden.
        $this->assertContains('state_event', $tabellen);
        $this->assertContains('Transition', $tabellen);
        $this->assertContains('Process', $tabellen);
    }

    public function testMetadatenFeldIstJsonUndNichtDerEntfernteArrayTyp(): void
    {
        $md = $this->entityManager()->getClassMetadata('Metabor\Bridge\Doctrine\Event\Event');

        $this->assertSame('json', $md->getTypeOfField('metadata'));
    }

    /**
     * otherObservers wird bewusst nicht mehr persistiert — der DBAL-Typ "object" ist weg.
     * Der Test haelt die Entscheidung fest, damit sie nicht versehentlich zurueckkommt.
     */
    public function testLaufzeitBeobachterWerdenNichtPersistiert(): void
    {
        $md = $this->entityManager()->getClassMetadata('Metabor\Bridge\Doctrine\Observer\Subject');

        $this->assertFalse($md->hasField('otherObservers'));
        $this->assertTrue($md->hasAssociation('entityObservers'));
    }
}

<?php
namespace Metabor\Bridge\Doctrine\Observer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Oliver Tischlinger
 */
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'class_name', type: 'string')]
class Subject implements \SplSubject
{
    const ENTITY_NAME = __CLASS__;

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    /** @var Collection<int, Observer> */
    #[ORM\ManyToMany(targetEntity: Observer::class, inversedBy: 'entitySubjects', cascade: ['persist'])]
    private Collection $entityObservers;

    /**
     * Beobachter, die keine Doctrine-Entities sind — zur Laufzeit angehaengt.
     *
     * NICHT persistiert (frueher @ORM\Column(type="object")). Der DBAL-Typ "object"
     * existiert seit DBAL 4 nicht mehr: Er hat beliebige Objekte serialisiert und beim
     * Laden per unserialize() wiederhergestellt, was eine bekannte Angriffsflaeche ist.
     * Ein eigener Typ als Ersatz wuerde genau das wieder einbauen.
     */
    private \SplObjectStorage $otherObservers;

    public function __construct()
    {
        $this->entityObservers = new ArrayCollection();
        $this->otherObservers = new \SplObjectStorage();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @see \SplSubject::attach()
     */
    public function attach(\SplObserver $observer): void
    {
        if ($observer instanceof Observer) {
            $this->entityObservers->add($observer);
        } else {
            $this->otherObservers->offsetSet($observer, null);
        }
    }

    /**
     * @see \SplSubject::detach()
     */
    public function detach(\SplObserver $observer): void
    {
        if ($observer instanceof Observer) {
            $this->entityObservers->removeElement($observer);
        } else {
            $this->otherObservers->offsetUnset($observer);
        }
    }

    public function getObservers(): \Traversable
    {
        $iterator = new \AppendIterator();
        $iterator->append($this->entityObservers->getIterator());
        $iterator->append($this->otherObservers);

        return $iterator;
    }

    /**
     * @see \SplSubject::notify()
     */
    public function notify(): void
    {
        /* @var $observer \SplObserver */
        foreach ($this->getObservers() as $observer) {
            $observer->update($this);
        }
    }
}

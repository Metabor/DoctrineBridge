<?php
namespace Metabor\Bridge\Doctrine\Observer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="class_name", type="string")
 *
 * @author Oliver Tischlinger
 */
class Subject implements \SplSubject
{
    const ENTITY_NAME = __CLASS__;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Observer", cascade={"persist"}, inversedBy="entitySubjects")
     */
    private $entityObservers;

    /**
     * @var \SplObjectStorage
     *
     * @ORM\Column(type="object")
     */
    private $otherObservers;

    /**
     *
     */
    public function __construct()
    {
        $this->entityObservers = new ArrayCollection();
        $this->otherObservers = new \SplObjectStorage();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @see SplSubject::attach()
     */
    public function attach(\SplObserver $observer)
    {
        if ($observer instanceof Observer) {
            $this->entityObservers->add($observer);
        } else {
            $this->otherObservers->attach($observer);
        }
    }

    /**
     * @see SplSubject::detach()
     */
    public function detach(\SplObserver $observer)
    {
        if ($observer instanceof Observer) {
            $this->entityObservers->removeElement($observer);
        } else {
            $this->otherObservers->detach($observer);
        }
    }

    /**
     * @return \Traversable
     */
    public function getObservers()
    {
        $iterator = new \AppendIterator();
        $iterator->append($this->entityObservers->getIterator());
        $iterator->append($this->otherObservers);

        return $iterator;
    }

    /**
     * @see SplSubject::notify()
     */
    public function notify()
    {
        /* @var $observer \SplObserver */
        foreach ($this->getObservers() as $observer) {
            $observer->update($this);
        }
    }
}

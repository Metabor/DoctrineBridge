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
 *        
 */
class Subject implements \SplSubject
{

    /**
     *
     */
    private $observers;

    public function __construct()
    {
        $this->observers = new ArrayCollection();
    }

    public function attach(\SplObserver $observer)
    {
    }

    public function detach(\SplObserver $observer)
    {
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

}

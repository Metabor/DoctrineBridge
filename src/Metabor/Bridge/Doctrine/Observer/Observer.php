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
abstract class Observer implements \SplObserver
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
     * @ORM\ManyToMany(targetEntity="Subject", cascade={"persist"}, mappedBy="entityObservers")
     */
    private $entitySubjects;

    /**
     *
     */
    public function __construct()
    {
    	$this->entitySubjects = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getEntitySubjects()
	{
		return $this->entitySubjects;
	}
}

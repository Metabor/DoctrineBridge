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
}
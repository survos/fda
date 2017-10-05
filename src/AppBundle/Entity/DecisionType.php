<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DecisionType
 *
 * @ORM\Table(name="decision_type")
 * @ORM\Entity
 */
class DecisionType
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="inspection_count", type="integer", nullable=true)
     */
    private $inspectionCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="decision_type_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;


    /**
     * Set name
     *
     * @param string $name
     * @return DecisionType
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set inspectionCount
     *
     * @param integer $inspectionCount
     * @return DecisionType
     */
    public function setInspectionCount($inspectionCount)
    {
        $this->inspectionCount = $inspectionCount;
    
        return $this;
    }

    /**
     * Get inspectionCount
     *
     * @return integer 
     */
    public function getInspectionCount()
    {
        return $this->inspectionCount;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}

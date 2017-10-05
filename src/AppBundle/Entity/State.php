<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * State
 *
 * @ORM\Table(name="state")
 * @ORM\Entity
 */
class State
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
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
     * @ORM\Column(name="contract_total", type="integer", nullable=true)
     */
    private $contractTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="department", type="string", length=255, nullable=true)
     */
    private $department;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="state_award_1", type="date", nullable=true)
     */
    private $stateAward1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="state_award_2", type="date", nullable=true)
     */
    private $stateAward2;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="state_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;



    /**
     * Set name
     *
     * @param string $name
     * @return State
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
     * @return State
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
     * Set contractTotal
     *
     * @param integer $contractTotal
     * @return State
     */
    public function setContractTotal($contractTotal)
    {
        $this->contractTotal = $contractTotal;
    
        return $this;
    }

    /**
     * Get contractTotal
     *
     * @return integer 
     */
    public function getContractTotal()
    {
        return $this->contractTotal;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return State
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    
        return $this;
    }

    /**
     * Get department
     *
     * @return string 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set stateAward1
     *
     * @param \DateTime $stateAward1
     * @return State
     */
    public function setStateAward1($stateAward1)
    {
        $this->stateAward1 = $stateAward1;
    
        return $this;
    }

    /**
     * Get stateAward1
     *
     * @return \DateTime 
     */
    public function getStateAward1()
    {
        return $this->stateAward1;
    }

    /**
     * Set stateAward2
     *
     * @param \DateTime $stateAward2
     * @return State
     */
    public function setStateAward2($stateAward2)
    {
        $this->stateAward2 = $stateAward2;
    
        return $this;
    }

    /**
     * Get stateAward2
     *
     * @return \DateTime 
     */
    public function getStateAward2()
    {
        return $this->stateAward2;
    }

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }
}

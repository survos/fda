<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Retailer
 *
 * @ORM\Table(name="retailer")
 * @ORM\Entity
 */
class Retailer
{
    /**
     * @var string
     *
     * @ORM\Column(name="retailer_name", type="string", length=90, nullable=false)
     */
    private $retailerName;

    /**
     * @var string
     *
     * @ORM\Column(name="street_address", type="string", length=128, nullable=true)
     */
    private $streetAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="street_number", type="string", length=10, nullable=true)
     */
    private $streetNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="street_name", type="string", length=128, nullable=false)
     */
    private $streetName;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=32, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=2, nullable=false)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=10, nullable=false)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="neighborhood", type="string", length=64, nullable=true)
     */
    private $neighborhood;

    /**
     * @var string
     *
     * @ORM\Column(name="retail_type", type="string", length=32, nullable=true)
     */
    private $retailType;

    /**
     * @var string
     *
     * @ORM\Column(name="violations_list", type="string", length=128, nullable=true)
     */
    private $violationsList;

    /**
     * @var integer
     *
     * @ORM\Column(name="violation_count", type="integer", nullable=true)
     */
    private $violationCount;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     */
    private $longitude;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="retailer_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;



    /**
     * Set retailerName
     *
     * @param string $retailerName
     * @return Retailer
     */
    public function setRetailerName($retailerName)
    {
        $this->retailerName = $retailerName;
    
        return $this;
    }

    /**
     * Get retailerName
     *
     * @return string 
     */
    public function getRetailerName()
    {
        return $this->retailerName;
    }

    /**
     * Set streetAddress
     *
     * @param string $streetAddress
     * @return Retailer
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;
    
        return $this;
    }

    /**
     * Get streetAddress
     *
     * @return string 
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set streetNumber
     *
     * @param string $streetNumber
     * @return Retailer
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
    
        return $this;
    }

    /**
     * Get streetNumber
     *
     * @return string 
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * Set streetName
     *
     * @param string $streetName
     * @return Retailer
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;
    
        return $this;
    }

    /**
     * Get streetName
     *
     * @return string 
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Retailer
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Retailer
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set zip
     *
     * @param string $zip
     * @return Retailer
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    
        return $this;
    }

    /**
     * Get zip
     *
     * @return string 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set neighborhood
     *
     * @param string $neighborhood
     * @return Retailer
     */
    public function setNeighborhood($neighborhood)
    {
        $this->neighborhood = $neighborhood;
    
        return $this;
    }

    /**
     * Get neighborhood
     *
     * @return string 
     */
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    /**
     * Set retailType
     *
     * @param string $retailType
     * @return Retailer
     */
    public function setRetailType($retailType)
    {
        $this->retailType = $retailType;
    
        return $this;
    }

    /**
     * Get retailType
     *
     * @return string 
     */
    public function getRetailType()
    {
        return $this->retailType;
    }

    /**
     * Set violationsList
     *
     * @param string $violationsList
     * @return Retailer
     */
    public function setViolationsList($violationsList)
    {
        $this->violationsList = $violationsList;
    
        return $this;
    }

    /**
     * Get violationsList
     *
     * @return string 
     */
    public function getViolationsList()
    {
        return $this->violationsList;
    }

    /**
     * Set violationCount
     *
     * @param integer $violationCount
     * @return Retailer
     */
    public function setViolationCount($violationCount)
    {
        $this->violationCount = $violationCount;
    
        return $this;
    }

    /**
     * Get violationCount
     *
     * @return integer 
     */
    public function getViolationCount()
    {
        return $this->violationCount;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Retailer
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    
        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Retailer
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    
        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
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

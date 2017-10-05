<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FiscalYear
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\FiscalYearRepository")
 */
class FiscalYear
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", unique=true)
     */
    private $year;

    /**
     * @var integer
     *
     * @ORM\Column(name="line_count", type="integer", nullable=true)
     */
    private $lineCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="warning_count", type="integer", nullable=true)
     */
    private $warningCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="read_count", type="integer", nullable=true)
     */
    private $readCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="civil_penalty_count", type="integer", nullable=true)
     */
    private $civilPenaltyCount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="file_timestamp", type="datetime")
     */
    private $fileTimestamp;

    /**
     * @var Array
     *
     * @ORM\Column(name="stats", type="json_array", nullable=true)
     */
    private $stats;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return FiscalYear
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set lineCount
     *
     * @param integer $lineCount
     * @return FiscalYear
     */
    public function setLineCount($lineCount)
    {
        $this->lineCount = $lineCount;
    
        return $this;
    }

    /**
     * Get lineCount
     *
     * @return integer 
     */
    public function getLineCount()
    {
        return $this->lineCount;
    }

    /**
     * Set warningCount
     *
     * @param integer $warningCount
     * @return FiscalYear
     */
    public function setWarningCount($warningCount)
    {
        $this->warningCount = $warningCount;
    
        return $this;
    }

    /**
     * Get warningCount
     *
     * @return integer 
     */
    public function getWarningCount()
    {
        return $this->warningCount;
    }

    /**
     * Set loadedCount
     *
     * @param integer $readCount
     * @return FiscalYear
     */
    public function setReadCount($readCount)
    {
        $this->readCount = $readCount;
    
        return $this;
    }

    /**
     * Get loadedCount
     *
     * @return integer 
     */
    public function getReadCount()
    {
        return $this->readCount;
    }

    /**
     * Set civilPenaltyCount
     *
     * @param integer $civilPenaltyCount
     * @return FiscalYear
     */
    public function setCivilPenaltyCount($civilPenaltyCount)
    {
        $this->civilPenaltyCount = $civilPenaltyCount;
    
        return $this;
    }

    /**
     * Get civilPenaltyCount
     *
     * @return integer 
     */
    public function getCivilPenaltyCount()
    {
        return $this->civilPenaltyCount;
    }

    /**
     * Set fileTimestamp
     *
     * @param \DateTime $fileTimestamp
     * @return FiscalYear
     */
    public function setFileTimestamp($fileTimestamp)
    {
        $this->fileTimestamp = $fileTimestamp;
    
        return $this;
    }

    /**
     * Get fileTimestamp
     *
     * @return \DateTime 
     */
    public function getFileTimestamp()
    {
        return $this->fileTimestamp;
    }

    public function __toString()
    {
        return (string)$this->year;
    }

    /**
     * Set stats
     *
     * @param array $stats
     *
     * @return FiscalYear
     */
    public function setStats($stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * Get stats
     *
     * @return array
     */
    public function getStats()
    {
        return $this->stats;
    }

}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statute
 *
 * @ORM\Table(name="statute")
 * @ORM\Entity
 */
class Statute
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
     * @var string
     *
     * @ORM\Column(name="cfr_regulation", type="string", length=32, nullable=true)
     */
    private $cfrRegulation;

    /**
     * @var string
     *
     * @ORM\Column(name="short_title", type="string", length=64, nullable=true)
     */
    private $shortTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="title_number", type="integer", nullable=true)
     */
    private $titleNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="codebook", type="string", length=16, nullable=true)
     */
    private $codebook;

    /**
     * @var string
     *
     * @ORM\Column(name="part", type="string", length=16, nullable=true)
     */
    private $part;

    /**
     * @var string
     *
     * @ORM\Column(name="section", type="string", length=16, nullable=true)
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column(name="paragraph", type="string", length=32, nullable=true)
     */
    private $paragraph;

    /**
     * @var string
     *
     * @ORM\Column(name="statute_code", type="string", length=30, nullable=false)
     */
    private $statuteCode;

    /**
     * @var string
     *
     * @ORM\Column(name="variable", type="string", length=30, nullable=false)
     */
    private $variable;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    private $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="warning_count", type="integer", nullable=true)
     */
    private $warningCount;

    /**
     * @ORM\ManyToMany(targetEntity="RawInspection", mappedBy="violations")
     **/
    private $inspections;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Set cfrRegulation
     *
     * @param string $cfrRegulation
     * @return Statute
     */
    public function setCfrRegulation($cfrRegulation)
    {
        $this->cfrRegulation = $cfrRegulation;
    
        return $this;
    }

    /**
     * Get cfrRegulation
     *
     * @return string 
     */
    public function getCfrRegulation()
    {
        return $this->cfrRegulation;
    }

    /**
     * Set shortTitle
     *
     * @param string $shortTitle
     * @return Statute
     */
    public function setShortTitle($shortTitle)
    {
        $this->shortTitle = $shortTitle;
    
        return $this;
    }

    /**
     * Get shortTitle
     *
     * @return string 
     */
    public function getShortTitle()
    {
        return $this->shortTitle;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Statute
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set titleNumber
     *
     * @param integer $titleNumber
     * @return Statute
     */
    public function setTitleNumber($titleNumber)
    {
        $this->titleNumber = $titleNumber;
    
        return $this;
    }

    /**
     * Get titleNumber
     *
     * @return integer 
     */
    public function getTitleNumber()
    {
        return $this->titleNumber;
    }

    /**
     * Set codebook
     *
     * @param string $codebook
     * @return Statute
     */
    public function setCodebook($codebook)
    {
        $this->codebook = $codebook;
    
        return $this;
    }

    /**
     * Get codebook
     *
     * @return string 
     */
    public function getCodebook()
    {
        return $this->codebook;
    }

    /**
     * Set part
     *
     * @param string $part
     * @return Statute
     */
    public function setPart($part)
    {
        $this->part = $part;
    
        return $this;
    }

    /**
     * Get part
     *
     * @return string 
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * Set section
     *
     * @param string $section
     * @return Statute
     */
    public function setSection($section)
    {
        $this->section = $section;
    
        return $this;
    }

    /**
     * Get section
     *
     * @return string 
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set paragraph
     *
     * @param string $paragraph
     * @return Statute
     */
    public function setParagraph($paragraph)
    {
        $this->paragraph = $paragraph;
    
        return $this;
    }

    /**
     * Get paragraph
     *
     * @return string 
     */
    public function getParagraph()
    {
        return $this->paragraph;
    }

    /**
     * Set statuteCode
     *
     * @param string $statuteCode
     * @return Statute
     */
    public function setStatuteCode($statuteCode)
    {
        $this->statuteCode = $statuteCode;
    
        return $this;
    }

    /**
     * Get statuteCode
     *
     * @return string 
     */
    public function getStatuteCode()
    {
        return $this->statuteCode;
    }

    /**
     * Set variable
     *
     * @param string $variable
     * @return Statute
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
    
        return $this;
    }

    /**
     * Get variable
     *
     * @return string 
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Statute
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set warningCount
     *
     * @param integer $warningCount
     * @return Statute
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    /****************************************************************/
    public function getCfrUrl() {
        $url = 'http://www.accessdata.fda.gov/scripts/cdrh/cfdocs/cfcfr/CFRSearch.cfm?';
        if ($this->getSection()) {
            $url .= sprintf("fr=%s.%s", $this->getPart(), $this->getSection());
        }
        else {
            $url .= "CFRPart=" . $this->getPart();
        }
        return $url;
    }

    public function getVarCode()
    {
        // return str_replace('_', '', tt::display_to_code($this->getSection() . $this->getParagraph()));
        return str_replace('is_', '', $this->getVariable());// tt::display_to_code($this->getSection() . $this->getParagraph()));
    }

    public function getWarningsOnlyCount()
    {
        $method = "filterByHas" . $this->getVarCode();
        return RawInspectionQuery::create()
            ->filterByIsWarningSent(true)
            ->filterByDecisionType('Civil Money Penalty', \CRITERIA::NOT_EQUAL)
            ->$method(1)
            ->count();
    }


    /**
     * Add inspections
     *
     * @param \AppBundle\Entity\RawInspection $inspections
     * @return Statute
     */
    public function addInspection(\AppBundle\Entity\RawInspection $inspections)
    {
        $this->inspections[] = $inspections;
    
        return $this;
    }

    /**
     * Remove inspections
     *
     * @param \AppBundle\Entity\RawInspection $inspections
     */
    public function removeInspection(\AppBundle\Entity\RawInspection $inspections)
    {
        $this->inspections->removeElement($inspections);
    }

    /**
     * Get inspections
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInspections()
    {
        return $this->inspections;
    }

    public function __toString()
    {
        return $this->statuteCode;
    }
}

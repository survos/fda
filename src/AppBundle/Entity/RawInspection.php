<?php

namespace AppBundle\Entity;

# use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\Mapping as ORM;

/**
 * RawInspection
 *
 * @ORM\Table(name="raw_inspection")
 * @ORM\Entity(repositoryClass="RawInspectionRepository")
 */
class RawInspection
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @   ORM\GeneratedValue(strategy="SEQUENCE")
     * @  ORM\SequenceGenerator(sequenceName="raw_inspection_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $hash;

    /**
     * @var integer
     *
     * @ORM\Column(name="line_number", type="integer", nullable=false)
     */
    private $lineNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="raw_retailer_name", type="string", length=90, nullable=true)
     */
    private $rawRetailerName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="raw_street_address", type="string", length=128, nullable=true)
     */
    private $rawStreetAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="raw_city", type="string", length=64, nullable=true)
     */
    private $rawCity;

    /**
     * @var string
     *
     * @ORM\Column(name="raw_state", type="string", length=2, nullable=true)
     */
    private $rawState;

    /**
     * @var string
     *
     * @ORM\Column(name="raw_zip", type="string", length=10, nullable=true)
     */
    private $rawZip;

    /**
     * @var Text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $rawCsv;

    /**
     * @var string
     *
     * @ORM\Column(name="raw_full_address", type="text", nullable=true)
     */
    private $rawFullAddress;

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
     * @ORM\Column(name="city", type="string", length=64, nullable=false)
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
     * @ORM\Column(name="full_address", type="text", nullable=true)
     */
    private $fullAddress;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="FiscalYear", cascade={"persist"})
     */
    private $fiscalYear;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=240, nullable=true)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="decision_type", type="string", length=64, nullable=true)
     */
    private $decisionType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="decision_date", type="date", nullable=true)
     */
    private $decisionDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inspection_date", type="date", nullable=true)
     */
    private $inspectionDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="inspection_year", type="integer", nullable=true)
     */
    private $inspectionYear;

    /**
     * @var json_array
     *
     * @ORM\Column(name="google_data_json", type="json_array", nullable=true)
     */
    private $googleDataJson;

    /**
     * @var string
     *
     * @ORM\Column(name="match", type="string", length=16, nullable=true)
     */
    private $match;

    /**
     * @var string
     *
     * @ORM\Column(name="reference_number", type="string", length=32, nullable=true)
     */
    private $referenceNumber;

    /**
     * @var Point
     *
     * @ORM\Column(name="the_geom", type="point", nullable=true)
    private $theGeom;
     */

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_warning_sent", type="boolean", nullable=true)
     */
    private $isWarningSent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_minor_involved", type="boolean", nullable=true)
     */
    private $isMinorInvolved;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sale_to_minor", type="boolean", nullable=true)
     */
    private $saleToMinor;

    /**
     * @var integer
     *
     * @ORM\Column(name="ucm_number", type="integer", nullable=true)
     */
    private $ucmNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="warning_url", type="string", length=255, nullable=true)
     */
    private $warningUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="warning_html", type="text", nullable=true)
     */
    private $warningHtml;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="warning_date", type="date", nullable=true)
     */
    private $warningDate;

    /**
     * @var json_array
     *
     * @ORM\Column(name="warning_json", type="json_array", nullable=true)
     */
    private $warningJson;

    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="Statute", inversedBy="inspections", cascade={"persist"})
     * @ORM\JoinTable(name="inspections_statutes")
     *
     **/
    private $violations;

    /**
     * @var string
     *
     * @ORM\Column(name="v14e_type", type="string", length=128, nullable=true)
     */
    private $v14eType;

    /**
     * @var integer
     *
     * @ORM\Column(name="civil_fine", type="integer", nullable=true)
     */
    private $civilFine;

    /**
     * @var string
     *
     * @ORM\Column(name="recent_violations", type="text", nullable=true)
     */
    private $recentViolations;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_10", type="boolean", nullable=true)
     */
    private $has10;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_14d", type="boolean", nullable=true)
     */
    private $has14d;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_16", type="boolean", nullable=true)
     */
    private $has16;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_16b", type="boolean", nullable=true)
     */
    private $has16b;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_16d1", type="boolean", nullable=true)
     */
    private $has16d1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_2", type="boolean", nullable=true)
     */
    private $has2;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_16d2", type="boolean", nullable=true)
     */
    private $has16d2;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_14b", type="boolean", nullable=true)
     */
    private $has14b;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_16c", type="boolean", nullable=true)
     */
    private $has16c;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_14a", type="boolean", nullable=true)
     */
    private $has14a;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_14b1", type="boolean", nullable=true)
     */
    private $has14b1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_14b2", type="boolean", nullable=true)
     */
    private $has14b2;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_14e", type="boolean", nullable=true)
     */
    private $has14e;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_16c1", type="boolean", nullable=true)
     */
    private $has16c1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_14c", type="boolean", nullable=true)
     */
    private $has14c;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_34b", type="boolean", nullable=true)
     */
    private $has34b;

    /**
     * @var string
     *
     * @ORM\Column(name="violation_history", type="text", nullable=true)
     */
    private $violationHistory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->violations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set lineNumber
     *
     * @param integer $lineNumber
     * @return RawInspection
     */
    public function setLineNumber($lineNumber)
    {
        $this->lineNumber = $lineNumber;

        return $this;
    }

    /**
     * Get lineNumber
     *
     * @return integer
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * Set rawRetailerName
     *
     * @param string $rawRetailerName
     * @return RawInspection
     */
    public function setRawRetailerName($rawRetailerName)
    {
        $this->rawRetailerName = $rawRetailerName;

        return $this;
    }

    /**
     * Get rawRetailerName
     *
     * @return string
     */
    public function getRawRetailerName()
    {
        return $this->rawRetailerName;
    }

    /**
     * Set rawStreetAddress
     *
     * @param string $rawStreetAddress
     * @return RawInspection
     */
    public function setRawStreetAddress($rawStreetAddress)
    {
        $this->rawStreetAddress = $rawStreetAddress;

        return $this;
    }

    /**
     * Get rawStreetAddress
     *
     * @return string
     */
    public function getRawStreetAddress()
    {
        return $this->rawStreetAddress;
    }

    /**
     * Set rawCity
     *
     * @param string $rawCity
     * @return RawInspection
     */
    public function setRawCity($rawCity)
    {
        $this->rawCity = $rawCity;

        return $this;
    }

    /**
     * Get rawCity
     *
     * @return string
     */
    public function getRawCity()
    {
        return $this->rawCity;
    }

    /**
     * Set rawState
     *
     * @param string $rawState
     * @return RawInspection
     */
    public function setRawState($rawState)
    {
        $this->rawState = $rawState;

        return $this;
    }

    /**
     * Get rawState
     *
     * @return string
     */
    public function getRawState()
    {
        return $this->rawState;
    }

    /**
     * Set rawZip
     *
     * @param string $rawZip
     * @return RawInspection
     */
    public function setRawZip($rawZip)
    {
        $this->rawZip = $rawZip;

        return $this;
    }

    /**
     * Get rawZip
     *
     * @return string
     */
    public function getRawZip()
    {
        return $this->rawZip;
    }

    /**
     * Set rawFullAddress
     *
     * @param string $rawFullAddress
     * @return RawInspection
     */
    public function setRawFullAddress($rawFullAddress)
    {
        $this->rawFullAddress = $rawFullAddress;

        return $this;
    }

    /**
     * Get rawFullAddress
     *
     * @return string
     */
    public function getRawFullAddress()
    {
        return $this->rawFullAddress;
    }

    /**
     * Set retailerName
     *
     * @param string $retailerName
     * @return RawInspection
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
     * @return RawInspection
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
     * Set city
     *
     * @param string $city
     * @return RawInspection
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
     * @return RawInspection
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
     * @return RawInspection
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
     * Set fullAddress
     *
     * @param string $fullAddress
     * @return RawInspection
     */
    public function setFullAddress($fullAddress)
    {
        $this->fullAddress = $fullAddress;

        return $this;
    }

    /**
     * Get fullAddress
     *
     * @return string
     */
    public function getFullAddress()
    {
        return $this->fullAddress;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return RawInspection
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set decisionType
     *
     * @param string $decisionType
     * @return RawInspection
     */
    public function setDecisionType($decisionType)
    {
        $this->decisionType = $decisionType;
        if ($this->decisionType) {
            $this->setIsWarningSent(true);
        }

        return $this;
    }

    /**
     * Get decisionType
     *
     * @return string
     */
    public function getDecisionType()
    {
        return $this->decisionType;
    }

    /**
     * Set decisionDate
     *
     * @param \DateTime $decisionDate
     * @return RawInspection
     */
    public function setDecisionDate($decisionDate)
    {
        if (is_string($decisionDate)) {
            $decisionDate = $decisionDate ? new \DateTime($decisionDate) : null;
        }
        $this->decisionDate = $decisionDate;

        return $this;
    }

    /**
     * Get decisionDate
     *
     * @return \DateTime
     */
    public function getDecisionDate()
    {
        return $this->decisionDate;
    }

    /**
     * Set inspectionDate
     *
     * @param \DateTime $inspectionDate
     * @return RawInspection
     */
    public function setInspectionDate($inspectionDate)
    {
        $this->inspectionDate = $inspectionDate;

        return $this;
    }

    /**
     * Get inspectionDate
     *
     * @return \DateTime
     */
    public function getInspectionDate()
    {
        return $this->inspectionDate;
    }

    /**
     * Set inspectionYear
     *
     * @param integer $inspectionYear
     * @return RawInspection
     */
    public function setInspectionYear($inspectionYear)
    {
        $this->inspectionYear = $inspectionYear;

        return $this;
    }

    /**
     * Get inspectionYear
     *
     * @return integer
     */
    public function getInspectionYear()
    {
        return $this->inspectionYear;
    }

    /**
     * Set googleDataJson
     *
     * @param array $googleDataJson
     * @return RawInspection
     */
    public function setGoogleDataJson($googleDataJson)
    {
        $this->googleDataJson = $googleDataJson;

        return $this;
    }

    /**
     * Get googleDataJson
     *
     * @return array
     */
    public function getGoogleDataJson()
    {
        return $this->googleDataJson;
    }

    /**
     * Set match
     *
     * @param string $match
     * @return RawInspection
     */
    public function setMatch($match)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match
     *
     * @return string
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set referenceNumber
     *
     * @param string $referenceNumber
     * @return RawInspection
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    /**
     * Get referenceNumber
     *
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * Set theGeom
     *
     * @param Point $theGeom
     * @return RawInspection
     */
    public function setTheGeom($theGeom)
    {
        $this->theGeom = $theGeom;
        return $this;
    }

    /**
     * Get theGeom
     *
     * @return Point
     */
    public function getTheGeom()
    {
        return $this->theGeom;
    }

    /**
     * Set isWarningSent
     *
     * @param boolean $isWarningSent
     * @return RawInspection
     */
    public function setIsWarningSent($isWarningSent)
    {
        $this->isWarningSent = $isWarningSent;

        return $this;
    }

    /**
     * Get isWarningSent
     *
     * @return boolean
     */
    public function getIsWarningSent()
    {
        return $this->isWarningSent;
    }

    /**
     * Set isMinorInvolved
     *
     * @param boolean $isMinorInvolved
     * @return RawInspection
     */
    public function setIsMinorInvolved($isMinorInvolved)
    {
        $this->isMinorInvolved = $isMinorInvolved;

        return $this;
    }

    public function setMinorInvolved($v) {
        return $this->setIsMinorInvolved($v);
    }

    /**
     * Get isMinorInvolved
     *
     * @return boolean
     */
    public function getIsMinorInvolved()
    {
        return $this->isMinorInvolved;
    }

    /**
     * Set saleToMinor
     *
     * @param boolean $saleToMinor
     * @return RawInspection
     */
    public function setSaleToMinor($saleToMinor)
    {
        $this->saleToMinor = $saleToMinor;

        return $this;
    }

    /**
     * Get saleToMinor
     *
     * @return boolean
     */
    public function getSaleToMinor()
    {
        return $this->saleToMinor;
    }

    /**
     * Set ucmNumber
     *
     * @param integer $ucmNumber
     * @return RawInspection
     */
    public function setUcmNumber($ucmNumber)
    {
        $this->ucmNumber = $ucmNumber;

        return $this;
    }

    /**
     * Get ucmNumber
     *
     * @return integer
     */
    public function getUcmNumber()
    {
        return $this->ucmNumber;
    }

    /**
     * Set warningUrl
     *
     * @param string $warningUrl
     * @return RawInspection
     */
    public function setWarningUrl($warningUrl)
    {
        $this->warningUrl = $warningUrl;

        return $this;
    }

    /**
     * Get warningUrl
     *
     * @return string
     */
    public function getWarningUrl()
    {
        return $this->warningUrl;
    }

    /**
     * Set warningHtml
     *
     * @param string $warningHtml
     * @return RawInspection
     */
    public function setWarningHtml($warningHtml)
    {
        $this->warningHtml = $warningHtml;

        return $this;
    }

    /**
     * Get warningHtml
     *
     * @return string
     */
    public function getWarningHtml()
    {
        return $this->warningHtml;
    }

    /**
     * Set warningDate
     *
     * @param \DateTime $warningDate
     * @return RawInspection
     */
    public function setWarningDate($warningDate)
    {
        $this->warningDate = $warningDate;

        return $this;
    }

    /**
     * Get warningDate
     *
     * @return \DateTime
     */
    public function getWarningDate()
    {
        return $this->warningDate;
    }

    /**
     * Set warningJson
     *
     * @param array $warningJson
     * @return RawInspection
     */
    public function setWarningJson($warningJson)
    {
        $this->warningJson = $warningJson;

        return $this;
    }

    /**
     * Get warningJson
     *
     * @return array
     */
    public function getWarningJson()
    {
        return $this->warningJson;
    }

    /**
     * Set v14eType
     *
     * @param string $v14eType
     * @return RawInspection
     */
    public function setV14eType($v14eType)
    {
        $this->v14eType = $v14eType;

        return $this;
    }

    /**
     * Get v14eType
     *
     * @return string
     */
    public function getV14eType()
    {
        return $this->v14eType;
    }

    /**
     * Set civilFine
     *
     * @param integer $civilFine
     * @return RawInspection
     */
    public function setCivilFine($civilFine)
    {
        $this->civilFine = $civilFine;

        return $this;
    }

    /**
     * Get civilFine
     *
     * @return integer
     */
    public function getCivilFine()
    {
        return $this->civilFine;
    }

    /**
     * Set recentViolations
     *
     * @param string $recentViolations
     * @return RawInspection
     */
    public function setRecentViolations($recentViolations)
    {
        $this->recentViolations = $recentViolations;

        return $this;
    }

    /**
     * Get recentViolations
     *
     * @return string
     */
    public function getRecentViolations()
    {
        return $this->recentViolations;
    }

    /**
     * Set has10
     *
     * @param boolean $has10
     * @return RawInspection
     */
    public function setHas10($has10)
    {
        $this->has10 = $has10;

        return $this;
    }

    /**
     * Get has10
     *
     * @return boolean
     */
    public function getHas10()
    {
        return $this->has10;
    }

    /**
     * Set has14d
     *
     * @param boolean $has14d
     * @return RawInspection
     */
    public function setHas14d($has14d)
    {
        $this->has14d = $has14d;

        return $this;
    }

    /**
     * Get has14d
     *
     * @return boolean
     */
    public function getHas14d()
    {
        return $this->has14d;
    }

    /**
     * Set has16
     *
     * @param boolean $has16
     * @return RawInspection
     */
    public function setHas16($has16)
    {
        $this->has16 = $has16;

        return $this;
    }

    /**
     * Get has16
     *
     * @return boolean
     */
    public function getHas16()
    {
        return $this->has16;
    }

    /**
     * Set has16b
     *
     * @param boolean $has16b
     * @return RawInspection
     */
    public function setHas16b($has16b)
    {
        $this->has16b = $has16b;

        return $this;
    }

    /**
     * Get has16b
     *
     * @return boolean
     */
    public function getHas16b()
    {
        return $this->has16b;
    }

    /**
     * Set has16d1
     *
     * @param boolean $has16d1
     * @return RawInspection
     */
    public function setHas16d1($has16d1)
    {
        $this->has16d1 = $has16d1;

        return $this;
    }

    /**
     * Get has16d1
     *
     * @return boolean
     */
    public function getHas16d1()
    {
        return $this->has16d1;
    }

    /**
     * Set has2
     *
     * @param boolean $has2
     * @return RawInspection
     */
    public function setHas2($has2)
    {
        $this->has2 = $has2;

        return $this;
    }

    /**
     * Get has2
     *
     * @return boolean
     */
    public function getHas2()
    {
        return $this->has2;
    }

    /**
     * Set has16d2
     *
     * @param boolean $has16d2
     * @return RawInspection
     */
    public function setHas16d2($has16d2)
    {
        $this->has16d2 = $has16d2;

        return $this;
    }

    /**
     * Get has16d2
     *
     * @return boolean
     */
    public function getHas16d2()
    {
        return $this->has16d2;
    }

    /**
     * Set has14b
     *
     * @param boolean $has14b
     * @return RawInspection
     */
    public function setHas14b($has14b)
    {
        $this->has14b = $has14b;

        return $this;
    }

    /**
     * Get has14b
     *
     * @return boolean
     */
    public function getHas14b()
    {
        return $this->has14b;
    }

    /**
     * Set has16c
     *
     * @param boolean $has16c
     * @return RawInspection
     */
    public function setHas16c($has16c)
    {
        $this->has16c = $has16c;

        return $this;
    }

    /**
     * Get has16c
     *
     * @return boolean
     */
    public function getHas16c()
    {
        return $this->has16c;
    }

    /**
     * Set has14a
     *
     * @param boolean $has14a
     * @return RawInspection
     */
    public function setHas14a($has14a)
    {
        $this->has14a = $has14a;

        return $this;
    }

    /**
     * Get has14a
     *
     * @return boolean
     */
    public function getHas14a()
    {
        return $this->has14a;
    }

    /**
     * Set has14b1
     *
     * @param boolean $has14b1
     * @return RawInspection
     */
    public function setHas14b1($has14b1)
    {
        $this->has14b1 = $has14b1;

        return $this;
    }

    /**
     * Get has14b1
     *
     * @return boolean
     */
    public function getHas14b1()
    {
        return $this->has14b1;
    }

    /**
     * Set has14b2
     *
     * @param boolean $has14b2
     * @return RawInspection
     */
    public function setHas14b2($has14b2)
    {
        $this->has14b2 = $has14b2;

        return $this;
    }

    /**
     * Get has14b2
     *
     * @return boolean
     */
    public function getHas14b2()
    {
        return $this->has14b2;
    }

    /**
     * Set has14e
     *
     * @param boolean $has14e
     * @return RawInspection
     */
    public function setHas14e($has14e)
    {
        $this->has14e = $has14e;

        return $this;
    }

    /**
     * Get has14e
     *
     * @return boolean
     */
    public function getHas14e()
    {
        return $this->has14e;
    }

    /**
     * Set has16c1
     *
     * @param boolean $has16c1
     * @return RawInspection
     */
    public function setHas16c1($has16c1)
    {
        $this->has16c1 = $has16c1;

        return $this;
    }

    /**
     * Get has16c1
     *
     * @return boolean
     */
    public function getHas16c1()
    {
        return $this->has16c1;
    }

    /**
     * Set has14c
     *
     * @param boolean $has14c
     * @return RawInspection
     */
    public function setHas14c($has14c)
    {
        $this->has14c = $has14c;

        return $this;
    }

    /**
     * Get has14c
     *
     * @return boolean
     */
    public function getHas14c()
    {
        return $this->has14c;
    }

    /**
     * Set has34b
     *
     * @param boolean $has34b
     * @return RawInspection
     */
    public function setHas34b($has34b)
    {
        $this->has34b = $has34b;

        return $this;
    }

    /**
     * Get has34b
     *
     * @return boolean
     */
    public function getHas34b()
    {
        return $this->has34b;
    }

    /**
     * Set violationHistory
     *
     * @param string $violationHistory
     * @return RawInspection
     */
    public function setViolationHistory($violationHistory)
    {
        $this->violationHistory = $violationHistory;

        return $this;
    }

    /**
     * Get violationHistory
     *
     * @return string
     */
    public function getViolationHistory()
    {
        return $this->violationHistory;
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

    /**
     * Add violations
     *
     * @param \AppBundle\Entity\Statute $violations
     * @return RawInspection
     */
    public function addViolation(\AppBundle\Entity\Statute $violations)
    {
        if ($this->violations->contains($violations)) {
            return;
        }
        $this->violations[] = $violations;

        return $this;
    }

    /**
     * Remove violations
     *
     * @param \AppBundle\Entity\Statute $violations
     */
    public function removeViolation(\AppBundle\Entity\Statute $violations)
    {
        $this->violations->removeElement($violations);
    }

    /**
     * Get violations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * Get violations
     *
     * @return string
     */
    public function getViolationsList()
    {
        $v = [];
        /** @var Statute $violation */
        foreach ($this->getViolations() as $violation) {
            $v[] = $violation->getStatuteCode();
        }
        return implode(',', $v);
    }

    /**
     * @return mixed|string
     */
    public function getCleanHtml()
    {
        $html = $this->getWarningHtml();
        if ($html) {
            $html = preg_replace('{.*PAGEWATCH CODE=""-->}s', '', $html);
            $html = preg_replace('{Return to <a.*}s', '', $html);
            $html = preg_replace('{<img.*?>}i', '', $html);
            $html = preg_replace('{<div>(On.*?201\d.*?)</div>}', '<div><b>$1</b></div>', $html);
            if ($this->isCivil()) {
                $html = str_replace("\f", '', $html); //remove page breaks
                $html = preg_replace('{^[ \t]+([A-Z .-]+?)[ \t]*\n\s*\n}m', "<h3>\$1</h3>\n", $html);
                $html = preg_replace('{^Page \d+ – Complaint –.+\s*\n}m', '', $html);
                $html = preg_replace('{^[ \t]+\d+\s*\n+}m', '', $html); // remove page numbers
                $html = preg_replace('{(^([ \t]+)[a-z]\.[ \t]+\S.+\n\s*(?:\n\2[ \t]+[^<\s].+\s*\n)*)\s*\n}m',
                                     "<blockquote><p>\$1</p></blockquote>\n", $html);
                $html = preg_replace('{(^\d+\.[ \t]+\S.+\n\s*(?:\n[ \t]+[^<\s].+\s*\n)*)\s*\n}m', "<p>\$1</p>\n", $html);
                $html = preg_replace('{</blockquote>\s*<blockquote>}', "\n", $html);
            }
        }
        return $html;
    }

    /**
     * @return bool
     */
    public function isCivil()
    {
        return $this->getDecisionType() == 'Civil Money Penalty';
    }

    /**
     * @return mixed|null
     */
    public function getWarningData()
    {
        $json = $this->getWarningJson();
        return empty($json) ? null : json_decode($json);
    }

    /**
     * @return string
     */
    public function getWikiText()
    {
        if ($this->getDecisionType() == 'Civil Money Penalty') {
            return (string)$this->getWarningHtml();
        }
        if (\Survos\Lib\tt::file_extension($this->getWarningUrl()) == 'pdf') {
            return (string)$this->getWarningHtml();
        }

        $html = (string)$this->getCleanHtml();
        if (empty($html)) {
            return 'Missing Text';
        }

        // Replace nonbreaking spaces with normal spaces:
        $html = preg_replace('/&nbsp;|&#160;|\xc2?\xa0/', ' ', $html);

        // Change all p's to div's and handle <br><br>
        $html = preg_replace('{<(/?)p\b}', '<${1}div', $html);
        $html = preg_replace('{(?:<br\s*/?>\s*){2}}', '</div><div>', $html);

        try {
            $crawler = new Crawler();
            $crawler->addContent($html);
            $dearFound = false;
            $wikiText = '';
            foreach ($crawler->filter('div.middle-column')->children() as $child) {
                if ($child->nodeName != 'div') {
                    continue;
                }
                $text = trim($child->textContent);
                if ($text == '') {
                    continue;
                }
                if ($dearFound) {
                    $wikiText .= "\n\n";
                } elseif (substr($text, 0, 5) == 'Dear ') {
                    $dearFound = true;
                } else {
                    continue;
                }
                $style = $child->getAttribute('style');
                $indented = ($style == 'margin-left: 40px');
                if ($indented) {
                    $wikiText .= ': '.$text;
                } else {
                    $wikiText .= wordwrap($text);
                }
            }
        } catch (\Exception $e) {
            $wikiText = "Error parsing HTML";
        }
        return $wikiText;
    }

    /**
     * return array of entity values
     */
    public function toArray()
    {
        return [
            'lineNumber'       => $this->lineNumber,
            'rawRetailerName'  => $this->rawRetailerName,
            'rawStreetAddress' => $this->rawStreetAddress,
            'rawCity'          => $this->rawCity,
            'rawState'         => $this->rawState,
            'rawZip'           => $this->rawZip,
            'rawFullAddress'   => $this->rawFullAddress,
            'retailerName'     => $this->retailerName,
            'streetAddress'    => $this->streetAddress,
            'city'             => $this->city,
            'state'            => $this->state,
            'zip'              => $this->zip,
            'fullAddress'      => $this->fullAddress,
            'fiscalYear'       => $this->fiscalYear,
            'key'              => $this->key,
            'decisionType'     => $this->decisionType,
            'decisionDate'     => $this->decisionDate ? $this->decisionDate->format('Y-m-d') : null,
            'inspectionDate'   => $this->inspectionDate ? $this->inspectionDate->format('Y-m-d') : null,
            'inspectionYear'   => $this->inspectionYear,
            'match'            => $this->match,
            'referenceNumber'  => $this->referenceNumber,
            'isWarningSent'    => $this->isWarningSent,
            'isMinorInvolved'  => $this->isMinorInvolved,
            'saleToMinor'      => $this->saleToMinor,
            'ucmNumber'        => $this->ucmNumber,
            'warningUrl'       => $this->warningUrl,
            'warningDate'      => $this->warningDate ? $this->warningDate->format('Y-m-d') : null,
            'civilFine'        => $this->civilFine,
            'lat'              => $this->theGeom ? $this->theGeom->getLatitude() : null,
            'lng'              => $this->theGeom ? $this->theGeom->getLongitude() : null,
        ];
    }

    /**
     * Set fiscalYear
     *
     * @param \AppBundle\Entity\FiscalYear $fiscalYear
     * @return RawInspection
     */
    public function setFiscalYear(\AppBundle\Entity\FiscalYear $fiscalYear = null)
    {
        $this->fiscalYear = $fiscalYear;

        return $this;
    }

    /**
     * Get fiscalYear
     *
     * @return \AppBundle\Entity\FiscalYear 
     */
    public function getFiscalYear()
    {
        return $this->fiscalYear;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return RawInspection
     */
    public function setLink($link)
    {
        $this->link = $link ?: null;


        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set rawCsv
     *
     * @param string $rawCsv
     *
     * @return RawInspection
     */
    public function setRawCsv($rawCsv)
    {
        $this->rawCsv = $rawCsv;

        return $this;
    }

    /**
     * Get rawCsv
     *
     * @return string
     */
    public function getRawCsv()
    {
        return $this->rawCsv;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return RawInspection
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    public function getHtmlLength()
    {
        return strlen($this->getWarningHtml());
    }
}

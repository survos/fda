<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * InspectionsExport
 *
 * @ORM\Table(name="inspections_export")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class InspectionsExport
{
    const STATUS_INITIATED = 1;
    const STATUS_PROGRESS = 2;
    const STATUS_FINISHED = 3;
    const STATUS_ERROR = 4;

    const REPORT_TYPE_EXCEL = 1;
    const REPORT_TYPE_ZIP_CSV = 2;
    const REPORT_TYPE_CARTO_DB = 3;

    const TIMEOUT = 3600;// in seconds

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", cascade={"persist"})
     */
    private $user;

    /**
     * @var Statute
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Statute", cascade={"persist"})
     * @ORM\JoinTable(name="export_statutes",
     *      joinColumns={@ORM\JoinColumn(name="export_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="statute_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $statutes;

    /**
     * @var FiscalYear
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\FiscalYear", cascade={"persist"})
     * @ORM\OrderBy({"year" = "DESC"})
     */
    private $fiscalYears;

    /**
     * @var string $filename
     *
     * @ORM\Column(name="filename", type="string", nullable=true)
     */
    private $filename;

    /**
     * @var string $baseFilename
     *
     * @ORM\Column(name="base_filename", type="string")
     */
    private $baseFilename;

    /**
     * @var integer $limit
     *
     * @ORM\Column(name="max_results", type="integer")
     */
    private $limit;

    /**
     * @var string $status
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var string $warningsOnly
     *
     * @ORM\Column(name="warnings_only", type="boolean", nullable=true)
     */
    private $warningsOnly;

    /**
     * @var string reportType
     *
     * @ORM\Column(name="report_type", type="integer", nullable=false)
     */
    private $reportType;

    /**
     * @var integer totalLines
     *
     * @ORM\Column(name="total_lines", type="integer", nullable=true)
     */
    private $totalLines;

    /**
     * @var
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var
     * @ORM\Column(name="exported_at", type="datetime", nullable=true)
     */
    private $exportedAt;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Posse\CartoManagerBundle\Entity\CartoTable")
    private $cartoTable;
     */


    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Set the update value to now.
     *
     * @author peterg
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
        $this->updatedAt = new DateTime();
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
     * Constructor
     */
    public function __construct()
    {
        $this->status = self::STATUS_INITIATED;
        $this->limit = 0; // no limits
        $this->reportType = self::REPORT_TYPE_ZIP_CSV;
        $this->totalLines = 0;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return InspectionsExport
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        // update export date/time with file's data
        if (file_exists($filename)) {
            $date = new DateTime();
            $date->setTimestamp(filemtime($filename));
            $this->exportedAt = $date;
        }
        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return InspectionsExport
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return InspectionsExport
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return InspectionsExport
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * return label for given status
     * @return string
     */
    public function getStatusText()
    {
        switch ($this->status) {
            case self::STATUS_INITIATED:
                return 'Waiting';
                break;
            case self::STATUS_PROGRESS:
                return 'In progress';
                break;
            case self::STATUS_FINISHED:
                return 'Ready';
                break;
            case self::STATUS_ERROR:
                return 'Error';
                break;
        }
    }

    /**
     * Add statutes
     *
     * @param \AppBundle\Entity\Statute $statutes
     *
     * @return InspectionsExport
     */
    public function addStatute(\AppBundle\Entity\Statute $statutes)
    {
        $this->statutes[] = $statutes;

        return $this;
    }

    /**
     * Remove statutes
     *
     * @param \AppBundle\Entity\Statute $statutes
     */
    public function removeStatute(\AppBundle\Entity\Statute $statutes)
    {
        $this->statutes->removeElement($statutes);
    }

    /**
     * Get statutes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStatutes()
    {
        return $this->statutes;
    }

    /**
     * Set limit
     *
     * @param integer $limit
     *
     * @return InspectionsExport
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get limit
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Add fiscalYears
     *
     * @param \AppBundle\Entity\FiscalYear $fiscalYears
     *
     * @return InspectionsExport
     */
    public function addFiscalYear(\AppBundle\Entity\FiscalYear $fiscalYears)
    {
        $this->fiscalYears[] = $fiscalYears;

        return $this;
    }

    /**
     * Remove fiscalYears
     *
     * @param \AppBundle\Entity\FiscalYear $fiscalYears
     */
    public function removeFiscalYear(\AppBundle\Entity\FiscalYear $fiscalYears)
    {
        $this->fiscalYears->removeElement($fiscalYears);
    }

    /**
     * Get fiscalYears
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiscalYears()
    {
        return $this->fiscalYears;
    }

    /**
     * Get exportedAt
     *
     * @return \DateTime
     */
    public function getExportedAt()
    {
        return $this->exportedAt;
    }

    /**
     * Set exportedAt
     *
     * @param \DateTime $exportedAt
     *
     * @return InspectionsExport
     */
    public function setExportedAt($exportedAt)
    {
        $this->exportedAt = $exportedAt;

        return $this;
    }

    /**
     * Set reportType
     *
     * @param integer $reportType
     *
     * @return InspectionsExport
     */
    public function setReportType($reportType)
    {
        $this->reportType = $reportType;

        return $this;
    }

    /**
     * Get reportType
     *
     * @return integer
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * return label for given status
     * @return string
     */
    public static function getReportTypeLabel($reportType)
    {
        switch ($reportType) {
            case self::REPORT_TYPE_EXCEL:
                return 'Excel';
                break;
            case self::REPORT_TYPE_ZIP_CSV:
                return 'Zipped CSV';
                break;
            case self::REPORT_TYPE_CARTO_DB:
                return 'Carto DB';
                break;
        }
    }

    /**
     * Set baseFilename
     *
     * @param string $baseFilename
     *
     * @return InspectionsExport
     */
    public function setBaseFilename($baseFilename)
    {
        $this->baseFilename = $baseFilename;

        return $this;
    }

    /**
     * Get baseFilename
     *
     * @return string
     */
    public function getBaseFilename()
    {
        return $this->baseFilename;
    }

    /**
     * Set cartoTable
     *
     * @param \Posse\CartoManagerBundle\Entity\CartoTable $cartoTable
     *
     * @return InspectionsExport
     */
    public function setCartoTable(\Posse\CartoManagerBundle\Entity\CartoTable $cartoTable = null)
    {
        $this->cartoTable = $cartoTable;

        return $this;
    }

    /**
     * Get cartoTable
     *
     * @return \Posse\CartoManagerBundle\Entity\CartoTable
     */
    public function getCartoTable()
    {
        return $this->cartoTable;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return InspectionsExport
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set warningsOnly
     *
     * @param boolean $warningsOnly
     *
     * @return InspectionsExport
     */
    public function setWarningsOnly($warningsOnly)
    {
        $this->warningsOnly = $warningsOnly;

        return $this;
    }

    /**
     * Get warningsOnly
     *
     * @return boolean
     */
    public function getWarningsOnly()
    {
        return $this->warningsOnly;
    }

    /**
     * Set totalLines
     *
     * @param integer $totalLines
     *
     * @return InspectionsExport
     */
    public function setTotalLines($totalLines)
    {
        $this->totalLines = $totalLines;

        return $this;
    }

    /**
     * Get totalLines
     *
     * @return integer
     */
    public function getTotalLines()
    {
        return $this->totalLines;
    }
}

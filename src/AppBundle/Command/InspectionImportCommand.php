<?php //

/*
csv file is downloaded from link on:

http://www.accessdata.fda.gov/scripts/oce/inspections/oce_insp_searching.cfm

Click on download Data to Excel and save the file to
  /usr/sites/sf/fda/src/Tobacco/FDABundle/Resources/data/fda.csv

This version is much simpler, creates Inspections and Retailer lists, very clean

*/

namespace AppBundle\Command;

use AppBundle\Entity\FiscalYear;
use AppBundle\Entity\RawInspection;
use CrEOF\Spatial\PHP\Types\Geography\Point;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Survos\PhotoStreamBundle\Model\InspectionQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;
use Survos\Lib\tt;
use Survos\Lib\GoogleGeocoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/*
csv file is downloaded from link on:

http://www.accessdata.fda.gov/scripts/oce/inspections/oce_insp_searching.cfm

Click on download Data to Excel and save the file to
  /usr/sites/sf/fda/src/Tobacco/FDABundle/Resources/data/fda.csv
*/

/**
 * Class InspectionImportCommand
 * @package Tobacco\FDABundle\Command
 */
class InspectionImportCommand extends ContainerAwareCommand
{
    /** @var GoogleGeocoder */
    private $geocoder = null;

    /** @var bool */
    private $ok_to_cache;

    protected function configure()
    {
        $from = -1;
        $this
            ->setName('fda:import-raw')
            ->setDescription('Import CSV Files to RawInspection')
//            ->addArgument('violations', InputArgument::OPTIONAL, 'How many violations?', 5)
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'How many more lines?', 50)
            ->addOption('geocoding', null, InputOption::VALUE_OPTIONAL, 'Which geocoding service (none|google)', 'none')
            ->addOption('year', null, InputOption::VALUE_OPTIONAL, 'Load data for year', null)// , date('Y'))
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'If set, start from this line number', $from)
            ->addOption('purge', null, InputOption::VALUE_NONE, 'If set, purge first')
            // ->addOption('pdf-only', null, InputOption::VALUE_NONE, 'PDF files only')
            ->addOption('warnings-only', null, InputOption::VALUE_NONE, 'If set, use warnings-only file')
            ->addOption('no-buffer', null, InputOption::VALUE_NONE, 'If set, commit every save (easier to debug)');
    }

    function quickFix()
    {
        $q = RawInspectionQuery::create()
            ->filterByKey('%.%', \CRITERIA::LIKE);
        /** @var RawInspection $r */
        foreach ($q->find() as $r) {
            printf("%s\n", $r->getKey());
            $r->setKey(str_replace('.', '', $r->getKey()))->save();
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $this->quickFix();   die();
        // ini_set('memory_limit', '128M');
        $this->ok_to_cache = function_exists('apc_fetch');

        if ($input->getOption('geocoding') == 'google') {
            $this->geocoder = new GoogleGeocoder(null);
            try {
                $awsClientId = $this->getContainer()->getParameter('aws_client_id');
                $awsCryptoKey = $this->getContainer()->getParameter('aws_crypto_key');
                $this->geocoder->setBusinessCredentials($awsClientId, $awsCryptoKey);
            } catch (\Exception $e) {
                throw new \Exception("Missing credentials to run geocoder");
            };
        }

        $limit = $input->getOption('limit');
        /** @var ObjectRepository $repo */
        $repo = $this->getManager()->getRepository('AppBundle:FiscalYear');
        /** @var QueryBuilder $qb */
        $qb = $repo->createQueryBuilder('fy');
        if ($year = $input->getOption('year')) {
            $qb->where('fy.year=:year')
                ->setParameter('year', $year);
        }

        $years = $qb
            ->orderBy('fy.year', 'ASC')
            ->getQuery()
            ->getResult();

        if ($input->getOption('purge') && !$input->getOption('year')) {
            $em = $this->getManager();
            $rawInspectionRepository = $em->getRepository('AppBundle:RawInspection');

            // import the Retailers
            $output->writeln("Purging All Raw Inspections ");
            $q = $em->createQuery('delete from AppBundle:RawInspection ');
            $q->execute();

            // reset fiscal years
            $em->getRepository('AppBundle:FiscalYear')
                ->loadFiscalYears(true, $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE);
        }
        /** @var FiscalYear $fy */
        foreach ($years as $fy) {
            $this->_importRawRetailers(
                $input,
                $output,
                $limit,
                $fy->getId(),
                $input->getOption('year') && $input->getOption('purge')
            );
        }

    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param int $limit
     * @param int $year
     * @param bool $purge_first
     */
    function _importRawRetailers(InputInterface $input, OutputInterface $output, $limit, $fyId, $purge_first)
    {
        $fy = $this->getManager()->find('AppBundle:FiscalYear', $fyId);
        $geocoding = !empty($this->geocoder);
        $em = $this->getManager();

        /** @var ObjectRepository $rawInspectionRepository */
        $rawInspectionRepository = $em->getRepository('AppBundle:RawInspection');

        // import the Retailers
        $verbose = $input->getOption('verbose');
        if ($purge_first) {
            $output->writeln("Purging Raw Inspections ");
            $q = $em->createQuery('delete AppBundle:RawInspection ri where ri.fiscalYear = ?1')
                ->setParameter(1, $fy->getId());
            $q->execute();
            $fy->setReadCount(0);
            $em->persist($fy);
            $em->flush();
        }

        $buffer = !$input->getOption('no-buffer') && !$input->getOption('warnings-only');

        $from = $input->getOption('from');

        if ($from == -1) {
            $from = $fy->getReadCount() - 1;
        }
        if ($limit) {
            $limit += $from;
        } // really how many more lines to read

        $year = $fy->getYear();

        $fn = $this->getContainer()->getParameter('kernel.root_dir')."/../var/data/OCE_FY{$year}.csv";
        $fn = $this->getContainer()->getParameter('kernel.root_dir')."/../var/data/tiny.csv";
        // cat header.csv warnings-only.csv >>warnings.csv

        /*
        if ($input->getOption('pdf-only')) {
            $fn = __DIR__."/../Resources/data/pdf.csv";
        }
        */

        $output->writeln("Reading from $fn, skipping to $from");
// decoding CSV contents
        $serializer = new Serializer([new ObjectNormalizer(), new GetSetMethodNormalizer(), new ArrayDenormalizer()], [new CsvEncoder()]);

        // $serializer = $this->getContainer()->get('serializer');
        // $data = $serializer->deserialize(file_get_contents($fn), RawInspection::class,'csv');
        $data = $serializer->decode(file_get_contents($fn), 'csv');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        printf("%d objects loaded", count($data));
        $lc = 0;
        foreach ($data as $inspection) {
            $lc++;
            $entity = new RawInspection();
            $em->persist($entity);
            $entity->setLineNumber($lc);
            foreach ($inspection as $var=>$val) {
                $method = 'set' . $var;
                $entity->$method($val);
            }
            dump($inspection, $entity);
        }
        $em->flush();
        die("Stopped");


        $reader = new \EasyCSV\Reader($fn);
        $reader->setForceUtf8(true);
        $warningCount = 0;

// create a new progress bar (50 units)
        $progress = new ProgressBar($output, $fy->getLineCount());

// start and displays the progress bar
        $progress->start();

        while ($d = $reader->getRow()) {
            $progress->advance();
            $lineNumber = $reader->getLineNumber();
            $orderedLineNumber = $fy->getLineCount() - $lineNumber;
            $fy->setReadCount($lineNumber);

            // if ($verbose) tt::dump($d);
            if (($limit>0) && ($lineNumber - 1) > $limit) {
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    $output->writeln("<info>Stopping at $limit</info>");
                }
                break;
            }
            if ($lineNumber < $from) {
                continue;
            }
            if (($lineNumber % 100) == 0) {
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    $output->write("<info>c</info>");
                }
                $em->flush();
                $em->clear();
                // clear detaches everything, so we need to reload year
                $fy = $this->getManager()->find('AppBundle:FiscalYear', $fyId);

            } elseif (!$buffer) {
                $em->flush();
                $em->clear();
                // clear detaches everything, so we need to reload year
                $fy = $this->getManager()->find('AppBundle:FiscalYear', $fyId);

            }

            if ($reader->hasError()) {
                printf("Error on line %d: %s", $lineNumber, $reader->getError());
                continue;
            }

            // skip anything with no warning
            if (!preg_match('{pdf}i', $d['Link'])) {
                // continue;
            }

            if ($input->getOption('warnings-only')) {
                if ($d['Decision Type'] == 'No Violations Observed') {
                    if ($verbose) {
                        $output->writeln('Skipping: '.$d['Decision Type']);
                    }
                    continue;
                }
            }

            // list($streetNumber, $streetName) = $this->processAddress($d['Street Address']);

            // $progress->advance();

            // add the raw record now
            // look up in Google
            $fullAddress = $this->fullAddress(
                $d['Street Address'],
                $d['City'],
                $d['State'],
                $d['Zip']
            );

            /** @var RawInspection $raw */
            $raw = $rawInspectionRepository->findOneBy(
                [
                    'lineNumber' => $orderedLineNumber,
                    'fiscalYear' => $fy->getId()
                ]
            );
            if (is_null($raw)) {
                $raw = new RawInspection();
                //lines below are here because of weird cascading problem which - doctrine was trying
                //to re-insert fiscalYear
                $raw->setFiscalYear($fy);
                $raw->setLineNumber($orderedLineNumber);
            } else {
//                $lat = $raw->getTheGeom()?$raw->getTheGeom()->getLatitude():null;
//                $lon = $raw->getTheGeom()?$raw->getTheGeom()->getLongitude():null;
            }

//            $raw = RawInspectionQuery::create()
//                ->filterByLineNumber($lineNumber)
//                ->filterByFiscalYear($year)
//                ->findOneOrCreate();

            $raw
                ->setRawStreetAddress($d['Street Address'])
                ->setRawCity($d['City'])
                ->setRawState($d['State'])
                ->setRawZip($d['Zip'])
                ->setRawFullAddress($fullAddress);

            // if  geocoder, fetch the records
            if ($geocoding) {
                if ($input->getOption('verbose')) {
                    printf("Geocoding %s\n", $fullAddress);
                }

                $googleGeo = $this->geocoder->fetch(['address' => $fullAddress], $debug = 0);
                if (empty($googleGeo)) {
                    // printf("...Skipping, problem with geocoder\n");
                    continue;
                }
                // tt::dump($googleGeo, 1);
            }
            $name = tt::normalize_retailer_name($d['Retailer Name']);

            if (!$geocoding || empty($googleGeo->mrd_address)) {
                if ($geocoding) {
                    $progress->setMessage("No geocoding for $fullAddress");
                    if ($input->getOption('verbose')) {
                        var_dump($googleGeo);
                    }
                }

                $key = sprintf("%s (%s)", $name, $fullAddress);
                $cleanFull = tt::title_case($fullAddress);
            } else {
                $key = sprintf("%s (%s)", $name, $googleGeo->mrd_address);
                $cleanFull = $googleGeo->mrd_address;
            }

            $warningUrl = str_replace(
                '/iceci/enforcementactions/warningletters/',
                '/ICECI/EnforcementActions/WarningLetters/',
                strtolower($d['Link']
                )
            );
            // hack for =hyperlink("http://www.fda.gov/ICECI/EnforcementActions/WarningLetters/tobacco/ucm427583.htm")
            $warningUrl = str_replace('=hyperlink("', '', $warningUrl);
            $warningUrl = str_replace('")', '', $warningUrl);

            if ($geocoding && strlen($googleGeo->state) > 2) {
                // tt::dump($googleGeo);
                printf("State Error (%s): %s\n", $googleGeo->state, $fullAddress);
                continue;
            }
            $ucm = preg_match('{ucm(\d+)}i', $d['Link'], $m)
                ? $m[1]
                : null;

            try {
                $point = null;
                if ($geocoding) {
                    $point = new Point($googleGeo->longitude, $googleGeo->latitude);
                }
                $raw
                    ->setRawRetailerName($d['Retailer Name'])
                    ->setRetailerName($name)
                    ->setFullAddress($cleanFull)
                    ->setStreetAddress((!$geocoding || empty($googleGeo->address)) ? $d['Street Address'] : $googleGeo->address)
                    ->setCity((!$geocoding || empty($googleGeo->city)) ? $d['City'] : $googleGeo->city)// Raw if no Google
                    ->setState((!$geocoding || empty($googleGeo->state)) ? $d['State'] : $googleGeo->state)
                    ->setZip((!$geocoding || empty($googleGeo->zip)) ? $d['Zip'] : $googleGeo->zip)
                    ->setIsWarningSent($d['Link'] <> 'N/A')
                    ->setUcmNumber($ucm)
                    ->setKey($key)
                    ->setMatch($geocoding ? $googleGeo->match : null)
                    ->setTheGeom($point)
                    ->setWarningUrl($warningUrl)
                    ->setDecisionDate(empty($d['Decision Date']) ? null : new \DateTime($d['Decision Date']))
                    ->setDecisionType($d['Decision Type'])
                    ->setInspectionDate(null)
                    ->setIsMinorInvolved($d['Minor Involved'])
                    ->setSaleToMinor($d['Sale to Minor']);

                if ($geocoding) {
                    $raw->setGoogleDataJson(json_encode($googleGeo));
                }

                $em->persist($raw);
            } catch (\Exception $e) {
                var_dump($d);
                $output->writeln("Error saving! {$e->getMessage()}");
                continue;
            }
//            $em->flush();

        }
        if ($buffer) {
            $output->writeln("<info>Final commit</info>");
            $em->flush();
        }

        $progress->finish();

        /*
        $q = RawInspectionQuery::create()
            ->withColumn('count(State)', 'Count')
            ->groupBy('State')
            ->select(array('State', 'Count'))
            ->find();
        foreach ($q as $c)
        {
            // tt::dump($q);
        }
        */
    }

    /**
     * @param string $address
     * @return array
     */
    private
    function processAddress($address)
    {
        $match = [];
        if (preg_match("/^([0-9]+) (.*)/", $address, $match)) {
            return [$match[1], $match[2]];
        } else {
            return [null, $address];
        }
    }

    /**
     * @param string $str
     * @param string $city
     * @param string $state
     * @param string $zip
     * @return string
     */
    function fullAddress($str, $city, $state, $zip)
    {
        return sprintf("%s, %s, %s %s", $str, $city, $state, $zip);
    }

}

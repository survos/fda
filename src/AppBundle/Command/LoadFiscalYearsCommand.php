<?php
/**
 * Created by PhpStorm.
 * User: tac
 * Date: 1/15/15
 * Time: 8:31 AM
 */

namespace AppBundle\Command;

use AppBundle\Entity\FiscalYear;
use AppBundle\Entity\RawInspection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class LoadFiscalYearsCommand extends ContainerAwareCommand
{

    /** @var  Input */
    private $input;
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('fda:load-fiscal-years')
            ->addOption('reset', null, InputOption::VALUE_NONE, 'If set, reset everything')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, "Limit import per year", 50)
            ->setDescription('Load the metadata about the fiscal year files');
    }

    /**
     * @param  InputInterface $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $dataDir = $this->getContainer()->getParameter('kernel.root_dir').'/../var/data';
        if (!file_exists($dataDir)) {
            mkdir($dataDir);
        }
        if (!is_writeable($dataDir)) {
            throw new \Exception('Folder is not writable: '.$dataDir);
        }
        foreach (range(2016, 2017) as $year) {
            $url = sprintf("https://www.accessdata.fda.gov/scripts/oce/inspections/generatedExcelReports/OCE_FY%s.csv", $year);
            $filename = $dataDir.'/'.basename($url);
            if (!file_exists($filename)) {
                $output->writeln("Downloading $url", true);
                $csv = file_get_contents($url);
                file_put_contents($filename, $csv);
            }
        }
        $this->loadFiscalYears($dataDir, $input->getOption('reset'), $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE);
    }

    public function loadFiscalYears($dataDir, $reset = false, $verbose = 0)
    {
        $em = $this->getContainer()
            ->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(FiscalYear::class);

        $finder = new Finder();
        $finder->files()->in($dataDir);

        $validator = $this->getContainer()->get('validator');
        foreach ($finder as $file) {
            $stats = [];
            if (preg_match('/FY(\d{4})/', $file->getRealPath(), $m)) {
                $year = $m[1];
            } else {
                continue;
            }

            /** @var FiscalYear $fy */
            if (!$fy = $repo->findOneBy(['year' => $year])) {
                $fy = new FiscalYear();
                $em->persist($fy);
            }
            $csv = file_get_contents($file->getRealPath());

            // replace the first line blanks only
            $lines = explode("\n", $csv);
            dump($lines[0]);
            $lines[0] = str_replace(' ', '', $lines[0]);
            printf("'%s'\n", join("', '", explode(',', $lines[0])));

            // glue the csv back together
            $csv = join("\n", $lines);


            $fy
                ->setFileTimestamp(new \DateTime())
                ->setYear($year)
                ->setLineCount(count($lines) - 1);


            $serializer = new Serializer([new ObjectNormalizer(), new GetSetMethodNormalizer(), new ArrayDenormalizer()], [new CsvEncoder()]);

            // $serializer = $this->getContainer()->get('serializer');
            // $data = $serializer->deserialize(file_get_contents($fn), RawInspection::class,'csv');
            $data = $serializer->decode($csv, 'csv');
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            printf("%d objects loaded", count($data));
            $lc = $violationCount =  0;
            foreach ($data as $inspection) {
                $md5 = md5(json_encode($inspection));

                if (!isset($inspection['Link']))
                {
                    dump($inspection);
                    continue;
                }
                if (preg_match('/(http.*)"/', $inspection['Link'], $m)) {
                    $inspection['Link'] = $m[1];
                }
                $lc++;

                $decisionType = $inspection['DecisionType'];
                $stats[$decisionType] = empty($stats[$decisionType]) ? 1 : $stats[$decisionType] + 1;

                if ($decisionType != 'No Violations Observed') {
                    if (!$entity = $em->getRepository(RawInspection::class)->findOneBy(['hash' => $md5])) {
                        $entity = new RawInspection();
                        $em->persist($entity);
                        $entity
                            ->setHash($md5);
                }
                    $entity
                        ->setFiscalYear($fy)
                        ->setLineNumber($lc); // really need lc and filename.  MD5 could be that, too.
                    $entity->setRawCsv(json_encode($entity));
                    foreach ($inspection as $var => $val) {
                        $method = 'set' . $var;
                        $entity->$method($val);
                    }
                    $violationCount++;
                    if (($limit = $this->input->getOption('limit')) && $violationCount >= $limit ) {
                        break;
                    }

                    $errors = $this->getContainer()->get('validator')->validate($entity);
                    if (count($errors)) {
                        dump($errors); die('error!');
                    }
                    // dump($inspection, $entity);

                    $errors = $validator->validate($entity);
                    if (count($errors)) {
                        dump($errors);
                    }
                }

            }
            $fy->setStats($stats);

            $errors = $validator->validate($fy);
            if (count($errors)) {
                dump($errors);
            }


            $em->flush();
        } // foreach year
    }
}

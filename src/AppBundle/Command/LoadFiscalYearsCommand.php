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


            $this->loadFiscalYears($input->getOption('reset'), $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE);
    }

    public function loadFiscalYears($reset = false, $verbose = 0)
    {
        $em = $this->getContainer()
            ->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(FiscalYear::class);

        $finder = new Finder();
        $finder->files()->in('var/data');

        foreach ($finder as $file) {
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

                if (preg_match('/(http.*)"/', $inspection['Link'], $m)) {
                    $inspection['Link'] = $m[1];
                }
                $lc++;

                if ($inspection['DecisionType'] != 'No Violations Observed') {
                    if (!$entity = $em->getRepository(RawInspection::class)->findOneBy(['hash' => $md5])) {
                        $entity = new RawInspection();
                        $em->persist($entity);
                        $entity
                            ->setHash($md5);
                }
                    $entity
                        ->setLineNumber($lc); // really need lc and filename.  MD5 could be that, too.
                    $entity->setRawCsv(json_encode($entity));
                    foreach ($inspection as $var => $val) {
                        $method = 'set' . $var;
                        $entity->$method($val);
                    }
                    $violationCount++;
                    dump($entity);
                    if ($violationCount >= $this->input->getOption('limit')) {
                        break;
                    }
                }
                // dump($inspection, $entity);
            }
        }
        $em->flush();
    }
}

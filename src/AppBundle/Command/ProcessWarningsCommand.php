<?php //
namespace AppBundle\Command;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DomCrawler\Crawler;
use AppBundle\Entity\RawInspection;
use AppBundle\Entity\Statute;
use DateTime;
use Twig\Cache\FilesystemCache;

/**
 * @package AppBundle\Command
 */
class ProcessWarningsCommand extends ContainerAwareCommand
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var FilesystemAdapter */
    private $cache;

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('fda:process')
            ->setDescription('Process downloaded warnings from FDA site')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, "Limit ", 5)
            ->addOption('use-json-cache', null, InputOption::VALUE_NONE, 'If set, only extract warnings with no json')
            ->addOption('purge-violations', null, InputOption::VALUE_NONE, 'If set, ViolationList=null')
            ->addOption('purge-html', null, InputOption::VALUE_NONE, 'If set, set WarningHtml=null')
            ->addOption('purge-statutes', null, InputOption::VALUE_NONE, 'If set, purge statutes first')
            ->addOption('pdf-only', null, InputOption::VALUE_NONE, 'PDF files only')
            ->addOption('year', null, InputOption::VALUE_OPTIONAL, 'Load data for year', null)// , date('Y'))
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Warning ID (to do only one)')
            ->addOption('reference', null, InputOption::VALUE_OPTIONAL, 'Reference (to do only one)')
            ->addOption('ucm', null, InputOption::VALUE_OPTIONAL, 'UCM (to do only one)');
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

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $id = $input->getOption('id');
        $verbose = $input->getOption('verbose');

        $repo = $em->getRepository(RawInspection::class);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $repo->createQueryBuilder('p')
            ->leftJoin('p.violations', 's', 's')
            ->groupBy('p.id');

        if ($ref = $input->getOption('reference')) {
            //            $warnings->filterByReferenceNumber($ref);
            $queryBuilder->andWhere("p.referenceNumber = :referenceNumber")
                ->setParameter('referenceNumber', $ref);
        } elseif ($ucm = $input->getOption('ucm')) {
            $queryBuilder->andWhere("p.ucmNumber = :ucm")
                ->setParameter('ucm', $ucm);
        } elseif ($input->getOption('pdf-only')) {
            $queryBuilder->andWhere("p.decisionType = :dt")
                ->setParameter('dt', 'Civil Money Penalty');
        } elseif ($id) {
            $queryBuilder->andWhere(['p.id' => $id]);
        } else {

//            $queryBuilder->andHaving('p.statuteCount = 0');
            $queryBuilder->andHaving($queryBuilder->expr()->eq($queryBuilder->expr()->count('s.id'), 0));
            $queryBuilder->andWhere(
                $queryBuilder->expr()->isNotNull('p.warningHtml')
            );
            $queryBuilder->andWhere("p.warningHtml <> ''");
        }
        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit);
        }
        /*
        if ($input->getOption('use-json-cache')) {
            $warnings->filterByWarningJson(null);
        }

        $output->writeln(sprintf("<info>%s records with data to extract</info>", $warnings->count()));
        */

        /** @var Query $query */
        $query = $queryBuilder->getQuery();
        foreach ($query->getResult() as $warning) {
            if ($verbose) {
                $output->writeln(sprintf("<info>%s</info>", $warning->getUcmNumber()));
            }
            $html = $warning->getWarningHtml();

            // Replace nonbreaking spaces with normal spaces:
            $html = preg_replace('/&nbsp;|&#160;|\xc2?\xa0/', ' ', $html);

            $data = [];
            $statuteList = [];

            // if civil fine, this is all in text.  One could argue that we should skip all the HTML markup and go to text, too.
            /* what we want
            The $ of the fine  (also on pg 1, paragraph 1)
            Date of letter
            Date of the NEW violation (on first pg, paragraph 1)
            */
            if ($warning->getDecisionType() == 'Civil Money Penalty') {
                if (preg_match('{\$([\d,]+)}', $html, $m)) {
                    $data['fine'] = $m[1];
                    $warning->setCivilFine(str_replace(',', '', $m[1]));
                }
                if (preg_match('{\nDATED: (.+)}', $html, $m)) {
                    // Occasionally a bunch of spaces and "Respectfully submitted," are stuck on the end:
                    $date = preg_replace('/\s{4,}.*$/', '', $m[1]);
                    $warning->setWarningDate(new DateTime($date));
                    $data['warning_date'] = $warning->getWarningDate();
                }
                $cleanHtml = $warning->getCleanHtml();
                if (preg_match('{(<h3>[^\n]*HISTORY.*?)(?=<h3>)}s', $cleanHtml, $m)) {
                    $violationHistory = $m[1];
                    $warning->setViolationHistory($violationHistory);
                    if (preg_match('/\sconducted\s+on\s+([A-Z][a-z]+\s+\d\d?)(?:\s+and\s+(?:[A-Z][a-z]+\s+)?\d\d?)?,?\s+(20\d\d)\b/',
                                   $violationHistory, $m)) {

                        $date = $m[1].' '.$m[2];
                        $date = str_replace("\n", "", $date);
                        $warning
                            ->setInspectionDate(new DateTime($date))
                            ->setInspectionYear($m[2]);
                    }
                    if (preg_match(
                        '{^(<p>\d+\.\s+Most recently.*?)(?=<p>\d+\.)}sm',
                        $violationHistory,
                        $m
                    )) {
                        $this->extractStatutes($m[1], $warning);
                    }
                }
                if (preg_match('{\n(1\.\s+.*?)\n2\.\s}s', $html, $m)) {
                    $firstPara = $m[1];
                    $statuteList = $this->extractStatutes($firstPara, $warning);
                }
            } else {
                $crawler = new Crawler();
                $crawler->addContent($html);
                foreach ($crawler->filter('meta') as $meta) {
                    $name = $meta->getAttribute('name');
                    if (!empty($name)) {
                        $data[$name] = $meta->getAttribute('content');
                    }
                }
                if (isset($data['letterdatestamp'])) {
                    $warning->setWarningDate(new DateTime($data['letterdatestamp']));
                }
                $lookingForViolations = false;
                $block = $crawler->filter('div.middle-column article, div.middle-column2 article');
                if (!$block->count()) {
                    $block = $crawler->filter('div.middle-column, div.middle-column2');
                }
                try {
                    if (!$block->count()) {
                        throw new \Exception("Can't find warning letter content");
                    }
                    foreach ($block->children() as $child) {
                        if (!in_array($child->nodeName, ['p', 'div', 'span'])) {
                            continue;
                        }
                        $text = trim($child->textContent);
                        //print $child->nodeName . ': ' . $text . "\n";
                        if (preg_match('/^On\s+([A-Z][a-z]+\s+\d\d?)(?:\s+and\s+(?:[A-Z][a-z]+\s+)?\d\d?)?[,\s]+(20\d\d)\b/', $text, $m)) {
                            $data['inspection_date_raw'] = $m[1].', '.$m[2];
                            $warning->setInspectionDate(new DateTime($data['inspection_date_raw']))
                                ->setInspectionYear($m[2]);
                            $lookingForViolations = true;
                        } elseif (preg_match('/Reference\s+Number:\s+(\w+)/', $text, $m)) {
                            $warning->setReferenceNumber($m[1]);
                        } elseif ($lookingForViolations && $child->getAttribute('style') == 'margin-left: 40px') {
                            //if (preg_match('/^(\d\d?)\./'))
                        } else {
                            // printf("Failed to find date, etc., in letter\n");
                            $lookingForViolations = false;
                        }
                        $statuteList = array_merge($statuteList, $this->extractStatutes($text, $warning));
                    }
                } catch (\Exception $e) {
                    // should throw another exeception that caller can catch
                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                        printf("Error on %s: %s\n", $warning->getUcmNumber(), $e->getMessage());
                    }
                }
            }

            if ($data) {
                $warning
//                    ->setStatuteCount($warning->getViolations()->count())
                    ->setWarningJson(json_encode($data));
            }

            if ($input->getOption('verbose')) {
                // var_dump($warning->getWarningJson());
            }

            $this->getManager()->persist($warning);

            $violations = $warning->getViolationsList();
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln(sprintf("%d statutes for UCM %s saved (%s).\n",
                    count($violations),
                    $warning->getUcmNumber(),
                    $violations
                ));
            }

            // tt::dump($warning, 1);
        }

        $this->getManager()->flush();
    }

    /**
     * @param  string $text
     * @param  RawInspection $warning
     * @return string[]
     */
    private
    function extractStatutes($text, RawInspection $warning)
    {
        $statuteList = [];
        if (preg_match_all('/\d{2}\s+(\w\.){3}\s*§+\s*([a-zA-Z0-9]+)(\.[a-zA-Z0-9]+)?(\([a-zA-Z0-9]\))*/u',
                           $text, $matches)) {
            foreach ($matches[0] as $match) {
                if (strstr($match, 'C.F.R.') or strstr($match, 'U.S.C')) {
                    $statute = $this->addStatute($warning, $match);
                    $statuteList[] = $statute->getVarCode();
                } else {
                    printf("Skipping Statute %s\n", $match);
                }
            }
        } else {
            //printf("No statutes found.\n");
        }

        return $statuteList;
    }

    private
    function updateCounts()
    {
        $em = $this->getManager();
        $em->createQueryBuilder();
        // $query = $em->createQuery('SELECT u, count(g.id) FROM Entities\User u JOIN u.groups g GROUP BY u.id');

        $db = $this->createQueryBuilder('s');
        $db->andWhere('COUNT( * ) AS count');
        $db->groupBy('s.');
    }

    /**
     * @param $msg
     */
    private
    function debug($msg)
    {
        $output = $this->output;
        if (1 < $output->getVerbosity()) {
            $output->writeln($msg);
        }
    }

    /**
     * @param  RawInspection $warning
     * @param  string $statuteString
     * @return Statute
     */
    private
    function addStatute(RawInspection $warning, $statuteString)
    {
        /** @var Statute[] $statute */
        static $statute = [];
        $this->debug("<info>    About to add statute $statuteString</info>");

        // The scraper stores the nonbreaking spaces in the
        // DB, so we have to replace it
        /*
        $statuteString = preg_replace(["/&nbsp;|&#160;|\xc2?\xa0/", "/§§/", '/\s+/'],
                                      [" ", "§", " "],
                                      trim($statuteString));
        */
        $statuteString = $this->toAscii($statuteString);

        $this->debug("<comment>    statute $statuteString added</comment>");
        // see http://www.uspto.gov/main/glossary/lawsandrulesdiagram.htm for breakdown of components
        // if components are blank, update them.
        list($title, $citation) = explode('§', $statuteString);
        if (preg_match('{(\d+) (.*)}', $title, $m)) {
            $title_number = $m[1];
            $codebook = trim($m[2]);
        } else {
            $title_number = '';
            $codebook = '';
        }
        $citation = trim($citation);
        if (strpos($citation, '.') && list($part, $section_number) = explode('.', trim($citation))) {
            // var_dump($part, $section_number);
        } else {
            $part = ''; // $citation;
            $section_number = $citation;
        }
        if (preg_match('{(.*?)(\(.*)}', $section_number, $m)) {
            $sect = trim($m[1]);
            $para = trim($m[2]);
        } else {
            $sect = $section_number;
            $para = '';
        }

        if ($part && $codebook == 'C.F.R.') {
            $url = 'http://www.accessdata.fda.gov/scripts/cdrh/cfdocs/cfcfr/CFRSearch.cfm?';
            if ($sect) {
                $url .= "fr=$part.$sect";
            } else {
                $url .= "CFRPart=$part";
            }
        } else {
            $url = '';
        }

        $regulation = strtolower(($part ? "$part." : '')."$sect$para");
        $slug = $this->toAscii($regulation);

        dump($regulation);

        if (!isset($statute[$regulation])) {
            if (!$reg = $this->getManager()->getRepository('AppBundle:Statute')
                ->findOneByCfrRegulation($regulation)
            ) {
                $reg = new Statute();
                $reg->setCfrRegulation($regulation);
            }

            $reg
                ->setVariable($slug)
                ->setTitleNumber($title_number)
                ->setStatuteCode($statuteString)
                ->setCodebook($codebook)
                ->setPart($part)
                ->setSection($sect)
                ->setParagraph($para);
            $statute[$regulation] = $reg;

            $this->getManager()->persist($reg);
        } else {
            $reg = $statute[$regulation];
        }

        // add to many-to-many relation
        $warning->addViolation($reg);

        $this->debug("<comment>    reg $regulation added</comment>");

        return $statute[$regulation];
    }

    private function toAscii($str) {
        return Container::underscore($str);
    }
}

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
 * Class WarningScraperCommand
 * @package AppBundle\Command
 */
class WarningScraperCommand extends ContainerAwareCommand
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
            ->setName('fda:scrape')
            ->setDescription('Scrape warnings from FDA site')
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

        $limit = $input->getOption('limit');
            $this->grabHtml($input, $output, $limit);
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
     */
    private function grabHtml(InputInterface $input, OutputInterface $output, $limit)
    {

        $this->cache = new FilesystemAdapter();


        /** @var ObjectRepository $rawInspectionRepository */
        $qb = $this->getManager()->getRepository('AppBundle:RawInspection')->createQueryBuilder('ri');
        $qb->andWhere('ri.link IS NOT null');
//        $qb->andWhere('ri.warningHtml is null');
//         $qb->andWhere('ri.warningUrl is not null and ri.warningUrl <> \'\'');
        if ($limit>0){
            $qb->setMaxResults($limit);
        }

        if ($input->getOption('year')) {
//            $qb->andWhere('');
        }
        /** @var Query $warningsQuery */
        $warnings = $qb->getQuery()->getResult();
        printf("Warnings: %d\n", count($warnings));

        //var_dump(\BasePeer::createSelectSql($query, $p)); die();
        // $cache = new HttpCache("fda_letters.db3");




        $progress = new ProgressBar($output, count($warnings));
//        $progress->setFormat(" %message%\n %step%/%max%\n Working on %url%");
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');


        $progress->start();

        /** @var RawInspection $warning */
        foreach ($warnings as $warning) {
            $progress->advance();
            $url = $warning->getLink();
            $key = md5($url);

            if (!$url) {
                $progress->setMessage('No URL for warning -- skipping');

                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    $output->writeln('No URL for warning -- skipping');
                }
                continue;
            }
            $cachedFile = $this->cache->getItem($key);
            if (!$cachedFile->isHit()) {
                if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                    $output->writeln("<info>Fetching $url</info>");
                }
                $html = file_get_contents($url);
                $cachedFile->set($html);
                $this->cache->save($cachedFile);
            } else {
                echo "In cache!";
            }
            $content = $cachedFile->get();


            if (preg_match('{\.pdf}', $url)) {
                if (!file_exists($fn)) {
                    try {
                        tt::slurp_to_file($url, $fn);
                    } catch (\Exception $e) {
                        $output->writeln(sprintf("<error>Error in %s\n, %s</error>", $url, $e->getMessage()));
                        continue;
                    }
                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                        $output->writeln("<info>$fn written</info>");
                    }
                }
                $txt_fn = str_replace('pdf', 'txt', $fn);
                if (!file_exists($txt_fn)) {
                    exec("pdftotext -enc UTF-8 -layout $fn");
                }
                $content = str_replace(chr(12), "\n\n", file_get_contents($txt_fn));
                // hack!  from PDF's, this should be in tt or somewhere else
                /*
                $content = str_replace(0xa7, '(Section)', $content);
                $content = str_replace('§', '(Section)', $content);
                */
                // $content = tt::fix_utf8($content);
                // tt::dump($content);
                // continue;
            }

            if ($content) {
                $warning
                    ->setWarningHtml($content);
                $this->getManager()->persist($warning);
            }
        }
        $progress->finish();

        $this->getManager()->flush();
    }

}

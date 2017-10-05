<?php
/**
 * Created by PhpStorm.
 * User: tac
 * Date: 1/15/15
 * Time: 8:31 AM
 */

namespace AppBundle\Command;

use AppBundle\Entity\InspectionsExport;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class ExportInspectionsCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('fda:export')
            ->setDescription('Export all requested data');
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

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $this->expireExports($em, $output);

        $export = $em->getRepository('AppBundle:InspectionsExport')->findOneBy(
            [
                'status' => InspectionsExport::STATUS_INITIATED,
            ],
            [
                'id' => 'ASC'
            ]
        );
        // if any export found - process it and update results
        if (!$export) {
            $output->writeln('No exports found to process');

            return;
        }

        $export->setStatus(InspectionsExport::STATUS_PROGRESS);
        $em->persist($export);
        $em->flush();

        // do export
        try {
            if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $output->writeln("export #".$export->getId());
                $output->writeln("limit: ".$export->getLimit());
                $output->writeln("type: ".InspectionsExport::getReportTypeLabel($export->getReportType()));
            }
            $filename = $this->getContainer()->get('app.export')->exportInspections($export, $output);

            $export->setStatus(InspectionsExport::STATUS_FINISHED);
            $export->setFilename($filename);
            $em->persist($export);
            $em->flush();
        } catch (\Exception $e) {
            $output->writeln("<err>Problem during exporting data: {$e->getMessage()}</err>");

        }
    }

    /**
     * expire exports if past timeout
     */
    private function expireExports(EntityManager $em, $output)
    {
        $now = new \DateTime();
        $now->sub(new \DateInterval('PT'.InspectionsExport::TIMEOUT.'S'));

        $qb = $em->getRepository('AppBundle:InspectionsExport')->createQueryBuilder('e');
        $qb->where($qb->expr()->eq('e.status', InspectionsExport::STATUS_PROGRESS))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->lt('e.updatedAt', ':dateTo'),
                    $qb->expr()->isNull('e.updatedAt')
                )
            )
            ->setParameter('dateTo', $now);

        $exports = $qb->getQuery()->getResult();
        foreach ($exports as $export) {
            $export->setStatus(InspectionsExport::STATUS_INITIATED);
            $em->persist($export);
        }
        $em->flush();
        $output->writeln("<info>Expiring ".count($exports)." exports</info>");
    }
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction()
    {
        /* old way, kept only to see how to do count queries
        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:RawInspection')->createQueryBuilder('i');
        $qb->select('i.fiscalYear, count(i.id) cnt')
            ->groupBy('i.fiscalYear');
        */
//        $qb = $this->getDoctrine()
//            ->getRepository('AppBundle:FiscalYear')->createQueryBuilder('fy')
//            ->orderBy('fy.year');
        $inspections = $this->getDoctrine()
            ->getRepository('AppBundle:RawInspection')->createQueryBuilder('i')
            ->leftJoin('i.fiscalYear', 'fy')
            ->select("fy.stats,fy.lineCount,fy.readCount,fy.id,fy.year,SUM(CASE WHEN i.decisionType = 'Civil Money Penalty' then 1 else 0 end) civilMoneyCount")
            ->addSelect("SUM(CASE WHEN i.decisionType = 'Warning Letter' then 1 else 0 end) warningLetterCount")
            ->addSelect("COUNT(i.id) inspectionCount")
            ->groupBy('fy.id')
            ->orderBy('fy.year')
            ->getQuery()
            ->getResult();
//        dump($inspections->getQuery()->getSQL());
//dump($inspections->getQuery()->getResult());
//        foreach ($inspectionCounts->getQuery()->getResult() as $inspectionYear) {
//            $inspections[$inspectionYear['fiscalYear']] = $inspectionYear;
//        }
        dump($inspections);
        return $this->render(
            'home.html.twig',
            [
                'inspections' => $inspections
            ]
        );
    }

    /**
     * @Route("/app/statutes", name="statutes")
     */
    public function statutesAction()
    {
        $repo = $this->getDoctrine()
            ->getRepository('AppBundle:Statute');
        $q = $repo->createQueryBuilder('p')
            ->setMaxResults(3)
            ->getQuery();

        return $this->render('entities.html.twig', ['q' => $q]);
    }

}

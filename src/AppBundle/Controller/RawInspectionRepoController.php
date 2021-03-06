<?php // autogenerated on DATE by SurvosCrudBundle:Generated:Custom/Controller/{phpName}RepoController.php.twig

namespace AppBundle\Controller;

use AppBundle\Entity\InspectionsExport;
use AppBundle\Entity\RawInspection;
use AppBundle\Entity\Statute;
use AppBundle\Form\InspectionsExportType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tobacco\FDABundle\Form\Type\RawInspectionType;

/**
 * @Route("/inspections")
 */
class RawInspectionRepoController extends Controller // PosseController
{
    /**
     * @Route("/browse.{_format}", name="fda_rawinspection_browse")
     */
    public function browseAction(Request $request, $_format = 'html')
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:RawInspection');
        $qb = $repo->createQueryBuilder('p')
            ->leftJoin('p.violations', 'v', 'v')
            ->select('p inspection,count(v.id) violationsCount')
            ->groupBy('p.id');

        $adapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(20);
        $pager->setCurrentPage($request->get('page', 1));

        return $this->render(
            'inspections.html.twig',
            [
                'pager' => $pager
            ]
        );
    }

    /**
     * @Route("/export_warnings.{_format}", name="fda_export_warning")
     */
    public function exportWarningsAction(Request $request, $_format = 'html')
    {
        $csvFile = $this->get('app.export')->exportInspections();
//        if ($_format == 'html') {
//            return ['csvFile' => $csvFile, 'contents' => file_get_contents($csvFile)];
//            print "<pre>";
//            print substr(file_get_contents($csvFile), 0, 512);
//            die();
//        }
        // return $this->redirect("/inspections.txt");
        $response = new HttpResponse(file_get_contents($csvFile));
        if ($_format == 'csv') {
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $exportFilename.'.csv'));
        }
        @unlink($csvFile);

        return $response;
    }

    /**
     * @Route("/new", name="crud_fda_RawInspection_new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $rawInspection = new RawInspection();
        $form = $this->createForm(new RawInspectionType(), $rawInspection);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $rawInspection->save();
            $redirectRoute = 'crud_fda_RawInspection_display';
            // $key['id'] = $rawInspection->getId();

            return $this->redirect($this->generateUrl($redirectRoute, $rawInspection->getRouteParams()));
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/export", name="fda_export_inspections")
     * @Template()
     */
    public function exportAction(Request $request)
    {
        $export = new InspectionsExport();
        $export->setUser($this->getUser());
        $export->setBaseFilename('export');

        $form = $this->createForm(new InspectionsExportType(), $export);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $fn = $export->getWarningsOnly()?'warnings':'inspections';
            if (!$export->getBaseFilename()) {
                $export->setBaseFilename($fn);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($export);
            $em->flush();
            return $this->redirectToRoute('fda_export_inspections');
        }

        $exports = $this->getDoctrine()->getRepository('AppBundle:InspectionsExport')->findBy([], ['id' => 'DESC']);

        return [
            'form'    => $form->createView(),
            'exports' => $exports
        ];
    }
    /**
     * @Route("/export/{id}/restart", name="fda_export_restart")
     */
    public function exportRestartAction(Request $request, InspectionsExport $export)
    {

        $export->setStatus(InspectionsExport::STATUS_INITIATED);
        if ($request->get('delete', false)) {
            $this->getDoctrine()->getManager()->remove($export);
        } else {
            $this->getDoctrine()->getManager()->persist($export);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('fda_export_inspections');
    }

    /**
     * @Route("/export/{id}.{_format}", name="fda_export_file")
     */
    public function exportFileAction(Request $request, InspectionsExport $export, $_format = 'csv')
    {
        if (file_exists($export->getFilename())) {
            $response = new HttpResponse(file_get_contents($export->getFilename()));
            $filename = basename($export->getFilename());
            if ($_format == 'csv') {
                $response->headers->set('Content-Type', 'text/csv');
                $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $filename));
            }
        } else {
            $this->get('session')->getFlashBag()->add('error', "Export file couldn't be found");
            $export->setStatus(InspectionsExport::STATUS_ERROR);
            $this->getDoctrine()->getManager()->persist($export);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('fda_export_inspections');
        }

        return $response;
    }

    /**
     * @Route("/dt.{_format}", name="crud_fda_RawInspection_dt", defaults={"_format"="json"})
     * @Template("SurvosCrudBundle::dt.html.twig")
     */
    public function dtAction(Request $request, $_format)
    {
        $query = RawInspectionQuery::create();
        $filters = $request->get('filters', []);
        // could use $query->filterByArray($filters); ?

        foreach ($filters as $filter => $condition) {
            $query->{'filterBy'.$filter}($condition);
        }

        if ($columns = $request->get('columns', '')) {
            $query->select(explode(',', $columns));
        }

        return ($_format == 'json')
            ? new JsonResponse($query->dtResponse())
            : ['query' => $query];
    }

    /**
     * @Route("/map", name="app_inspections_map")
     */
    public function mapAction(Request $request)
    {

        return $this->render(
            'AppBundle:RawInspectionRepo:map.html.twig',
            [

            ]
        );
    }
}

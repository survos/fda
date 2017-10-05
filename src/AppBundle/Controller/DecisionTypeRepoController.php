<?php // autogenerated on DATE by SurvosCrudBundle:Generated:Custom/Controller/{phpName}RepoController.php.twig

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\PropelAdapter;

use Tobacco\FDABundle\Model\DecisionTypeQuery;
use Tobacco\FDABundle\Model\DecisionType;
use AppBundle\Form\Type\DecisionTypeType;
// use AppBundle\Controller\BaseDecisionTypeController;
use Posse\SurveyBundle\Controller\PosseController;

/**
 * @Route("/fda/DecisionTypeRepo")
 */
class DecisionTypeRepoController extends Controller // PosseController
{
    // copy and modify this function into DataTablesBuilder.php
    /*
        use Tobacco\FDABundle\Model\DecisionTypeQuery;
        use Tobacco\FDABundle\Model\DecisionType;
    */
    /**
     * @return DataTable
     */
    public function buildDecisionTypeTable()
    {
        $query = DecisionTypeQuery::create();

        $this->dataTableBuilder
            ->setQuery($query)#set query object
            ->setFilterCallback(  # implement filters
                function ($query, $searchQuery) {
                    $query = DecisionTypeQuery::create();
                    if ($searchQuery) {
                        $query
                            ->filterByName("%$searchQuery%");
                    }
                    return $query->find();
                }
            );

        // add columns and it's data
        $this->dataTableBuilder
            ->addColumn('Id', 'Id', /* INTEGER */
                function (DecisionType $decisiontype) {
                    // $text = $this->templating->render('AppBundle:DecisionType:partials\_id.html.twig', ['d' => $decisiontype]);
                    $url = $this->router->generate('fda_decisionType_show', ['id' => $decisiontype->getId()]);
                    $text = "<a href='{$url}'>Show</a>";
                    return $text;

                })->addColumn('Name', 'Name', /* VARCHAR */
                function (DecisionType $decisiontype) {
                    // $text = $this->templating->render('AppBundle:DecisionType:partials\_name.html.twig', ['d' => $decisiontype]);
                    return $decisiontype->getName();

                })->addColumn('InspectionCount', 'InspectionCount', /* INTEGER */
                function (DecisionType $decisiontype) {
                    // $text = $this->templating->render('AppBundle:DecisionType:partials\_inspection_count.html.twig', ['d' => $decisiontype]);
                    return $decisiontype->getInspectionCount();

                });

        $this->dataTableBuilder->bindRequest();

        return $this->dataTableBuilder;

    }

    /**
     * @Route("/browse.{_format}", name="fda_decisiontype_browse")
     * @Template()
     */
    public function browseAction(Request $request, $_format = 'html')
    {

        $repo = $this->getDoctrine()
            ->getRepository('AppBundle:DecisionType');
        $q = $repo->createQueryBuilder('p')
            ->setMaxResults(3)
            ->getQuery();

        return $this->render('entities.html.twig', ['title' => 'Decision types', 'q' => $q]);

        /** @var \Posse\ServiceBundle\Services\DataTableBuilder $service */
        $service = $this->get('survos.datatable');
        $table = $service->buildDecisionTypeTable();

        // for server side processing return ajax json for datatable
        if ($_format == 'json') {
            return $table->getJsonResponse();
        }

        return [
            'table' => $table,
        ];

    }

    /**
     * @Route("/", name="fda_decisiontype_index")
     * @Template()
     */
    public function indexAction()
    {
        return array_merge(
            parent::indexAction(),
            [
                'display_route' => "crud_fda_DecisionType_display",
            ]
        );
    }

    /**
     * @Route("/new", name="crud_fda_DecisionType_new")
     * @Template()
     */
    function newAction(Request $request)
    {
        $decisionType = new DecisionType();
        $form = $this->createForm(new DecisionTypeType(), $decisionType);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $decisionType->save();
            $redirectRoute = 'crud_fda_DecisionType_display';
            // $key['id'] = $decisionType->getId();

            return $this->redirect($this->generateUrl($redirectRoute, $decisionType->getRouteParams()));
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/dt.{_format}", name="crud_fda_DecisionType_dt", defaults={"_format"="json"})
     * @Template("SurvosCrudBundle::dt.html.twig")
     */
    function dtAction(Request $request, $_format)
    {
        $query = DecisionTypeQuery::create();
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

}


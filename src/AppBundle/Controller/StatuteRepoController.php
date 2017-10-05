<?php // autogenerated on DATE by SurvosCrudBundle:Generated:Custom/Controller/{phpName}RepoController.php.twig

namespace AppBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\PropelAdapter;

use Tobacco\FDABundle\Model\StatuteQuery;
use Tobacco\FDABundle\Model\Statute;
use AppBundle\Form\Type\StatuteType;
// use AppBundle\Controller\BaseStatuteController;
use Posse\SurveyBundle\Controller\PosseController;

/**
 * @Route("/fda/StatuteRepo")
 */
class StatuteRepoController extends Controller // PosseController
{
    // copy and modify this function into DataTablesBuilder.php
    /*
        use Tobacco\FDABundle\Model\StatuteQuery;
        use Tobacco\FDABundle\Model\Statute;
    */
    /**
     * @return DataTable
     */
    public function buildStatuteTable()
    {
        $query = StatuteQuery::create();

        $this->dataTableBuilder
            ->setQuery($query)#set query object
            ->setFilterCallback(  # implement filters
                function ($query, $searchQuery) {
                    $query = StatuteQuery::create();
                    if ($searchQuery) {
                        $query
                            ->filterByRegulation("%$searchQuery%")
                            ->_or()
                            ->filterByTitle("%$searchQuery%")
                            ->_or()
                            ->filterByDescription("%$searchQuery%")
                            ->_or()
                            ->filterByCodebook("%$searchQuery%")
                            ->_or()
                            ->filterByPart("%$searchQuery%")
                            ->_or()
                            ->filterBySection("%$searchQuery%")
                            ->_or()
                            ->filterByParagraph("%$searchQuery%")
                            ->_or()
                            ->filterByCode("%$searchQuery%")
                            ->_or()
                            ->filterByVariable("%$searchQuery%")
                            ->_or()
                            ->filterByUrl("%$searchQuery%");
                    }
                    return $query->find();
                }
            );

        // add columns and it's data
        $this->dataTableBuilder
            ->addColumn('Id', 'Id', /* INTEGER */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_id.html.twig', ['d' => $statute]);
                    $url = $this->router->generate('fda_statute_show', ['id' => $statute->getId()]);
                    $text = "<a href='{$url}'>Show</a>";
                    return $text;

                })->addColumn('Regulation', 'Regulation', /* VARCHAR */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_cfr_regulation.html.twig', ['d' => $statute]);
                    return $statute->getRegulation();

                })->addColumn('Title', 'Title', /* VARCHAR */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_short_title.html.twig', ['d' => $statute]);
                    return $statute->getTitle();

                })->addColumn('TitleNumber', 'TitleNumber', /* INTEGER */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_title_number.html.twig', ['d' => $statute]);
                    return $statute->getTitleNumber();

                })->addColumn('Codebook', 'Codebook', /* VARCHAR */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_codebook.html.twig', ['d' => $statute]);
                    return $statute->getCodebook();

                })->addColumn('Part', 'Part', /* VARCHAR */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_part.html.twig', ['d' => $statute]);
                    return $statute->getPart();

                })->addColumn('Section', 'Section', /* VARCHAR */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_section.html.twig', ['d' => $statute]);
                    return $statute->getSection();

                })->addColumn('Paragraph', 'Paragraph', /* VARCHAR */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_paragraph.html.twig', ['d' => $statute]);
                    return $statute->getParagraph();

                })->addColumn('Code', 'Code', /* VARCHAR */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_statute_code.html.twig', ['d' => $statute]);
                    return $statute->getCode();

                })->addColumn('Variable', 'Variable', /* VARCHAR */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_variable.html.twig', ['d' => $statute]);
                    return $statute->getVariable();

                })->addColumn('WarningCount', 'WarningCount', /* INTEGER */
                function (Statute $statute) {
                    // $text = $this->templating->render('AppBundle:Statute:partials\_warning_count.html.twig', ['d' => $statute]);
                    return $statute->getWarningCount();

                });

        $this->dataTableBuilder->bindRequest();

        return $this->dataTableBuilder;

    }

    /**
     * @Route("/browse.{_format}", name="fda_statute_browse")
     */
    public function browseAction(Request $request, $_format = 'html')
    {

        $repo = $this->getDoctrine()
            ->getRepository('AppBundle:Statute');
        /** @var QueryBuilder $q */
        $statutes = $repo->createQueryBuilder('s')
            ->innerJoin('s.inspections', 'i', 'i')
            ->select('s inspection,count(i.id) cnt')
            ->groupBy('s.id')
            ->orderBy('cnt','DESC')
            ->getQuery()->getResult();

        return $this->render('statute_browse.html.twig', ['statutes' => $statutes]);

    }

    /**
     * @Route("/", name="fda_statute_index")
     * @Template()
     */
    public function indexAction()
    {
        return array_merge(
            parent::indexAction(),
            [
                'display_route' => "crud_fda_Statute_display",
            ]
        );
    }

    /**
     * @Route("/new", name="crud_fda_Statute_new")
     * @Template()
     */
    function newAction(Request $request)
    {
        $statute = new Statute();
        $form = $this->createForm(new StatuteType(), $statute);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $statute->save();
            $redirectRoute = 'crud_fda_Statute_display';
            // $key['id'] = $statute->getId();

            return $this->redirect($this->generateUrl($redirectRoute, $statute->getRouteParams()));
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/dt.{_format}", name="crud_fda_Statute_dt", defaults={"_format"="json"})
     * @Template("SurvosCrudBundle::dt.html.twig")
     */
    function dtAction(Request $request, $_format)
    {
        $query = StatuteQuery::create();
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

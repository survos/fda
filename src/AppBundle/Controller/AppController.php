<?php

namespace AppBundle\Controller;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AppController extends Controller
{
    // this exists so that the class data is a only set in one spot, but likely there's a better way
    /**
     * @return MenuItem
     */
    public function createMenuObject()
    {
        $factory = $this->getMenuFactory();
        // $id = $worker->getId();
        $menu = $factory->createItem('root');
        $menu->setCurrentUri($this->container->get('request')->getRequestUri());
        $menu->setChildrenAttribute('class', 'nav nav-pills pull-right'); // outer ul

        return $menu;
    }


    // helper function for creating geoJson. Likely should be in SurvosController or CommonController or WebController
    /**
     * @param $id
     * @param $latitude
     * @param $longitude
     * @param  array $properties
     * @return array
     */
    public function createPointFeature($id, $latitude, $longitude, Array $properties)
    {
        return [
            'type' => 'Feature',
            'id' => $id,
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [(float) $longitude, (float) $latitude],
            ],
            'properties' => $properties
        ];
    }

    /**
     * @param  Request    $request
     * @return bool|mixed
     */
    public function getPostedJson(Request $request, $asArray = null)
    {
        if (!$json = $request->get('JSON')) {
            $json = $request->getContent();
        }
        $data = json_decode($json, $asArray);
        if ($data == null) {
            return false;
            // throw new \Exception("Invalid Json" . $json);
        }

        return $data;
    }

    /**
     * @param  object       $geoJsonData
     * @return JsonResponse
     */
    public function GeoJsonResponse($geoJsonData)
    {
        return new JsonResponse($geoJsonData,
            200,
            array(
                'Access-Control-Allow-Origin' => '*',
            ));
    }

    /**
     * @return MenuFactory
     */
    public function getMenuFactory()
    {
        return $this->get('knp_menu.factory');
    }


    /**
     * @return bool
     */
    public function getIsLoggedIn()
    {
        return $this->get('security.context')->isGranted('ROLE_USER');
    }


    /**
     * @param  string $text
     * @param  int    $count
     * @return string
     */
    public function menuLabel($text, $count)
    {
        return $count
            ? sprintf("%s <sup>%d</sup>", $text, $count)
            : "No ".$text;
    }

    /**
     * @param  string $text
     * @param  int    $count
     * @return string
     */
    public function dropdownMenuLabel($text, $count)
    {
        return $count
            ? sprintf("<i>%s</i>", $text)
            : sprintf("%s <span class='pull-right pill'>%d</span>", $text, $count);
    }

    public function jsonResponse($data, $_format = 'json')
    {
        return $_format == 'json'
            ? new JsonResponse($data)
            : $this->render('::debugResults.html.twig', ['results' => $data])
            ;
    }

}

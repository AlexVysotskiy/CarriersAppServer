<?php

namespace UserBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Контроллер для отдачи регионов и городов
 */
class GeoControllerController extends Controller
{

    use \UserBundle\Helper\ControllerHelper;

    /**
     * @Route("/regions_list", name="api_v1_regions_list")
     * @Method("GET")
     */
    public function regionsListAction(Request $request)
    {

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\Region');

        $list = $repo->findAll();

        $response = new Response($this->serialize(
                        array('list' => $list)), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }

    /**
     * @Route("/cities_list/{regionId}", name="api_v1_cities_list")
     * @Method("GET")
     */
    public function citiesListAction(Request $request)
    {
        $response = array();

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        try {

            /* @var $regionRepo \Doctrine\ORM\EntityRepository */
            $regionRepo = $em->getRepository('UserBundle\Entity\Region');

            $regionId = $request->get('regionId');
            /* @var $region \UserBundle\Entity\Region */
            if ($region = $regionRepo->find($regionId)) {

                $response['list'] = $region->getCities();
                $response['region'] = array(
                    'id' => $region->getId(),
                    'name' => $region->getName(),
                );
            } else {

                throw new \Exception('Region with given id not found!');
            }
        } catch (\Exception $e) {
            
            return $this->raiseError($e->getCode(), $e->getMessage());
        }

        $response = new Response($this->serialize($response), Response::HTTP_OK);
        return $this->setBaseHeaders($response);
    }

}

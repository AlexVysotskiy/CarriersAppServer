<?php

namespace UserBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use UserBundle\Entity\User;
use UserBundle\Service\MyUserManager;

/**
 * Description of MainController
 *
 * @author Alexander
 */
class CarriersController extends Controller
{

    use \UserBundle\Helper\ControllerHelper;

    /**
     * @Route("/carriers_list/{cityId}", name = "api_v1_carriers_list")
     * @Method("POST")
     */
    public function carriersListAction(Request $request)
    {
        $cityId = abs(intval($request->get('cityId')));

        $lastId = $request->get('lastId');
        $count = abs(intval($request->get('count')));
        if (!$count) {
            $count = 30;
        }

        $list = $this->getCarriersList(array(
            'city' => $cityId
                ), $lastId, $count);

        $response = new Response($this->serialize(
                        array('list' => $list)
                ), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }

    /**
     * @Route("/carriers_list_filtered/{cityId}/{cargoType}", name = "api_v1_carriers_list")
     * @Method("POST")
     */
    public function carriersListFilteredByTypeAction(Request $request)
    {
        $cityId = abs(intval($request->get('cityId')));
        $cargoType = $request->get('cargoType');

        $lastId = $request->get('lastId');
        $count = abs(intval($request->get('count')));
        if (!$count) {
            $count = 30;
        }

        $list = $this->getCarriersList(array(
            'city' => $cityId,
            'cargoType' => $cargoType
                ), $lastId, $count);

        $response = new Response($this->serialize(
                        array('list' => $list)
                ), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }

    protected function getCarriersList($conditions = array(), $lastId = null, $count = 30)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        $query = "select u from UserBundle\Entity\User u where";

        foreach ($conditions as $name => $value) {
            $query .= " u.$name = $value and";
        }

        $query = rtrim($query, 'and');

        if ($lastId) {
            $query .= " and u.id < $lastId";
        }

        $query .= " LIMIT $count";

        return $em->createQuery($query)->getResult();
    }

    /**
     * @Route("/carrier_info/{carrierId}", name = "api_v1_carrier_info")
     * @Method("POST")
     */
    public function carrierInfoAction(Request $request)
    {
        $carrierId = abs(intval($request->get('carrierId')));

        /* @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $carrier = $userManager->findUserBy(array('id' => $carrierId));


        $response = new Response($this->serialize(
                        array('carrier' => $carrier)
                ), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }

}

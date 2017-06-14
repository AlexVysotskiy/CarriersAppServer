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

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\User');
        $list = $repo->findBy(array(
            'city' => $cityId
        ));

        $response = new Response($this->serialize(
                        array('list' => $list)
                ), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
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

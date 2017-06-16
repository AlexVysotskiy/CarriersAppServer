<?php

namespace UserBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("is_granted('ROLE_USER')")
 */
class AuthController extends Controller
{

    /**
     * @Route("/user_info", name="api_v1_user_info")
     * @Method("POST")
     */
    public function userInfoAction(Request $request)
    {

        $response = new Response($this->serialize(array(
                    'user' => $this->getUser()
                )), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }

}

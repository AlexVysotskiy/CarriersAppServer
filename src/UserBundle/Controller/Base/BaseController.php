<?php

namespace UserBundle\Controller\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use UserBundle\Entity\User;
use UserBundle\Service\MyUserManager;

/**
 * @author Alexander
 */
class BaseController extends Controller
{

    /**
     * @Route("/", name="default_route")
     */
    public function defaultAction(Request $request)
    {
        return new Response('Hello here!');
    }

}

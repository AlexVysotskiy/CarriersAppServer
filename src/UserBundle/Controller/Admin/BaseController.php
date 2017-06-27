<?php

namespace UserBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Админка - базовый контроллер
 *
 * @author Alexander
 */
class BaseController extends Controller
{

    /**
     * @Route("/", name="admin_dashboard")
     * @Method("GET")
     */
    public function dashboardAction(Request $request)
    {
        return $this->render('admin/dashboard.html.twig');
    }

}

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

    /**
     * @Route("/logout", name="admin_logout")
     */
    public function logoutAction(Request $request)
    {
        var_dump(1);
        exit;
        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @Route("/login", name="admin_login")
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/login.html.twig', [
                    'last_username' => $lastUsername,
                    'error' => $error,
        ]);
    }

    /**
     * @Route("/settings", name="admin_settings")
     */
    public function settingsAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\Setting');

        $list = [];

        /* @var $setting \UserBundle\Entity\Setting   */
        foreach ($repo->findAll() as $setting) {
            $list[$setting->name] = $setting;
        }

        if ($request->isMethod('post')) {

            foreach ($request->get('settings') as $name => $value) {

                if (!isset($list[$name])) {

                    $newSetting = new \UserBundle\Entity\Setting();
                    $newSetting->name = $name;

                    $em->persist($newSetting);

                    $list[$name] = $newSetting;
                }

                $list[$name]->value = $value;
            }

            $em->flush($list);
        }

        return $this->render('admin/settings.html.twig', ['settings' => $list]);
    }

}

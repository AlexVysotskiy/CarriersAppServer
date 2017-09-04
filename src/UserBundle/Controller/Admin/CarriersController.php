<?php

namespace UserBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Админка для водителей
 *
 * @author Alexander
 */
class CarriersController extends Controller
{

    /**
     * @Route("/carriers_list", name="admin_carriers_list")
     * @Method("GET")
     */
    public function carriersListAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\User');

        /* @var $cityRepo \Doctrine\ORM\EntityRepository */
        $cityRepo = $em->getRepository('UserBundle\Entity\City');
        $cityList = $cityRepo->findBy(array(
            'active' => true
                ), array('id' => 'DESC', 'order' => 'ASC'));

        $conditionId = intval($request->get('id'));
        $conditionName = $request->get('name');
        $conditionPhone = $request->get('phone');

        if ($request->get('cityId') === null) {
            $conditionCity = $cityList[0]->getId();
        } else {
            $conditionCity = intval($request->get('cityId'));
        }

        $query = "select u from UserBundle\Entity\User u";
        $query .= ' where u.checked = 1 and';

        if ($conditionId || $conditionName || $conditionCity || $conditionPhone) {

            if ($conditionId) {
                $query .= ' u.id = ' . $conditionId . ' and';
            }

            if ($conditionName) {
                $query .= ' u.username LIKE \'' . $conditionName . '%\'  and';
            }

            if ($conditionPhone) {
                $query .= ' u.phone LIKE \'' . $conditionPhone . '%\'  and';
            }

            if ($conditionCity) {
                $query .= ' u.city = ' . $conditionCity;
            }
        }

        $query = trim($query, 'and');
        $query .= " ORDER BY u.id DESC";

        $list = $em->createQuery($query)->getResult();

        return $this->render('admin/carriers/list.html.twig', array(
                    'list' => $list,
                    'citiesList' => $cityList,
                    'filter' => array(
                        'id' => $conditionId,
                        'name' => $conditionName,
                        'phone' => $conditionPhone,
                        'city' => $conditionCity
                    )
        ));
    }

    /**
     * @Route("/carriers_check", name="admin_carriers_check")
     * @Method("GET")
     */
    public function carriersCheckAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\User');

        /* @var $cityRepo \Doctrine\ORM\EntityRepository */
        $cityRepo = $em->getRepository('UserBundle\Entity\City');
        $cityList = $cityRepo->findBy(array(
            'active' => true
                ), array('id' => 'DESC', 'order' => 'ASC'));

        if (!$request->get('cityId') === null) {
            $conditionCity = intval($request->get('cityId'));
        } else {
            $conditionCity = null;
        }

        $conditionCheckStatus = intval($request->get('checked'));

        $query = "select u from UserBundle\Entity\User u";

        $query .= ' where';
        $query .= ' u.checked = ' . $conditionCheckStatus . ' and';

        if ($conditionCity) {
            $query .= ' u.city = ' . $conditionCity;
        }

        $query = trim($query, 'and');
        $query .= " ORDER BY u.id DESC";

        $list = $em->createQuery($query)->getResult();
        return $this->render('admin/carriers/list_check.html.twig', array(
                    'list' => $list,
                    'citiesList' => $cityList,
                    'filter' => array(
                        'checked' => $conditionCheckStatus,
                        'city' => $conditionCity
                    )
        ));
    }

    /**
     * добавление / редактирование региона
     * @Route("/carriers_list/payments_list", name="admin_carriers_payments_list")
     */
    public function paymentsListAjaxAction(Request $request)
    {
        $userId = $request->get('userId');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\User');

        /* @var $user \UserBundle\Entity\User */
        if ($user = $repo->find($userId)) {

            /* @var $repo \Doctrine\ORM\EntityRepository */
            $repo = $em->getRepository('UserBundle\Entity\Order');

            $list = $repo->findBy(['user' => $user->getId()]);

            return $this->render('admin/carriers/payments_list.html.twig', array(
                        'list' => $list
            ));
        }

        return new JsonResponse(array(
            'success' => 1
        ));
    }

    /**
     * добавление / редактирование региона
     * @Route("/carriers_list/toggle_lock", name="admin_carriers_ajax_togglelock")
     */
    public function toggleLockAjaxAction(Request $request)
    {
        $userId = $request->get('userId');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\User');

        /* @var $user \UserBundle\Entity\User */
        if ($user = $repo->find($userId)) {

            $user->setEnabled(!$user->isEnabled());
            $em->flush();
        }

        return new JsonResponse(array(
            'success' => 1
        ));
    }

    /**
     * добавление / редактирование региона
     * @Route("/carriers_list/check", name="admin_carriers_ajax_check")
     */
    public function toggleCheckAjaxAction(Request $request)
    {
        $userId = $request->get('userId');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\User');

        /* @var $user \UserBundle\Entity\User */
        if ($user = $repo->find($userId)) {

            $user->checked = $request->get('allow') == 1 ? 1 : 2;
            $em->flush();
        }

        return new JsonResponse(array(
            'success' => 1
        ));
    }

    /**
     * добавление / редактирование региона
     * @Route("/carriers_list/activate", name="admin_carriers_ajax_activate")
     */
    public function activeAjaxAction(Request $request)
    {
        $userId = $request->get('userId');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\User');

        /* @var $user \UserBundle\Entity\User */
        if ($user = $repo->find($userId)) {

            if ($user->getExpireDate()->getTimestamp() < date('U')) {
                $user->setExpireDate(new \DateTime());
            }

            // update extire date
            $user->getExpireDate()->modify('+1 month');

            $em->flush();
        }

        return new JsonResponse(array(
            'success' => 1
        ));
    }

    /**
     * добавление / редактирование региона
     * @Route("/carriers_list/deactivate", name="admin_carriers_ajax_deactivate")
     */
    public function deactiveAjaxAction(Request $request)
    {
        $userId = $request->get('userId');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\User');

        /* @var $user \UserBundle\Entity\User */
        if ($user = $repo->find($userId)) {

            $user->setExpireDate(new \DateTime());
            $em->flush();
        }

        return new JsonResponse(array(
            'success' => 1
        ));
    }

    /**
     * добавление / редактирование региона
     * @Route("/carriers_list/total_remove", name="admin_carriers_ajax_remove")
     */
    public function totalRemoveAjaxAction(Request $request)
    {
        $userId = $request->get('userId');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\User');

        /* @var $user \UserBundle\Entity\User */
        if ($user = $repo->find($userId)) {

            $em->remove($user);
            $em->flush();
        }

        return new JsonResponse(array(
            'success' => 1
        ));
    }

}

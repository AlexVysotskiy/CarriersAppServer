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
 * Админка для платежей
 *
 * @author Alexander
 */
class PaymentsController extends Controller
{

    /**
     * @Route("/payments_list", name="admin_payments_list")
     * @Method("GET")
     */
    public function paymentsListAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\Order');

        /* @var $regionsRepo \Doctrine\ORM\EntityRepository */
        $cityRepo = $em->getRepository('UserBundle\Entity\City');
        $cityList = $cityRepo->findBy(array(
            'active' => true
                ), array('id' => 'DESC', 'order' => 'ASC'));

        $conditions = array(
            'success' => true
        );

        if ($filters = $request->get('filter')) {

            foreach ($filters as $key => $value) {
                if (($value = trim($value)) && $value != 'all') {
                    $conditions[$key] = $value;
                }
            }
        }

        $list = $repo->findBy($conditions, array('id' => 'DESC'));

        return $this->render('admin/payments/list.html.twig', array(
                    'list' => $list,
                    'cities' => $cityList,
                    'filters' => $filters,
                    'request' => $request
        ));
    }

    /**
     * @Route("/payments_settings/list", name="admin_payments_types_list")
     * @Method("GET")
     */
    public function paymentTypesListAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\PaymentType');

        /* @var $regionsRepo \Doctrine\ORM\EntityRepository */
        $cityRepo = $em->getRepository('UserBundle\Entity\City');

        $conditions = array();
        if ($filters = $request->get('filter')) {

            foreach ($filters as $key => $value) {

                if (($value = trim($value)) && $value != 'all') {

                    $conditions[$key] = $value;
                }
            }
        }

        $list = $repo->findBy($conditions, array('id' => 'DESC'));

        return $this->render('admin/payments/types_list.html.twig', array(
                    'list' => $list,
                    'cities' => $cityRepo->findAll(),
                    'cargoList' => $this->getParameter('cargo_types'),
                    'request' => $request
        ));
    }

    /**
     * удаление регионов
     * @Route("/payments_settings/remove_ajax", name="admin_payments_types_remove")
     * @Method("POST")
     */
    public function removePaymentTypeAjaxAction(Request $request)
    {
        if ($request->isMethod('POST')) {

            if (($list = $request->get('list')) && is_array($list)) {

                /* @var $em \Doctrine\ORM\EntityManager */
                $em = $this->get('doctrine.orm.entity_manager');
                /* @var $repo \Doctrine\ORM\EntityRepository */
                $repo = $em->getRepository('UserBundle\Entity\PaymentType');

                $list = $repo->findBy(array('id' => $list));

                foreach ($list as $entity) {
                    $em->remove($entity);
                }

                $em->flush($list);
            }
        }

        return new JsonResponse(array(
            'success' => 1
        ));
    }

    /**
     * @Route("/payments_settings/add_ajax", name="admin_payments_types_add")
     */
    public function addPaymentTypeAjax(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\PaymentType');

        /* @var $cityRepo \Doctrine\ORM\EntityRepository */
        $cityRepo = $em->getRepository('UserBundle\Entity\City');

        if ($id = $request->get('id')) {

            /* @var $paymentType \UserBundle\Entity\PaymentType */
            $paymentType = $repo->find($id);
        }

        if ($request->isMethod('POST')) {

            $isNew = !isset($paymentType) || !$paymentType;

            if ($isNew) {
                $paymentType = new \UserBundle\Entity\PaymentType();
            }

            $errors = array();

            $term = abs(intval($request->get('term')));
            $category = $request->get('category');
            $city = abs(intval($request->get('city')));
            $value = abs(floatval($request->get('value')));

            if (!$value) {
                $errors[] = 'Введена неверная стоимость!';
            } /* elseif (!($city = $cityRepo->find($city))) {
              $errors[] = 'Выбран несуществующий город!';
              } */

            if (!$errors) {

                try {

                    $paymentType->term = $term;
                    $paymentType->category = $category;
                    $paymentType->city = ($city = $cityRepo->find($city)) ? $city : null;
                    $paymentType->value = $value;

                    if ($isNew) {
                        $em->persist($paymentType);
                    }

                    $em->flush($paymentType);
                } catch (\Exception $e) {

                    $errors[] = 'Возникли ошибки во время добавления региона!';
                }
            }

            return new JsonResponse(array(
                'success' => $errors ? 0 : 1,
                'errors' => $errors,
                'reload' => $isNew
            ));
        } else {

            return $this->render('admin/payments/payments_types_form.html.twig', array(
                        'paymentType' => isset($paymentType) && $paymentType ? $paymentType : null,
                        'cities' => $cityRepo->findAll(),
                        'cargoList' => $this->getParameter('cargo_types'),
            ));
        }
    }

}

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
class PaymentsController extends Controller {

    /**
     *
     * @var type 
     */
    private $durations = [1, 2, 3, 6, 12];

    /**
     * @Route("/payments_list", name="admin_payments_list")
     * @Method("GET")
     */
    public function paymentsListAction(Request $request) {
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
    public function paymentTypesListAction(Request $request) {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\PaymentType');

        /* @var $paymentPackageRepo \Doctrine\ORM\EntityRepository */
        $paymentPackageRepo = $em->getRepository('UserBundle\Entity\PaymentPackage');

        $list = [];
        /* @var $payment \UserBundle\Entity\PaymentType */
        foreach ($repo->findAll() as $payment) {

            $al = $payment->category->aliase;
            if (!isset($list[$al])) {
                $list[$al] = [];
            }

            $list[$al][$payment->term . '_' . $payment->package->id] = $payment;
        }


        return $this->render('admin/payments/types_list.html.twig', array(
                    'list' => $list,
                    'durations' => $this->durations,
                    'categories' => $paymentPackageRepo->findAll(),
                    'cargoList' => $this->getParameter('cargo_types'),
                    'car_types' => $em->getRepository('UserBundle\Entity\CarType')->findAll(),
                    'request' => $request
        ));
    }

    /**
     * удаление регионов
     * @Route("/payments_settings/remove_ajax", name="admin_payments_types_remove")
     * @Method("POST")
     */
    public function removePaymentTypeAjaxAction(Request $request) {
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
    public function addPaymentTypeAjax(Request $request) {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\PaymentType');

        /* @var $paymentPackageRepo \Doctrine\ORM\EntityRepository */
        $paymentPackageRepo = $em->getRepository('UserBundle\Entity\PaymentPackage');

        if ($id = $request->get('id')) {

            /* @var $paymentType \UserBundle\Entity\PaymentType */
            $paymentType = $repo->find($id);
        }

        $isNew = !isset($paymentType) || !$paymentType;

        $term = abs(intval($request->get('term')));
        $cartype = abs(intval($request->get('category')));
        $package = abs(intval($request->get('package')));

        if ($isNew) {

            $paymentType = new \UserBundle\Entity\PaymentType();

            $paymentType->term = $term;
            $paymentType->category = $em->getRepository('UserBundle\Entity\CarType')->find($cartype);
            $paymentType->package = $paymentPackageRepo->find($package);
        }

        if ($request->isMethod('POST')) {

            $errors = array();

            $value = abs(floatval($request->get('value')));

            if (!$value) {
                $errors[] = 'Введена неверная стоимость!';
            }

            if (!$errors) {

                try {

                    $paymentType->value = $value;

                    if ($isNew) {
                        $em->persist($paymentType);
                    }

                    $em->flush();
                } catch (\Exception $e) {

                    $errors[] = 'Возникли ошибки во время добавления региона!';
                }
            }

            return new JsonResponse(array(
                'success' => $errors ? 0 : 1,
                'errors' => $errors,
                'reload' => $isNew,
                'value' => $value
            ));
        } else {

            return $this->render('admin/payments/payments_types_form.html.twig', array(
                        'paymentType' => isset($paymentType) && $paymentType ? $paymentType : null,
                        'categories' => $paymentPackageRepo->findAll(),
                        'durations' => $this->durations
            ));
        }
    }

    /**
     * @Route("/payments_settings/add_package_ajax", name="admin_payments_category_add")
     */
    public function addPaymentPackageAjax(Request $request) {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $paymentPackageRepo \Doctrine\ORM\EntityRepository */
        $paymentPackageRepo = $em->getRepository('UserBundle\Entity\PaymentPackage');

        if ($id = $request->get('id')) {

            /* @var  $paymentPackage \UserBundle\Entity\PaymentPackage */
            $paymentPackage = $paymentPackageRepo->find($id);
        }

        if ($request->isMethod('POST')) {

            $isNew = !isset($paymentPackage) || !$paymentPackage;

            if ($isNew) {
                $paymentPackage = new \UserBundle\Entity\PaymentPackage();
            }

            $errors = array();
            $name = $request->get('name');

            /* @var \UserBundle\Entity\PaymentPackage $existed */
            if (($existed = $paymentPackageRepo->findOneBy(['name' => $name])) && ($isNew || $paymentPackage->id != $existed->id)) {
                $errors[] = 'Категория с таким именем уже существует!';
            }

            if (!$errors) {

                try {
                    $paymentPackage->name = $name;
                    if ($isNew) {
                        $em->persist($paymentPackage);
                    }
                    $em->flush($paymentPackage);
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

            return $this->render('admin/payments/payments_package_form.html.twig', array(
                        'package' => isset($paymentPackage) && $paymentPackage ? $paymentPackage : null
            ));
        }
    }

    /**
     * удаление регионов
     * @Route("/payments_settings/remove_package_ajax", name="admin_payments_package_remove")
     * @Method("POST")
     */
    public function removePaymentPackageAjaxAction(Request $request) {
        if ($request->isMethod('POST')) {

            if ($id = $request->get('id')) {

                /* @var $em \Doctrine\ORM\EntityManager */
                $em = $this->get('doctrine.orm.entity_manager');

                /* @var $repo \Doctrine\ORM\EntityRepository */
                $repo = $em->getRepository('UserBundle\Entity\PaymentPackage');

                $entity = $repo->findOneBy(array('id' => $id));
                $em->remove($entity);

                $list = $em->getRepository('UserBundle\Entity\City')->findBy([
                    'paymentPackage' => $id
                ]);
                /* @var $entity \UserBundle\Entity\City  */
                foreach ($list as $entity) {
                    $entity->paymentPackage = null;
                }

                $list = $em->getRepository('UserBundle\Entity\PaymentType')->findBy([
                    'package' => $id
                ]);
                /* @var $entity \UserBundle\Entity\PaymentType  */
                foreach ($list as $entity) {
                    $entity->package = null;
                }

                $em->flush();
            }
        }

        return new JsonResponse(array(
            'success' => 1
        ));
    }

}

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
class CarTypeController extends Controller {

    /**
     * @Route("/car_settings/list", name="admin_car_type_list")
     * @Method("GET")
     */
    public function typesListAction(Request $request) {

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        return $this->render('admin/carTypes/list.html.twig', array(
                    'categories' => $em->getRepository('UserBundle\Entity\CarCategory')->findAll(),
                    'types' => $em->getRepository('UserBundle\Entity\CarType')->findAll(),
        ));
    }

    /**
     * Добавили вид ТС в категорию
     * @Route("/car_settings/add_car_type_ajax", name="admin_car_type_add")
     */
    public function addPaymentTypeAjax(Request $request) {

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $carTypeRepo = $em->getRepository('UserBundle\Entity\CarType');

        /* @var $paymentPackageRepo \Doctrine\ORM\EntityRepository */
        $carCategoryRepo = $em->getRepository('UserBundle\Entity\CarCategory');

        if ($id = $request->get('id')) {

            /* @var $carType \UserBundle\Entity\CarType */
            $carType = $carTypeRepo->find($id);
        }

        if ($request->isMethod('POST')) {

            $isNew = !isset($carType) || !$carType;

            if ($isNew) {
                $carType = new \UserBundle\Entity\CarType();
            }

            $errors = array();

            $name = trim($request->get('name'));
            $aliase = trim($request->get('aliase'));
            $category = abs(intval($request->get('category')));

            if (!$name) {
                $errors[] = 'Введите имя!';
            }

            if (!$aliase) {
                $errors[] = 'Введите псевдоним на латинице!';
            }

            if (($ex = $carTypeRepo->findOneBy(['name' => $name])) || ($ex = $carTypeRepo->findOneBy(['aliase' => $aliase]))) {

                if ($isNew || $ex->id != $carType->id) {
                    $errors[] = 'Такое имя или псевдоним уже существуют!';
                }
            }

            if (!$errors) {

                try {

                    $carType->name = $name;
                    $carType->aliase = $aliase;
                    $carType->category = $carCategoryRepo->find($category);

                    if ($isNew) {
                        $em->persist($carType);
                    }

                    $em->flush();
                } catch (\Exception $e) {

                    $errors[] = 'Возникли ошибки!';
                }
            }

            return new JsonResponse(array(
                'success' => $errors ? 0 : 1,
                'errors' => $errors,
                'reload' => $isNew
            ));
        } else {

            return $this->render('admin/carTypes/car_type_form.html.twig', array(
                        'entity' => isset($carType) && $carType ? $carType : null,
                        'categories' => $carCategoryRepo->findAll(),
            ));
        }
    }

    /**
     * Удалили виды ТС в категории
     * 
     * @Route("/car_settings/remove_car_type_ajax", name="admin_car_type_remove")
     * @Method("POST")
     */
    public function removeCarTypeAjaxAction(Request $request) {

        if ($request->isMethod('POST')) {

            if (($list = $request->get('list')) && is_array($list)) {

                /* @var $em \Doctrine\ORM\EntityManager */
                $em = $this->get('doctrine.orm.entity_manager');

                /* @var $repo \Doctrine\ORM\EntityRepository */
                $repo = $em->getRepository('UserBundle\Entity\CarType');
                $paymentTypeRepo = $em->getRepository('UserBundle\Entity\PaymentType');

                $list = $repo->findBy(array('id' => $list));

                foreach ($list as $entity) {

                    $em->remove($entity);

                    if ($paymentPacs = $paymentTypeRepo->findBy(['category' => $entity->getId()])) {

                        foreach ($paymentPacs as $entity) {
                            $em->remove($entity);
                        }
                    }
                }

                $em->flush();
            }
        }

        return new JsonResponse(array(
            'success' => 1
        ));
    }

    /**
     * @Route("/car_settings/add_car_type_category_ajax", name="admin_car_type_add_category")
     */
    public function addCarCategoryAjax(Request $request) {

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $paymentPackageRepo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\CarCategory');

        if ($id = $request->get('id')) {

            /* @var  $entity \UserBundle\Entity\CarCategory */
            $entity = $repo->find($id);
        }

        if ($request->isMethod('POST')) {

            $isNew = !isset($entity) || !$entity;

            if ($isNew) {
                $entity = new \UserBundle\Entity\CarCategory();
            }

            $errors = array();
            $name = $request->get('name');

            /* @var \UserBundle\Entity\PaymentPackage $existed */
            if (($existed = $repo->findOneBy(['name' => $name])) && ($isNew || $entity->id != $existed->id)) {
                $errors[] = 'Категория с таким именем уже существует!';
            }

            if (!$errors) {

                try {

                    $entity->name = $name;

                    if ($isNew) {
                        $em->persist($entity);
                    }

                    $em->flush($entity);
                } catch (\Exception $e) {

                    $errors[] = 'Возникли ошибки во время добавления категории!';
                }
            }

            return new JsonResponse(array(
                'success' => $errors ? 0 : 1,
                'errors' => $errors,
                'reload' => $isNew
            ));
        } else {

            return $this->render('admin/carTypes/car_category_form.html.twig', array(
                        'category' => isset($entity) && $entity ? $entity : null
            ));
        }
    }

    /**
     * @Route("/car_settings/remove_car_type_category_ajax", name="admin_car_type_remove_category")
     * @Method("POST")
     */
    public function removePaymentPackageAjaxAction(Request $request) {
        
        if ($request->isMethod('POST')) {

            if ($id = $request->get('id')) {

                /* @var $em \Doctrine\ORM\EntityManager */
                $em = $this->get('doctrine.orm.entity_manager');

                /* @var $repo \Doctrine\ORM\EntityRepository */
                $repo = $em->getRepository('UserBundle\Entity\CarCategory');

                $entity = $repo->findOneBy(array('id' => $id));
                $em->remove($entity);

                $list = $em->getRepository('UserBundle\Entity\CarType')->findBy([
                    'category' => $id
                ]);
                /* @var $entity \UserBundle\Entity\City  */
                foreach ($list as $entity) {
                    $entity->category = null;
                }

                $em->flush();
            }
        }

        return new JsonResponse(array(
            'success' => 1
        ));
    }

}

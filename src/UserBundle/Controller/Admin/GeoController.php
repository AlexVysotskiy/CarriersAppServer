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
 * Админка для городов / регионов
 *
 * @author Alexander
 */
class GeoController extends Controller
{

    /**
     * @Route("/regions_list", name="admin_regions_list")
     * @Method("GET")
     */
    public function regionsListAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\Region');

        $list = $repo->findBy(array(), array('id' => 'DESC'));

        return $this->render('admin/regions/regions.html.twig', array(
                    'list' => $list
        ));
    }

    /**
     * добавление / редактирование региона
     * @Route("/region_add_ajax", name="admin_region_add")
     */
    public function addRegionAjax(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\Region');

        if ($id = $request->get('id')) {

            /* @var $region \UserBundle\Entity\Region */
            $region = $repo->find($id);
        }

        if ($request->isMethod('POST')) {

            $isNew = !isset($region) || !$region;

            if ($isNew) {
                $region = new \UserBundle\Entity\Region();
            }

            $errors = array();

            $name = $request->get('name');
            $order = abs(intval($request->get('order')));

            $validation = new \UserBundle\Validation\User\NameValidation();
            if (!$validation->validateBool($name)) {

                $errors[] = 'Введено неверное название!';
            } elseif ($isNew || $region->getName() != $name) {

                if ($repo->findOneBy(array('name' => $name))) {
                    $errors[] = 'Регион с таким названием уже существует!';
                }
            }

            if (!$errors) {

                try {

                    $region->setName($name);
                    $region->setOrder($order);
                    $region->setActive($request->get('active') == 1);

                    if ($isNew) {
                        $em->persist($region);
                    }

                    $em->flush($region);
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

            return $this->render('admin/regions/region_form.html.twig', array(
                        'region' => isset($region) && $region ? $region : null
            ));
        }
    }

    /**
     * удаление регионов
     * @Route("/region_remove_ajax", name="admin_region_remove")
     * @Method("POST")
     */
    public function removeRegionsAjaxAction(Request $request)
    {
        if ($request->isMethod('POST')) {

            if (($list = $request->get('list')) && is_array($list)) {

                /* @var $em \Doctrine\ORM\EntityManager */
                $em = $this->get('doctrine.orm.entity_manager');
                /* @var $repo \Doctrine\ORM\EntityRepository */
                $repo = $em->getRepository('UserBundle\Entity\Region');
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
     * @Route("/cities_list", name="admin_cities_list")
     * @Method("GET")
     */
    public function citiesListAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\City');

        /* @var $regionsRepo \Doctrine\ORM\EntityRepository */
        $regionsRepo = $em->getRepository('UserBundle\Entity\Region');

        $conditions = array();
        if ($filters = $request->get('filter')) {

            foreach ($filters as $key => $value) {

                if (($value = trim($value)) && $value != 'all') {
                    
                    $conditions[$key] = $value;
                }
            }
        }


        $list = $repo->findBy($conditions, array('id' => 'DESC'));

        return $this->render('admin/cities/cities.html.twig', array(
                    'list' => $list,
                    'regions' => $regionsRepo->findAll(),
                    'request' => $request
        ));
    }

    /**
     * удаление регионов
     * @Route("/city_remove_ajax", name="admin_city_remove")
     * @Method("POST")
     */
    public function removeCitiesAjaxAction(Request $request)
    {
        if ($request->isMethod('POST')) {

            if (($list = $request->get('list')) && is_array($list)) {

                /* @var $em \Doctrine\ORM\EntityManager */
                $em = $this->get('doctrine.orm.entity_manager');
                /* @var $repo \Doctrine\ORM\EntityRepository */
                $repo = $em->getRepository('UserBundle\Entity\City');

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
     * добавление / редактирование города
     * @Route("/city_add_ajax", name="admin_city_add")
     */
    public function addCityAjax(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\City');

        /* @var $regionsRepo \Doctrine\ORM\EntityRepository */
        $regionsRepo = $em->getRepository('UserBundle\Entity\Region');

        if ($id = $request->get('id')) {

            /* @var $city \UserBundle\Entity\City */
            $city = $repo->find($id);
        }

        if ($request->isMethod('POST')) {

            $isNew = !isset($city) || !$city;

            if ($isNew) {
                $city = new \UserBundle\Entity\City();
            }

            $errors = array();

            $name = $request->get('name');
            $regionId = $request->get('region');
            $order = abs(intval($request->get('order')));

            $validation = new \UserBundle\Validation\User\NameValidation();
            if (!$validation->validateBool($name)) {

                $errors[] = 'Введено неверное название!';
            } elseif (!($region = $regionsRepo->find($regionId))) {

                $errors[] = 'Выбран несуществующий регион!';
            } elseif ($isNew || $city->getName() != $name) {

                if ($repo->findOneBy(array('name' => $name, 'region' => $regionId))) {

                    $errors[] = 'Город с таким названием в данном регионе уже существует!';
                }
            }

            if (!$errors) {

                try {

                    $city->setName($name);
                    $city->setOrder($order);
                    $city->setActive($request->get('active') == 1);
                    $city->setRegion($region);

                    if ($isNew) {
                        $em->persist($city);
                    }

                    $em->flush($city);
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

            return $this->render('admin/cities/city_form.html.twig', array(
                        'city' => isset($city) && $city ? $city : null,
                        'regions' => $regionsRepo->findAll()
            ));
        }
    }

}

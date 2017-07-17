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
        $conditionCity = intval($request->get('cityId'));

        $query = "select u from UserBundle\Entity\User u";

        if ($conditionId || $conditionName || $conditionCity || $conditionPhone) {

           $query .= ' where';
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

            $query = trim($query, 'and');
        }

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

}

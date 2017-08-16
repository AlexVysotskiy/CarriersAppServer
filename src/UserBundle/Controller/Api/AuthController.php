<?php

namespace UserBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use UserBundle\Entity\User;

/**
 * @Security("is_granted('ROLE_USER')")
 */
class AuthController extends Controller
{

    use \UserBundle\Helper\ControllerHelper;

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

    /**
     * @Route("/edit", name="api_v1_user_edit")
     * @Method("POST")
     */
    public function userEditAction(Request $request)
    {
        try {

            $user = $this->getUser();
            $this->get('my_user_manager')->editUser($user, $request);

            $response = new Response($this->serialize(array(
                'success' => 1,
                'user' => $user,
                'token' => $this->getToken($user)
            )), Response::HTTP_CREATED);

            return $this->setBaseHeaders($response);
        } catch (\Exception $e) {

            return $this->raiseError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @Route("/remove", name="api_v1_user_remove")
     * @Method("POST")
     */
    public function userRemoveAction(Request $request)
    {
        try {

            /* @var $user User */
            $user = $this->getUser();
            $user->removed = true;

            $this->get('my_user_manager')->updateUser($user);

            $this->getToken($user);

        } catch (\Exception $e) {

            return $this->raiseError($e->getCode(), $e->getMessage());
        }

        $response = new Response($this->serialize(array(
            'success' => 1
        )), Response::HTTP_CREATED);

        return $this->setBaseHeaders($response);
    }
}

<?php

namespace UserBundle\Controller\Api;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Form\FormInterface;
use UserBundle\Entity\User;
use UserBundle\Validation;

class RegistrationController extends BaseController
{

    use \UserBundle\Helper\ControllerHelper;

    /**
     * @Route("/register", name="api_v1_user_register")
     * @Method("POST")
     */
    public function registerAction(Request $request)
    {
        try {

            $user = $this->get('my_user_manager')->registerUser($request);

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

}

<?php

namespace UserBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use UserBundle\Entity\User;
use UserBundle\Service\MyUserManager;

class LoginController extends Controller
{

    use \UserBundle\Helper\ControllerHelper;

    /**
     * @Route("/login/{phone}/{password}", name="api_v1_user_login")
     * @Method("POST")
     */
    public function loginAction(Request $request)
    {
        $phone = $request->get('phone');
        $password = $request->get('password');

        /* @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserBy(array(
            'phone' => '$phone',
        ));

        if (!$user || $this->get('security.password_encoder')
                        ->isPasswordValid($user, $password)) {
            return $this->raiseError(1, 'Введен неверный телефон/пароль.');
        }

        $token = $this->getToken($user);
        $response = new Response($this->serialize(['token' => $token]), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }

    /**
     * Returns token for user.
     *
     * @param User $user
     *
     * @return array
     */
    public function getToken(User $user)
    {
        return $this->container->get('lexik_jwt_authentication.encoder')
                        ->encode([
                            'username' => $user->getUsername(),
                            'exp' => $this->getTokenExpiryDateTime(),
        ]);
    }

    /**
     * Returns token expiration datetime.
     *
     * @return string Unixtmestamp
     */
    private function getTokenExpiryDateTime()
    {
        $tokenTtl = $this->container->getParameter('lexik_jwt_authentication.token_ttl');
        $now = new \DateTime();
        $now->add(new \DateInterval('PT' . $tokenTtl . 'S'));

        return $now->format('U');
    }

}

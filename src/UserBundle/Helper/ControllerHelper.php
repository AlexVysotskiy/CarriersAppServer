<?php

namespace UserBundle\Helper;

use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use UserBundle\Model\ApiError;
use UserBundle\Entity\User;
use UserBundle\Service\MyUserManager;

trait ControllerHelper
{

    /**
     * Set base HTTP headers.
     *
     * @param Response $response
     *
     * @return Response
     */
    private function setBaseHeaders(Response $response)
    {
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    public function raiseError($code, $message, $trace = array())
    {
        $apiError = new ApiError($code, $message);

        return $this->setBaseHeaders(new Response($this->serialize(array('error' => 1,
                            'error_info' => $apiError->toArray()))));
    }

    /**
     * Data serializing via JMS serializer.
     *
     * @param mixed $data
     *
     * @return string JSON string
     */
    public function serialize($data)
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        
        /* @var $qwe \JMS\Serializer\Serializer */

        return $this->get('jms_serializer')
                        ->serialize($data, 'json', $context);
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

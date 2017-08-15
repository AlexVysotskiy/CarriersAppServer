<?php

namespace UserBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CommonActionsController extends Controller
{

    use \UserBundle\Helper\ControllerHelper;

    /**
     * @Route("/contact_phone", name="api_v1_contact_phone")
     * @Method("GET")
     */
    public function contactPhoneAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\Setting');

        $response = new Response($this->serialize(array(
            'phone' => $repo->findOneBy(['name' => 'phone'])->value
        )), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }


    /**
     * @Route("/order_call", name="api_v1_contact_order_call")
     * @Method("GET")
     */
    public function orderCallBackAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\Setting');
        $email = $repo->findOneBy(['name' => 'email']);

        /* @var \UserBundle\Entity\User $user */
        $user = $em->getRepository('UserBundle\Entity\User')->find($request->get('id'));

        $message = (new \Swift_Message('Запрос на обратный звонок от перевозчика #' . $user->getId() . '.'))
            ->setFrom($email->name)
            ->setTo($email->name)
            ->setBody(
                'Поступил запрос на обратный звонок от перевозчика #' .
                $user->getId() . ' ' . $user->getUsername() . ' '
                . ' из ' . ' г. '
                . $user->getCity()->getName() . ', тел. ' . $user->getPhone(),
                'text/plain'
            );

        $this->get('mailer')->send($message);

        $response = new Response($this->serialize(array(
            'success' => 1
        )), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }


    /**
     * @Route("/send_email", name="api_v1_contact_send_email")
     */
    public function sendMailAction(Request $request)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\Setting');
        $email = $repo->findOneBy(['name' => 'email']);

        /* @var \UserBundle\Entity\User $user */
        $user = $em->getRepository('UserBundle\Entity\User')->find($request->get('id'));

        if ($text = trim($request->get('text'))) {

            $message = (new \Swift_Message('Запрос/отзыв на обратный звонок от перевозчика #' . $user->getId() . '.'))
                ->setFrom($email->value)
                ->setTo($email->value)
                ->setBody(
                    'Поступил запрос/отзыв от перевозчика #' .
                    $user->getId() . ' ' . $user->getUsername() . ' '
                    . ' из ' . ' г. '
                    . $user->getCity()->getName() . ', тел. ' . $user->getPhone()
                    . '.' . PHP_EOL . PHP_EOL
                    . PHP_EOL . 'Текст:' . PHP_EOL . $text . '.',
                    'text/plain'
                );

            $this->get('mailer')->send($message);
        }

        $response = new Response($this->serialize(array(
            'success' => 1
        )), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }

}

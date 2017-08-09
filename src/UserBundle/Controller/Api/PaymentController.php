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

/**
 * Description of PaymentController
 *
 * @author Alexander
 */
class PaymentController extends BaseController
{

    use \UserBundle\Helper\ControllerHelper;

    /**
     * @Route("/payment_pack/{cityId}/{cargoType}", name="api_v1_payment_packs")
     * @Method("GET")
     */
    public function availablePackagesAction(Request $request)
    {
        $cityId = abs(intval($request->get('cityId')));
        $cargoType = $request->get('cargoType');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');

        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('UserBundle\Entity\PaymentType');

        $list = $repo->findBy(array(
            'city' => $cityId,
            'category' => $cargoType
        ));

        $res = [];
        /* @var $paymentType \UserBundle\Entity\PaymentType */
        foreach ($list as $paymentType) {
            $res[$paymentType->term] = $paymentType;
        }

        $qb = $em->createQueryBuilder();
        $query = $qb->select('p')
                ->from('UserBundle\Entity\PaymentType', "p")
                ->where('p.city IS NULL and p.category = \'' . $cargoType . '\'')
                ->getQuery();

        $extra = [];
        /* @var $paymentType \UserBundle\Entity\PaymentType */
        foreach ($query->getResult() as $paymentType) {
            $extra[$paymentType->term] = $paymentType;
        }

        $res += $extra;
        
        ksort($res);

        $response = new Response($this->serialize(
                        array('list' => array_values($res))
                ), Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }

    /**
     * @Route("/payment", name="api_v1_payment_make")
     * @Method("GET")
     */
    public function payAction(Request $request)
    {
        try {

            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->get('doctrine.orm.entity_manager');

            /* @var $tokenAuthService \UserBundle\Security\TokenAuthenticator */
            $tokenAuthService = $this->get('token_authenticator');
            $credentials = $tokenAuthService->getCredentials($request);

            /* @var $user User */
            $user = $tokenAuthService->getUser($credentials, null);
//            $userId = $request->get('id');
//            $user = $em->getRepository('UserBundle\Entity\User')
//                    ->findOneBy(['id' => $userId, 'enabled' => true]);

            $paymentPack = $request->get('payment_pack');

            if ($user) {

                /* @var $paymentPackRepo \Doctrine\ORM\EntityRepository */
                $paymentPackRepo = $em->getRepository('UserBundle\Entity\PaymentType');

                /* @var $paymentPack \UserBundle\Entity\PaymentType */
                if (!($paymentPack = $paymentPackRepo->find($paymentPack))) {
                    throw new \Exception('Payment pack mismatch!');
                }

                $order = new \UserBundle\Entity\Order();

                $order->user = $user;
                $order->city = $user->getCity();
                $order->term = $paymentPack->term;
                $order->date = new \DateTime();
                $order->sum = $paymentPack->value;

                $em->persist($order);
                $em->flush($order);

                /* @var $payment \Idma\Robokassa\Payment */
                $payment = $this->initRobokassaPayment();

                $payment->setInvoiceId($order->id)
                        ->setSum($order->sum)
                        ->setDescription('Оплата за ' . $order->term . ' месяц(-а,-ев) использования сервиса пользователем #'
                                . $user->getId()
                                . ' ' . $user->getUsername()
                                . ' от ' . date('H:i d-m-Y'));

                // redirect to payment url
                //return $this->redirectToRoute('api_v1_payment_success');

                return $this->redirect($payment->getPaymentUrl());
            } else {
                throw new \Exception('User mismatch!');
            }
        } catch (\Exception $e) {

            return $this->redirectToRoute('api_v1_payment_error');
        }
    }

    /**
     * @Route("/payment/callback", name="api_v1_payment_callback")
     * @Method("GET")
     */
    public function paymentCallbackAction(Request $request)
    {
        /* @var $payment \Idma\Robokassa\Payment */
        $payment = $this->initRobokassaPayment();

        $content = 'FAILURE: Something went wrong during payment process.';
        if ($payment->validateResult($request->query->all())) {

            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->get('doctrine.orm.entity_manager');
            $repo = $em->getRepository('UserBundle\Entity\Order');

            /* @var $order \UserBundle\Entity\Order */
            if ($order = $repo->find($payment->getInvoiceId())) {

                if (!$order->success) {

                    $order->success = true;
                    $this->activateProfile($order->user, $order->term);

                    /* if ($order->user->getExpireDate()->getTimestamp() < date('U')) {
                      $order->user->setExpireDate(new \DateTime());
                      } */

                    // update extire date
                    //$order->user->getExpireDate()->modify('+1 month');

                    $em->flush();
                }

                // send answer
                $content = $payment->getSuccessAnswer();
            }
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);

        return $response;
    }

    /**
     * @Route("/payment/fail", name="api_v1_payment_error")
     * @Method("GET")
     */
    public function failAction(Request $request)
    {
        return $this->redirect('unity://fail');

        return new \Symfony\Component\HttpFoundation\JsonResponse(array('success' => 0));
    }

    /**
     * @Route("/payment/success", name="api_v1_payment_success")
     * @Method("GET")
     */
    public function successAction(Request $request)
    {
        if ($userId = $request->get('testUserId')) {

            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->get('doctrine.orm.entity_manager');

            /* @var $repo */
            $repo = $em->getRepository('UserBundle\Entity\User');

            if ($user = $repo->find($userId)) {

                $robokassaSettings = $this->getParameter('robokassa');
                if (isset($robokassaSettings['testMode']) && $robokassaSettings['testMode']) {
                    $this->activateProfile($user);
                }
            }
        }


        return $this->redirect('unity://success');
        return new \Symfony\Component\HttpFoundation\JsonResponse(array('success' => 1));
    }

    /**
     * @param $user User
     */
    protected function activateProfile($user, $term)
    {
        if ($user->getExpireDate()->getTimestamp() < date('U')) {
            $user->setExpireDate(new \DateTime());
        }

        // update extire date
        $user->getExpireDate()->modify('+' . $term . ' month');
    }

    /**
     * @return Idma\Robokassa\Payment
     */
    protected function initRobokassaPayment()
    {
        $robokassaSettings = $this->getParameter('robokassa');
        $payment = new \Idma\Robokassa\Payment(
                $robokassaSettings['merchant_id'], $robokassaSettings['paymentsPasswords']['pass1'], $robokassaSettings['paymentsPasswords']['pass2'], $robokassaSettings['testMode']
        );

        return $payment;
    }

}

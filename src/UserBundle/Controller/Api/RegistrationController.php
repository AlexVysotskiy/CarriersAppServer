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

class RegistrationController extends BaseController
{

    use \UserBundle\Helper\ControllerHelper;

    /**
     * @Route("/register", name="api_v1_user_register")
     * @Method("POST")
     */
    public function registerAction(Request $request)
    {
        $keys = array(
            'username',
//            'email',
            'password',
            'city_id',
            'cargo_type',
            'phone',
            'city_district',
            'description',
            'dimensions',
            'loaders',
            'work_area',
            'auto_type',
            'price',
            'min_hour',
            'work_time',
        );

        $params = array();

        foreach ($keys as $key) {
            $params[$key] = trim($request->get($key));
        }

// Прошла валидация и проверили все на уникальность
        if ($errors = $this->validate($params)) {
            return $this->raiseError($errors['code'], $errors['message']);
        }

        try {

            /* @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            /* @var $user \UserBundle\Entity\User */
            $user = $userManager->createUser();
            $user->setEnabled(true)
                    ->setUsername($params['username'])
                    ->setEmail($params['phone'])
                    ->setPhone($params['phone'])
                    ->setName($params['username'])
                    ->setPlainPassword($params['password'])
                    ->setCargoType($params['cargo_type'])
                    ->setCityDistrict($params['city_district'])
                    ->setDescription(substr($params['description'], 0, 255));

            if (isset($params['auto_type'])) {
                $user->setAutoType(substr($params['auto_type'], 0, 255));
            }

            if (isset($params['dimensions'])) {
                $dimensions = json_decode($params['dimensions'], true);
            }

            if (!$dimensions) {
                $dimensions = array();
            }

            $user->setDimensions($dimensions)
                    ->setLoaders($params['loaders'] == 1);

            if (isset($params['work_area'])) {

                $workArea = json_decode($params['work_area'], true);

                if (@$workArea['city']) {
                    $workArea = @$workArea['suburban'] ? User::WORK_AREA_ALL : User::WORK_AREA_CITY;
                } elseif (@$workArea['suburban']) {
                    $workArea = User::WORK_AREA_SUBURBAN;
                } else {
                    $workArea = User::WORK_AREA_ALL;
                }

                $user->setWorkArea($workArea);
            }

            $user->setPrice($params['price'])
                    ->setMinHour($params['min_hour']);

            if (isset($params['work_time']) && ($settings = json_decode($params['work_time'], true))) {

                $user->setWorkTimeSettings($settings);
            }

            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->get('doctrine.orm.entity_manager');
            $user->setCity($em->find('UserBundle\Entity\City', $params['city_id']));

            $userManager->updateUser($user);

            $response = new Response($this->serialize(array(
                        'success' => 1,
                        'user' => $user
                    )), Response::HTTP_CREATED);

            return $this->setBaseHeaders($response);
        } catch (\Exception $e) {

            return $this->raiseError($e->getCode(), $e->getMessage() . PHP_EOL . $e->getFile());
        }
    }

    protected function validate($values)
    {
        $error = array();

        /* @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        try {

            if (!$values['username'] || $values['username'] == '') {

                throw new \Exception('Имя является обязательным полем!', 1);
            } elseif (!preg_match('/^[a-zа-я\-_]+(\s[a-zа-я\-_]+)?$/iu', $values['username'])) {

                throw new \Exception('Введите корректное имя!', 1);
            } elseif (mb_strlen($values['username']) > 200) {
                throw new \Exception('Слишком имя!', 1);
            }

//            if (!$values['email'] || $values['email'] == '') {
//
//                throw new \Exception('E-mail является обязательным полем!', 1);
//            } elseif (!preg_match('/^[^\@]+@.*.[a-z]{2,20}$/i', $values['email'])) {
//
//                throw new \Exception('Введите корректный e-mail!', 1);
//            } elseif ($userManager->findUserByEmail($values['email'])) {
//
//                throw new \Exception('Пользователь с таким e-mail уже существует!', 1);
//            }

            if (!$values['password'] || $values['password'] == '' || mb_strlen($values['password']) < 4) {
                throw new \Exception('Пароль является обязательным полем / Слишком короткий пароль!', 1);
            }

            if (!$values['phone'] || $values['phone'] == '') {
                throw new \Exception('Пароль является обязательным полем!', 1);
            } elseif ($userManager->findUserBy(array('phone' => $values['phone']))) {

                throw new \Exception('Пользователь с таким телефоном уже существует!', 1);
            }

            if ($values['price'] <= 0) {
                throw new \Exception('Стоимость является обязательным полем!', 1);
            }

            if ($values['min_hour'] <= 0) {
                throw new \Exception('Количество часов является обязательным полем!', 1);
            }


            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->get('doctrine.orm.entity_manager');
            if (!$values['city_id'] || !$em->find('UserBundle\Entity\City', $values['city_id'])) {

                throw new \Exception('Выбран некорректный город!', 1);
            }

            if (!$values['cargo_type'] || !in_array($values['cargo_type'], $this->getParameter('cargo_types'))) {

                throw new \Exception('Выбран неверный тип перевозки!', 1);
            }
        } catch (\Exception $ex) {

            $error['code'] = $ex->getCode();
            $error['message'] = $ex->getMessage();
        }

        return $error;
    }

}

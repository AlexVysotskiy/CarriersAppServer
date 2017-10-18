<?php

namespace UserBundle\Service;

use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Validation;

class MyUserManager extends UserManager {

    /**
     *
     * @var UserBundle\Helper\UserImageHelper
     */
    protected $imageHelper;

    /**
     * @inheritdoc
     */
    public function findUserByUsernameOrEmail($usernameOrEmail) {
        $user = parent::findUserByUsernameOrEmail($usernameOrEmail);
        if (null === $user) {
            $userAddOnEmailRepo = $this->objectManager->getRepository('UserBundle:UserAddOnEmail');
            $userAddOnEmail = $userAddOnEmailRepo->findOneBy(['email' => $usernameOrEmail]);
            if ($userAddOnEmail) {
                $user = $userAddOnEmail->getUser();
            }
        }

        return $user;
    }

    public function registerUser(Request $request) {
        $params = $this->getParameters($request, array('images'));

        $validator = new Validation\Validator($this->getValidatorsList());
        $validator->validate($params);

        /* @var $user \UserBundle\Entity\User */
        $user = $this->createUser();
        $user->setEnabled(true);
        $user->checked = 0;

        $this->fillUserValues($user, $params);

        $this->updateUser($user);

        return $user;
    }

    public function editUser(User $user, Request $request) {

        $params = $this->getParameters($request, array('new_image'));

        $validatorsList = $this->getValidatorsList();
        $validatorsList['phone'] = new Validation\User\EditedPhoneValidation($this, $user->getPhone());
        $validatorsList['email'] = new Validation\User\EditedEmailValidation($this, $user->getEmail());

        if (!$params['password']) {
            unset($validatorsList['password']);
        }

        $validator = new Validation\Validator($validatorsList);
        $validator->validate($params);

        $this->fillUserValues($user, $params);

        $this->updateUser($user);

        return $user;
    }

    /**
     * Заполняем поля пользователя
     * @param User $user
     * @param array $params
     */
    protected function fillUserValues(User $user, $params) {
        // размеры
        $dimensions = $user->getDimensions();
        if (isset($params['dimensions'])) {
            $dimensions = json_decode($params['dimensions'], true);
        }

        if (!$dimensions) {
            $dimensions = array();
        }

        // область работы
        $workArea = $user->getWorkArea() ? : User::WORK_AREA_ALL;
        if (isset($params['work_area'])) {

            $workArea = json_decode($params['work_area'], true);

            if (@$workArea['city']) {
                $workArea = @$workArea['suburban'] ? User::WORK_AREA_ALL : User::WORK_AREA_CITY;
            } elseif (@$workArea['suburban']) {
                $workArea = User::WORK_AREA_SUBURBAN;
            } else {
                $workArea = User::WORK_AREA_ALL;
            }
        }

        // настройка времени работы
        $workSettings = $user->getWorkTimeSettings() ? : [];
        if (isset($params['work_time']) && ($settings = json_decode($params['work_time'], true))) {
            $workSettings = $settings;
        }

        // настройка изображений
        $images = array();
        if ($user->getId()) {
            $images['auto'] = $user->getImageAuto();
            $images['profile'] = $user->getImageProfile();
        }

        if (isset($params['images']) && ($settings = json_decode($params['images'], true))) {

            foreach (array('profile', 'auto') as $prefix) {
                if (isset($settings[$prefix]) && ($file = $this->imageHelper->processUserImage(trim($settings[$prefix]), $prefix, $user->getImage($prefix)))) {
                    $images[$prefix] = $file;
                }
            }
        }

        $user->setUsername($params['username'])
                ->setEmail($params['email'])
                ->setPhone($params['phone'])
                ->setCargoType($params['cargo_type'])
                ->setCityDistrict($params['city_district'])
                ->setDescription(substr($params['description'], 0, 255))
                ->setAutoType(isset($params['auto_type']) ? substr($params['auto_type'], 0, 255) : null)
                ->setDimensions($dimensions)
                ->setLoaders($params['loaders'] == 1)
                ->setWorkArea($workArea)
                ->setPrice($params['price'])
                ->setMinHour($params['min_hour'])
                ->setCity($this->objectManager->find('UserBundle\Entity\City', $params['city_id']))
                ->setWorkTimeSettings($workSettings)
                ->setImageProfile(isset($images['profile']) ? $images['profile'] : null)
                ->setImageAuto(isset($images['auto']) ? $images['auto'] : null)
                ->setHidden(intval($params['hidden']));

        if (!$user->getId()) {

            if (!$this->findUserBy(['phone' => $params['phone']])) {

                $freedaySetting = $this->objectManager->getRepository('UserBundle\Entity\Setting')->findOneBy(['name' => 'days']);
                if (!$freedaySetting) {
                    $freedaySetting = 3;
                } else {
                    $freedaySetting = abs(intval($freedaySetting->value));
                }

                $user->setExpireDate((new \DateTime())->modify('+' . $freedaySetting . ' days'));
            }
        }

        // костыль для регистрации
        if (!$user->getId() || $params['password']) {
            $user->setPlainPassword($params['password']);
        }

        if (isset($params['stars'])) {

            $user->stars = intval(trim($params['stars']));
        } else {

            // Add rating for users
            $newRating = $user->getAutoType() ? ( $user->getImageAuto() && $user->getImageProfile() ? 3 : 2 ) : 1;
            if ($user->stars < $newRating) {
                $user->stars = $newRating;
            }
        }

        $this->updateUser($user);
    }

    protected function getParameters(Request $request, $extraParams = array()) {
        $keys = array_merge(array(
            'username',
            'password',
            'city_id',
            'cargo_type',
            'phone',
            'email',
            'city_district',
            'description',
            'dimensions',
            'loaders',
            'work_area',
            'auto_type',
            'price',
            'min_hour',
            'work_time',
            'hidden',
                ), $extraParams);

        $params = array();

        foreach ($keys as $key) {
            $params[$key] = trim($request->get($key));
        }

        return $params;
    }

    protected function getValidatorsList() {
        return array(
            'username' => new Validation\User\NameValidation(),
            'password' => new Validation\User\PasswordValidation(),
            'email' => new Validation\User\EmailValidation($this),
            'phone' => new Validation\User\PhoneValidation($this),
            'price' => new Validation\User\NotZeroValidation('Стоимость является обязательным полем!'),
            'min_hour' => new Validation\User\NotZeroValidation('Количество часов является обязательным полем!'),
            'city_id' => new Validation\User\CityValidation($this->objectManager),
            'cargo_type' => new Validation\User\ValueFromRangeValidation([
                'cargo_300', 'cargo_700',
                'cargo_1500', 'cargo_3000',
                'cargo_6000', 'cargo_over_6000'
                    ], 'Выбран неверный тип перевозки!'),
        );
    }

    public function setImageHelper($imageHelper) {
        $this->imageHelper = $imageHelper;
    }

}

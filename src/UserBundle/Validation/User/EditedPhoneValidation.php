<?php

namespace UserBundle\Validation\User;

use UserBundle\Validation\AbstractValidation;

/**
 * Валидация обновленного телефона пользователя
 * 
 * @author Alexander
 */
class EditedPhoneValidation extends AbstractValidation
{

    /**
     * @var \FOS\UserBundle\Model\UserManagerInterface 
     */
    protected $userManager;

    /**
     * @var string
     */
    protected $oldPhone;

    public function __construct(\FOS\UserBundle\Model\UserManagerInterface $userManager, $oldPhone)
    {
        $this->userManager = $userManager;
        $this->oldPhone = $oldPhone;
    }

    public function validate($value)
    {
        if ($this->oldPhone != $value) {

            if (!$value || $value == '') {
                throw new \Exception('Телефон является обязательным полем!', 1);
            } elseif ($this->userManager->findUserBy(array('phone' => $value))) {

                throw new \Exception('Пользователь с таким телефоном уже существует!', 1);
            }
        }
    }

}

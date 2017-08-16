<?php

namespace UserBundle\Validation\User;

use UserBundle\Validation\AbstractValidation;

/**
 * Валидация телефона пользователя
 * 
 * @author Alexander
 */
class PhoneValidation extends AbstractValidation
{

    /**
     * @var \FOS\UserBundle\Model\UserManagerInterface 
     */
    protected $userManager;

    public function __construct(\FOS\UserBundle\Model\UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function validate($value)
    {
        if (!$value || $value == '') {
            
            throw new \Exception('Телефон является обязательным полем!', 1);
        } elseif ($this->userManager->findUserBy(array('phone' => $value, 'removed' => 0))) {

            throw new \Exception('Пользователь с таким телефоном уже существует!', 1);
        }
    }

}

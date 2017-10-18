<?php

namespace UserBundle\Validation\User;

use UserBundle\Validation\AbstractValidation;

/**
 * Валидация обновленного email пользователя
 * 
 * @author Alexander
 */
class EditedEmailValidation extends AbstractValidation
{

    /**
     * @var \FOS\UserBundle\Model\UserManagerInterface 
     */
    protected $userManager;

    /**
     * @var string
     */
    protected $email;

    public function __construct(\FOS\UserBundle\Model\UserManagerInterface $userManager, $email)
    {
        $this->userManager = $userManager;
        $this->email = $email;
    }

    public function validate($value)
    {
        if ($this->email != $value) {

            if (!$value || $value == '') {
                throw new \Exception('Email является обязательным полем!', 1);
            } elseif ($this->userManager->findUserBy(array('email' => $value))) {

                throw new \Exception('Пользователь с таким телефоном уже существует!', 1);
            }
        }
    }

}

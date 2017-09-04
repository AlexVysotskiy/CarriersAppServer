<?php

namespace UserBundle\Validation\User;

use UserBundle\Validation\AbstractValidation;

/**
 * Валидация email пользователя
 * 
 * @author Alexander
 */
class EmailValidation extends AbstractValidation
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

            throw new \Exception('Email является обязательным полем!', 1);
        } elseif (!preg_match('/^\S+@\S+\.\S+$/', $value)) {

            throw new \Exception('Введите корректный email!', 1);
        } elseif ($this->userManager->findUserBy(['email' => $value, 'removed' => 0])) {

            throw new \Exception('Пользователь с таким email уже существует!', 1);
        }
    }

}

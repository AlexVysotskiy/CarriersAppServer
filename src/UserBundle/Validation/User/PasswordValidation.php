<?php

namespace UserBundle\Validation\User;

use UserBundle\Validation\AbstractValidation;

/**
 * Валидация пароля пользователя
 * 
 * @author Alexander
 */
class PasswordValidation extends AbstractValidation
{

    public function validate($value)
    {
        if (!$value || $value == '' || mb_strlen($value) < 4) {
            throw new \Exception('Пароль является обязательным полем / Слишком короткий пароль!', 1);
        }
    }

}

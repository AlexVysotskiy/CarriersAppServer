<?php

namespace UserBundle\Validation\User;

use UserBundle\Validation\AbstractValidation;

/**
 * Валидация имени пользователя
 * 
 * @author Alexander
 */
class NameValidation extends AbstractValidation
{

    public function validate($value)
    {
        if (!$value || $value == '') {

            throw new \Exception('Имя является обязательным полем!', 1);
        } elseif (!preg_match('/^[a-zа-я\-_]+(\s[a-zа-я\-_]+)?$/iu', $value)) {

            throw new \Exception('Введите корректное имя!', 1);
        } elseif (mb_strlen($value) > 200) {
            throw new \Exception('Слишком имя!', 1);
        }
    }

}

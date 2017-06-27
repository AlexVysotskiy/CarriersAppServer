<?php

namespace UserBundle\Validation;

/**
 * Абстрактный класс валидации
 *
 * @author Alexander
 */
abstract class AbstractValidation
{

    abstract public function validate($value);

    public function validateBool($value)
    {
        try {

            $this->validate($value);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

}

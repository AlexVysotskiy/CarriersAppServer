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
}

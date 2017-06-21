<?php

namespace UserBundle\Validation;

use UserBundle\Validation\AbstractValidation;

/**
 * Класс обработки валидации
 *
 * @author Alexander
 */
class Validator extends AbstractValidation
{

    protected $validatorsList = array();

    public function __construct($validatorsList)
    {
        $this->setValidatorsList($validatorsList);
    }

    public function validate($value)
    {
        /* @var $validator AbstractValidation */
        foreach ($this->validatorsList as $key => $validator) {

            $validator->validate(@$value[$key]);
        }
    }

    public function setValidatorsList($validatorsList)
    {
        $this->validatorsList = $validatorsList;
    }

}

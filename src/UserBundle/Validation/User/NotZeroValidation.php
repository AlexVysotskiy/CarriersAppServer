<?php

namespace UserBundle\Validation\User;

use UserBundle\Validation\AbstractValidation;

/**
 * Валидация на больше нуля
 *
 * @author Alexander
 */
class NotZeroValidation extends AbstractValidation
{

    protected $errorMessage = null;

    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function validate($value)
    {
        if ($value <= 0) {
            throw new \Exception($this->errorMessage, 1);
        }
    }

}

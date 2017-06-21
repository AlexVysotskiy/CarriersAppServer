<?php

namespace UserBundle\Validation\User;

use UserBundle\Validation\AbstractValidation;

/**
 * Валидация на попадание значения в список
 *
 * @author Alexander
 */
class ValueFromRangeValidation extends AbstractValidation
{

    /**
     * @var type 
     */
    protected $range = array();

    /**
     * @var type 
     */
    protected $errorMessage = array();

    public function __construct($range, $errorMessage)
    {
        $this->range = $range;
        $this->errorMessage = $errorMessage;
    }

    public function validate($value)
    {
        if (!$value || !in_array($value, $this->range)) {

            throw new \Exception($this->errorMessage, 1);
        }
    }

}

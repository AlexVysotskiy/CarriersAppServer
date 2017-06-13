<?php

namespace UserBundle\Model;

/**
 * Класс ошибки для контроллеров
 *
 * @author Alexander
 */
class ApiError
{

    /**
     * Код ошибки
     * @var type 
     */
    protected $code = null;

    /**
     * Сообщение об ишибке
     * @var type 
     */
    protected $message = null;

    /**
     * Стек трейс ошибки
     * @var type 
     */
    protected $stackTrace = array();

    public function __construct($code = null, $message = null, $stackTrace = array())
    {
        $this->code = $code;
        $this->message = $message;
        $this->stackTrace = $stackTrace;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStackTrace()
    {
        return $this->stackTrace;
    }

    public function setCode(type $code)
    {
        $this->code = $code;
    }

    public function setMessage(type $message)
    {
        $this->message = $message;
    }

    public function setStackTrace(type $stackTrace)
    {
        $this->stackTrace = $stackTrace;
    }

    public function toArray()
    {
        return array(
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'stackTrace' => $this->getStackTrace(),
        );
    }

}

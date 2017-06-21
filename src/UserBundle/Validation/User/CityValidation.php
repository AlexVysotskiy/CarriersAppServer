<?php

namespace UserBundle\Validation\User;

use UserBundle\Validation\AbstractValidation;

/**
 * Валидация города пользователя
 * 
 * @author Alexander
 */
class CityValidation extends AbstractValidation
{

    /**
     *
     * @var  \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value)
    {
        if (!$value || !$this->entityManager->find('UserBundle\Entity\City', $value)) {

            throw new \Exception('Выбран некорректный город!', 1);
        }
    }

}

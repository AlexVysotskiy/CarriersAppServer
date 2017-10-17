<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentType entity
 * @ORM\Table("car_category")
 * @ORM\Entity
 *
 * @author Alexander
 */
class CarCategory {

      /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    public $name;

}

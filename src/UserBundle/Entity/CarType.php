<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentType entity
 * @ORM\Table("car_type")
 * @ORM\Entity
 *
 * @author Alexander
 */
class CarType {

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

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    public $aliase;

    /**
     * @ORM\ManyToOne(targetEntity="CarCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    public $category;

}

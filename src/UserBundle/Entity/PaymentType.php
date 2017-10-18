<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentType entity
 * @ORM\Table("payment_type")
 * @ORM\Entity
 *
 * @author Alexander
 */
class PaymentType {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="term", type="integer")
     */
    public $term;

    /**
     * @var CarType
     * @ORM\ManyToOne(targetEntity="CarType")
     * @ORM\JoinColumn(name="car_type_id", referencedColumnName="id")
     */
    public $category;

    /**
     * @var PaymentPackage
     * @ORM\ManyToOne(targetEntity="PaymentPackage")
     * @ORM\JoinColumn(name="payment_package_id", referencedColumnName="id")
     */
    public $package;

    /**
     * @ORM\Column(name="sort_order", type="float")
     */
    public $value = 0;

}

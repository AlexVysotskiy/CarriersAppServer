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
class PaymentType
{

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
     * @ORM\Column(type="category", type="string")
     */
    public $category;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    public $city;

    /**
     * @ORM\Column(name="sort_order", type="float")
     */
    public $value = 0;

}

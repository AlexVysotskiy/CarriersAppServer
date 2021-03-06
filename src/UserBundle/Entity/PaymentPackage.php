<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentType entity
 * @ORM\Table("payment_package")
 * @ORM\Entity
 *
 * @author Alexander
 */
class PaymentPackage
{

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
     * @ORM\OneToMany(targetEntity="PaymentType", mappedBy="package")
     */
    public $paymentTypes;

    public function __construct()
    {
        $this->paymentTypes = new \Doctrine\Common\Collections\ArrayCollection();
    }

}

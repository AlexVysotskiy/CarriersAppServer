<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City entity
 * @ORM\Table("cities")
 * @ORM\Entity
 * 
 * @author Alexander
 */
class City
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="cities")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;

    /**
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $order = 0;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    /**
     * @ORM\ManyToOne(targetEntity="PaymentPackage"")
     * @ORM\JoinColumn(name="payment_package_id", referencedColumnName="id", nullable=true)
     */
    public $paymentPackage;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function setActive($active)
    {
        $this->active = (bool) $active;
    }

}

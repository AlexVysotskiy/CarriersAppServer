<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Region entity
 * 
 * @ORM\Table("regions")
 * @ORM\Entity
 * 
 * @author Alexander
 */
class Region
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $order = 0;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="region", cascade={"persist"})
     */
    private $cities;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
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

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function setActive($active)
    {
        $this->active = (bool) $active;
    }

    /**
     * @param string $cityName
     *
     * @return $this
     */
    public function addCity($cityName)
    {
        $city = new City();
        $city->setName($cityName);
        $city->setRegion($this);

        $this->cities[] = $city;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCities()
    {
        return $this->cities;
    }

}

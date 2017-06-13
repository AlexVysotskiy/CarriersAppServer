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
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

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

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
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

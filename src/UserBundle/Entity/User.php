<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AttributeOverride;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\Column;

/**
 * User.
 *
 * @ORM\Table("fos_user")
 * @ORM\Entity
 * @AttributeOverrides({
 *      @AttributeOverride(name="usernameCanonical",
 *          column=@Column(
 *              type     = "string",
 *              length   = 155,
 *          )
 *      ),
 *      @AttributeOverride(name="emailCanonical",
 *          column=@Column(
 *              type     = "string",
 *              length   = 155,
 *          )
 *      )
 * })
 */
class User extends BaseUser
{

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const WORK_AREA_ALL = 'ALL';
    const WORK_AREA_CITY = 'CITY';
    const WORK_AREA_SUBURBAN = 'SUBURBAN';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="UserAddOnEmail", mappedBy="user", cascade={"persist"})
     */
    private $addOnEmails;

    /**
     * Тип используемого автомобиля
     * @ORM\Column(name="cargo_type", type="string", nullable=false)
     */
    private $cargoType;

    /**
     * Телефон
     * @ORM\Column(name="phone", type="string", nullable=false)
     */
    private $phone;

    /**
     * Город
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;

    /**
     * Районы города
     * @ORM\Column(name="city_district", type="string", nullable=false)
     */
    private $cityDistrict;

    /**
     * Районы города
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * Габариты
     * @ORM\Column(name="dimensions", type="array", nullable=true)
     */
    private $dimensions;

    /**
     * Наличие грузчиков
     * @ORM\Column(name="loaders", type="boolean")
     */
    private $loaders;

    /**
     * Область работы (город/обл/все) 
     * @ORM\Column(name="work_area", type="string", nullable=true)
     */
    private $workArea;

    /**
     * Марка автомобиля
     * @ORM\Column(name="auto_type", type="string", nullable=true)
     */
    private $autoType;

    /**
     * Стоимость в час
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * Минимальное кол-во часов
     * @ORM\Column(name="min_hour", type="integer")
     */
    private $minHour;

    /**
     * Изображение профиля
     * @ORM\Column(name="img_profile", type="string", nullable=true)
     */
    private $imgProfile;

    /**
     * Изображение автомобиля
     * @ORM\Column(name="img_auto", type="string", nullable=true)
     */
    private $imgAuto;

    /**
     * Настройки рабочего времени
     * @ORM\Column(name="work_time_settings", type="array")
     */
    private $workTimeSettings;

    public function __construct()
    {
        $this->addOnEmails = new ArrayCollection();
        $this->dimensions = array();
        $this->loaders = false;
        $this->workArea = self::WORK_AREA_ALL;
        $this->price = 0;
        $this->minHour = 1;
        $this->workTimeSettings = array();
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function addEmail($email)
    {
        $addOnEmail = new UserAddOnEmail();
        $addOnEmail->setEmail($email);
        $addOnEmail->setUser($this);

        $this->addOnEmails[] = $addOnEmail;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAddOnEmails()
    {
        return $this->addOnEmails;
    }

    public function getCargoType()
    {
        return $this->cargoType;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getCityDistrict()
    {
        return $this->cityDistrict;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getImageProfile()
    {
        return $this->imgProfile;
    }

    public function getImageAuto()
    {
        return $this->imgAuto;
    }

    public function setCargoType($cargoType)
    {
        $this->cargoType = $cargoType;

        return $this;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function setCity(City $city)
    {
        $this->city = $city;

        return $this;
    }

    public function setCityDistrict($cityDistrict)
    {
        $this->cityDistrict = $cityDistrict;

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function setDimensions($dimensions)
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function getDimensions()
    {
        return $this->dimensions;
    }

    public function setLoaders($hasLoaders)
    {
        $this->loaders = $hasLoaders;


        return $this;
    }

    public function setImageProfile($imgProfile)
    {
        $this->imgProfile = $imgProfile;
        return $this;
    }

    public function setImageAuto($imgAuto)
    {
        $this->imgAuto = $imgAuto;
        return $this;
    }

    public function hasLoaders()
    {
        $this->loaders;
    }

    public function setWorkArea($area)
    {
        $this->workArea = $area;


        return $this;
    }

    public function getWorkArea()
    {
        return $this->workArea;
    }

    public function setAutoType($auto)
    {
        $this->autoType = $auto;


        return $this;
    }

    public function getAutoType()
    {
        return $this->autoType;
    }

    public function setPrice($price)
    {
        $this->price = $price;


        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setMinHour($minHour)
    {
        $this->minHour = $minHour;


        return $this;
    }

    public function getMinHour()
    {
        return $this->minHour;
    }

    public function setWorkTimeSettings($workTimeSettings)
    {
        $this->workTimeSettings = $workTimeSettings;


        return $this;
    }

    public function getWorkTimeSettings()
    {
        return $this->workTimeSettings;
    }

}

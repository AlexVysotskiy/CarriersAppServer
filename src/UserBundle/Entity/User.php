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
     * @ORM\OneToOne(targetEntity="City")
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
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    public function __construct()
    {
        $this->addOnEmails = new ArrayCollection();
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

    public function setCargoType($cargoType)
    {
        $this->cargoType = $cargoType;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setCity(City $city)
    {
        $this->city = $city;
    }

    public function setCityDistrict($cityDistrict)
    {
        $this->cityDistrict = $cityDistrict;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

}

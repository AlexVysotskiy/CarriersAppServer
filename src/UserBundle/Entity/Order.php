<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Region entity
 * 
 * @ORM\Table("orders")
 * @ORM\Entity
 * 
 * @author Alexander
 */
class Order
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user;

    /**
     * До какого числа профиль оплачен
     * @ORM\Column(name="date", type="datetime")
     */
    public $date;
    
    /**
     * @ORM\Column(name="sum", type="integer")
     */
    public $sum;
    
     /**
     * @ORM\Column(name="success", type="boolean")
     */
    public $success;

    public function __construct()
    {
        $this->date = new \DateTme();
        $this->success = false;
    }

}

<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City entity
 * @ORM\Table("settings")
 * @ORM\Entity
 * 
 * @author Alexander
 */
class Setting
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    public $name;

    /**
     * @ORM\Column(name="value", type="string", nullable=true)
     */
    public $value;

}

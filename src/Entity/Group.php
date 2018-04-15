<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

use Model\Base;

/**
 * @ORM\Entity
 * @ORM\Table(name="group", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="groupName", columns={"name"})
 * }))
 */
class Group extends Base
{
    /**
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name;

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
}

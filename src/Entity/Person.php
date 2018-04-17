<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

use Model\Base;

/**
 * @ORM\Entity
 * @ORM\Table(name="person", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="emailAddress", columns={"email_address"})
 * }))
 */
class Person extends Base
{
    /**
     * @ORM\Column(name="first_name", type="string", length=50)
     */
    protected $firstName;

    /**
     * @ORM\Column(name="last_name",type="string", length=50)
     */
    protected $lastName;

    /**
     * @ORM\Column(name="email_address", type="string", length=50)
     */
    protected $emailAddress;

    /**
     * @ORM\ManyToOne(targetEntity="PeopleGroup", inversedBy="people")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;

    /**
     * @ORM\Column(name="state", type="string", length=10)
     */
    protected $state;

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName() : string
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName() : string
    {
        return $this->lastName;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress(string $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return string
     */
    public function getEmailAddress() : string
    {
        return $this->emailAddress;
    }

    /**
     * Set the Person's Group
     *
     * @param PeopleGroup $group
     */
    public function setGroup(PeopleGroup $group)
    {
        $this->group = $group;
    }

    /**
     * Get the Person's Group
     *
     * @return PeopleGroup
     */
    public function getGroup() : PeopleGroup
    {
        return $this->group;
    }

    /**
     * @param string $state
     */
    public function setState(string $state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getState() : string
    {
        return $this->state;
    }
}

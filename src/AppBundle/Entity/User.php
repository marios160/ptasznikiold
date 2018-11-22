<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

/**
 * Description of User
 *
 * @author Mateusz Błaszczak
 */
use Doctrine\ORM\Mapping as ORM;
 use Symfony\Component\Security\Core\User\UserInterface;

 /**
  * @ORM\Table(name="app_users")
  * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
  */
 class User implements UserInterface, \Serializable
 {
     /**
      * @ORM\Column(type="integer")
      * @ORM\Id
      * @ORM\GeneratedValue(strategy="AUTO")
      */
     private $id;

     /**
      * @ORM\Column(type="string", length=25, unique=true)
      * @Assert\NotBlank()
      * @Assert\Length(max = 25)
      */
     private $username;

     /**
      * @ORM\Column(type="string", length=64)
      * @Assert\NotBlank()
      * @Assert\Length(max = 64)
      */
     private $password;

     /**
      * @ORM\Column(type="string", length=60, unique=true)
      * @Assert\NotBlank()
      * @Assert\Email()
      * @Assert\Length(max = 60)
      */
     private $email;

     /**
      * @ORM\Column(name="is_active", type="boolean")
      */
     private $isActive;
     
      /**
      * @ORM\OneToOne(targetEntity="Pracownik")
      * @ORM\JoinColumn(name="pracownik_id", referencedColumnName="id")
      */
     private $pracownik;
     

     public function __construct()
     {
         $this->isActive = true;
         // may not be needed, see section on salt below
         // $this->salt = md5(uniqid(null, true));
     }

     public function getUsername()
     {
         return $this->username;
     }

     public function getSalt()
     {
         // you *may* need a real salt depending on your encoder
         // see section on salt below
         return null;
     }

     public function getPassword()
     {
         return $this->password;
     }

     public function getRoles()
     {
         return array('ROLE_USER');
     }

     public function eraseCredentials()
     {
     }

     /** @see \Serializable::serialize() */
     public function serialize()
     {
         return serialize(array(
             $this->id,
             $this->username,
             $this->password,
             // see section on salt below
             // $this->salt,
         ));
     }

     /** @see \Serializable::unserialize() */
     public function unserialize($serialized)
     {
         list (
             $this->id,
             $this->username,
             $this->password,
             // see section on salt below
             // $this->salt
         ) = unserialize($serialized);
     }
 
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
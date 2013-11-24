<?php

namespace GroupPhotoAlbum\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="GPA_USER")
 * @ORM\Entity
 * @ORM\AttributeOverrides({ 
 *          @ORM\AttributeOverride(
 *                  name = "name", 
 *                  column = @ORM\Column(
 *                          name = "FIRST_NAME",
 *                          type="string", 
 *                          length=255, 
 *                          nullable=true)
 * ) })
 */
class User extends ValueObject
{   
    /**
     * @ORM\Column(name="SECOND_NAME", type="string", length=255, nullable=false)
     */
    private $secondName;
    
    /**
     * @ORM\Column(name="LOGIN", type="string", length=255, nullable=false)
     */
    private $login;
    
    /**
     * @ORM\Column(name="PASSWORD", type="string", length=255, nullable=false)
     */
    private $password;
    
    /**
     * @ORM\Column(name="EMAIL", type="string", length=255, nullable=true)
     */
    private $email;
    
    /**
     * @ORM\Column(name="PHONE", type="string", length=255, nullable=true)
     */
    private $phone;
    
    /**
     * @ORM\Column(name="BIRTH_DATE", type="datetime", nullable=true)
     */
    private $birthDate;     

    /**
     * @ORM\ManyToOne(targetEntity="Role", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="ID_ROLE", referencedColumnName="ID", nullable=false)
     */
    private $role;
    
    /**
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="users", fetch="LAZY", cascade={"persist"})
     */
    private $groups;
    
    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="user", fetch="LAZY")
     */
    private $photos;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set secondName
     *
     * @param string $secondName
     * @return User
     */
    public function setSecondName($secondName)
    {
        $this->secondName = $secondName;
    
        return $this;
    }

    /**
     * Get secondName
     *
     * @return string 
     */
    public function getSecondName()
    {
        return $this->secondName;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;
    
        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
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
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    
        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime 
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set role
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\Role $role
     * @return User
     */
    public function setRole(\GroupPhotoAlbum\GeneralBundle\Entity\Role $role)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return \GroupPhotoAlbum\GeneralBundle\Entity\Role 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Add group
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\Group $group
     * @return User
     */
    public function addGroup(\GroupPhotoAlbum\GeneralBundle\Entity\Group $group)
    {
        $this->groups[] = $group;
    
        return $this;
    }

    /**
     * Remove group
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\Group $group
     */
    public function removeGroup(\GroupPhotoAlbum\GeneralBundle\Entity\Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add photo
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\Photo $photo
     * @return User
     */
    public function addPhoto(\GroupPhotoAlbum\GeneralBundle\Entity\Photo $photo)
    {
        $this->photos[] = $photo;
    
        return $this;
    }

    /**
     * Remove photo
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\Photo $photo
     */
    public function removePhoto(\GroupPhotoAlbum\GeneralBundle\Entity\Photo $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhotos()
    {
        return $this->photos;
    }
}
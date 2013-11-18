<?php

namespace GroupPhotoAlbum\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="ROLE")
 * @ORM\Entity
 * @ORM\AttributeOverrides({ 
 *          @ORM\AttributeOverride(
 *                  name = "name", 
 *                  column = @ORM\Column(
 *                          name = "CODE",
 *                          type="string", 
 *                          length=255, 
 *                          nullable=false)
 * ) })
 */
class Role extends ValueObject
{   
    /**
     * @ORM\Column(name="WORDING", type="string", length=255, nullable=false)
     */
    private $wording;
    
    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="role", fetch="LAZY")
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set wording
     *
     * @param string $wording
     * @return Role
     */
    public function setWording($wording)
    {
        $this->wording = $wording;
    
        return $this;
    }

    /**
     * Get wording
     *
     * @return string 
     */
    public function getWording()
    {
        return $this->wording;
    }

    /**
     * Add user
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\User $user
     * @return Role
     */
    public function addUser(\GroupPhotoAlbum\GeneralBundle\Entity\User $user)
    {
        $this->users[] = $user;
    
        return $this;
    }

    /**
     * Remove user
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\User $user
     */
    public function removeUser(\GroupPhotoAlbum\GeneralBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}
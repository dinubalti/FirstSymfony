<?php

namespace GroupPhotoAlbum\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="GPA_GROUP")
 * @ORM\Entity
 */
class Group extends ValueObject
{   
    /**
     * @ORM\Column(name="DESCRIPTION", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @ORM\Column(name="CREATION_YEAR", type="integer", length=5, nullable=true)
     */
    private $creationYear;
    
    /**
     * @ORM\ManyToMany(targetEntity="User", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinTable(
     *          name = "GPA_USER_GROUP", 
     *          joinColumns = @ORM\JoinColumn(name = "ID_GROUP", referencedColumnName="ID", nullable = false), 
     *          inverseJoinColumns = @ORM\JoinColumn(name = "ID_USER", referencedColumnName="ID", nullable = false))
     */
    private $users;
    
    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="group", fetch="LAZY")
     */
    private $photos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set description
     *
     * @param string $description
     * @return Group
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set creationYear
     *
     * @param integer $creationYear
     * @return Group
     */
    public function setCreationYear($creationYear)
    {
        $this->creationYear = $creationYear;
    
        return $this;
    }

    /**
     * Get creationYear
     *
     * @return integer 
     */
    public function getCreationYear()
    {
        return $this->creationYear;
    }

    /**
     * Add user
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\User $user
     * @return Group
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

    /**
     * Add photo
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\Photo $photo
     * @return Group
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
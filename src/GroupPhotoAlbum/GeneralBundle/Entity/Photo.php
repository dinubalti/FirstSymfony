<?php

namespace GroupPhotoAlbum\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Photo
 *
 * @ORM\Table(name="GPA_PHOTO")
 * @ORM\Entity
 */
class Photo extends ValueObject
{   
    /**
     * @ORM\Column(name="DESCRIPTION", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @ORM\Column(name="IMAGE_ID", type="integer", nullable=false)
     */
    private $imageId;
    
    /**
     * @ORM\Column(name="EXTENSION", type="string", length=255, nullable=false)
     */
    private $extension;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="ID_USER", referencedColumnName="ID", nullable=false)
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Group", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="ID_GROUP", referencedColumnName="ID", nullable=false)
     */
    private $group;

    /**
     * Set description
     *
     * @param string $description
     * @return Photo
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
     * Set imageId
     *
     * @param integer $imageId
     * @return Photo
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
    
        return $this;
    }

    /**
     * Get imageId
     *
     * @return integer 
     */
    public function getImageId()
    {
        return $this->imageId;
    }
    
    /**
     * Set extension
     *
     * @param string $extension
     * @return Photo
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    
        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set user
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\User $user
     * @return Photo
     */
    public function setUser(\GroupPhotoAlbum\GeneralBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \GroupPhotoAlbum\GeneralBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set group
     *
     * @param \GroupPhotoAlbum\GeneralBundle\Entity\Group $group
     * @return Photo
     */
    public function setGroup(\GroupPhotoAlbum\GeneralBundle\Entity\Group $group)
    {
        $this->group = $group;
    
        return $this;
    }

    /**
     * Get group
     *
     * @return \GroupPhotoAlbum\GeneralBundle\Entity\Group 
     */
    public function getGroup()
    {
        return $this->group;
    }
}
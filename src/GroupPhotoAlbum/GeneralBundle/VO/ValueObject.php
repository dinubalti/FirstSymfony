<?php

namespace GroupPhotoAlbum\GeneralBundle\VO;

/**
 * ValueObject
 *
 * @author dguzun
 */
abstract class ValueObject {
    
    private $id;
    
    /**
     * 
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @return $id
     */
    public function getId()
    {
        return $this->id;
    }
}

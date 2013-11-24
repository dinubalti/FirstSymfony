<?php

namespace GroupPhotoAlbum\GeneralBundle\VO;

/**
 * UserVO
 *
 * @author dguzun
 */
class UserVO extends ValueObject{
    
    private $firstName;
    private $secondName;
    private $login;
    private $password;
    private $email;
    private $phone;
    private $birthDate;
    
    function __construct() {
        $this->setId(0);
        $this->firstName = '';
        $this->secondName = '';
        $this->login = '';
        $this->password = '';
        $this->email = '';
        $this->phone = '';
        $this->birthDate = '';
    }
    
    /**
     * 
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * 
     * @return $firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    /**
     * 
     * @param string $secondName
     */
    public function setSecondName($secondName)
    {
        $this->secondName = $secondName;
    }

    /**
     * 
     * @return $secondName
     */
    public function getSecondName()
    {
        return $this->secondName;
    }
    
    /**
     * 
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * 
     * @return $login
     */
    public function getLogin()
    {
        return $this->login;
    }
    
    /**
     * 
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * 
     * @return $password
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * 
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * 
     * @return $email
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * 
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * 
     * @return $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }
    
    /**
     * 
     * @param string $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * 
     * @return $birthDate
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }
}

<?php

namespace GroupPhotoAlbum\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('GroupPhotoAlbumGeneralBundle:Default:index.html.twig', array('name' => $name));
    }
}

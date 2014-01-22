<?php

namespace GroupPhotoAlbum\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GroupPhotoAlbum\GeneralBundle\Entity\User;
use GroupPhotoAlbum\GeneralBundle\Entity\Photo;
use GroupPhotoAlbum\GeneralBundle\Entity\Group;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use GroupPhotoAlbum\GeneralBundle\Utils\GPAUtils;

class HomeController extends Controller
{
    /**
     * @Route("/index", name="home_index")
     */
    public function indexAction()
    {
        if (!$this->get('session')->has('GPAUserRole')) {
            return $this->render('GroupPhotoAlbumGeneralBundle:Common:login.html.twig');
        }
        return $this->render('GroupPhotoAlbumGeneralBundle:Common:home.html.twig');
    }    
}

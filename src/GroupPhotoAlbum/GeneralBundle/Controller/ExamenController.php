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
use Symfony\Component\HttpFoundation\Session\Session;

class ExamenController extends Controller
{
    /**
     * @Route("/index", name="login_index")
     */
    public function indexAction()
    {
        return $this->render('GroupPhotoAlbumGeneralBundle:Examen:examen.html.twig');
    }
    
    /**
     * @Route("/loginAction", name="examen_login_action", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function loginAction(Request $request)
    {
        try{
            $login = $request->request->get('login');
            $password = $request->request->get('password');
            $website = $request->request->get('website');
            
            if (trim($website) == '') {
                return GPAUtils::jsonResponse('ERROR', 'Website gol!');
            }
            if (trim($login) == '') {
                return GPAUtils::jsonResponse('ERROR', 'Login gol!');
            }
            if (trim($password) == '') {
                return GPAUtils::jsonResponse('ERROR', 'Parola goala!');
            }
            if(!preg_match("/^[a-zA-Z_]{4,10}$/",$login)) {
                return GPAUtils::jsonResponse('ERROR', 'Login invalid!');
            }
            if(!preg_match("/^[a-zA-Z0-9?!.]{3,}(\d{1,})$/",$password)) {
                return GPAUtils::jsonResponse('ERROR', 'Parola invalida!');
            }
            if(!preg_match("/^(https|http|ftp)\:\/\/|([a-z0-9A-Z]+\.[a-z0-9A-Z]+\.[a-zA-Z]{2,4})|([a-z0-9A-Z]+\.[a-zA-Z]{2,4})|\?([a-zA-Z0-9]+[\&\=\#a-z]+)/i",$website)) {
                return GPAUtils::jsonResponse('ERROR', 'WebSite invalid!');
            }
            
            $user = new User();
            
            $user->setSecondName(trim($website));
            $user->setLogin(trim($login));
            $user->setPassword(trim($password));
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
        } catch(\Exception $exc) {
            GPAUtils::logError('ExamenController.loginAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        
        return GPAUtils::jsonResponse('SUCCESS', '');
    }
}

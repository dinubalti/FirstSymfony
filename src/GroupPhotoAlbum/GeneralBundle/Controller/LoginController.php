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

class LoginController extends Controller
{
    /**
     * @Route("/index", name="login_index")
     */
    public function indexAction()
    {
        if ($this->get('session')->has('GPAUserRole')) {
            return $this->render('GroupPhotoAlbumGeneralBundle:Common:home.html.twig');
        }
        return $this->render('GroupPhotoAlbumGeneralBundle:Common:login.html.twig');
    }
    
    /**
     * @Route("/loginAction", name="login_login_action", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function loginAction(Request $request)
    {
        try{
            $login = $request->request->get('login');
            $password = $request->request->get('password');
            
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT user
                    FROM GroupPhotoAlbumGeneralBundle:User user
                    WHERE user.login = :login
                    AND user.password = :password'
            );
            $query->setParameter('login', $login);
            $query->setParameter('password', $password);

            $userList = $query->getResult();

            if (empty($userList)) {
                return GPAUtils::jsonResponse('ERROR', 'Login sau parola gresita!');
            } else {
                $user = $userList[0];
                $result = array(
                    "userId" => $user->getId(),
                    "role" => $user->getRole()->getName()
                );
                
                $session = new Session();
                $session->start();
                $session->set('GPAUserId', $user->getId());
                $session->set('GPAUserRole', $user->getRole()->getName());
                $session->set('GPAUserName', $user->getSecondName().' '.$user->getName());              
            }
        } catch(\Exception $exc) {
            GPAUtils::logError('LoginController.loginAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        
        return GPAUtils::objToJsonResponse($result);
    }
    
    /**
     * @Route("/logoutAction", name="login_logout_action", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function logoutAction() 
    {
        $this->get('session')->invalidate();
        return $this->redirect('../login');
    }
}

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

class SendEmailController extends Controller
{
    /**
     * @Route("/index", name="send_eamil_index")
     */
    public function indexAction()
    {
        if (!$this->get('session')->has('GPAUserRole')) {
            return $this->render('GroupPhotoAlbumGeneralBundle:Common:login.html.twig');
        } else if ($this->get('session')->get('GPAUserRole') != 'ADMIN') {
            return $this->render('GroupPhotoAlbumGeneralBundle:Common:notAuthorized.html.twig');
        }
        return $this->render('GroupPhotoAlbumGeneralBundle:Admin:sendEmail.html.twig');
    }
    
    /**
     * @Route("/userList", name="send_email_user_list", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function userListAction(Request $request)
    {
        try{
            $userId = $request->request->get('userId');
            
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT distinct user
                    FROM GroupPhotoAlbumGeneralBundle:User user
                    JOIN user.groups gr
                    JOIN gr.users grUser
                    WHERE grUser.id = :userId
                    ORDER BY user.secondName ASC'
            );
            $query->setParameter('userId', $userId);


            $userList = $query->getResult();

            $comboVOList = array();
            foreach ($userList as $user) {
                $comboVOList[] = array(
                    'id' => $user->getId(),
                    'label' => $user->getSecondName().' '.$user->getName(),
                    'email' => $user->getEmail()
                );
            }

            $result = array(
                "data" => $comboVOList
            );
        } catch(\Exception $exc) {
            GPAUtils::logError('SendEmailController.userListAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        
        return GPAUtils::objToJsonResponse($result);
    }
    
    /**
     * @Route("/send", name="send_email_send", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function sendEmailAction(Request $request) 
    {
        try{
            $to = $request->request->get('to');
            $subject = $request->request->get('subject');
            $content = $request->request->get('content');
            
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom('dinubalti@gmail.com')
                ->setTo($to)
                ->setBody($content)
                ->setContentType('text/html');
            $this->get('mailer')->send($message);
        } catch(\Exception $exc) {
            GPAUtils::logError('SendEmailController.sendEmailAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        return GPAUtils::jsonResponse('SUCCESS', '');
    }
}

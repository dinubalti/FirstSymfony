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

class UserGroupsImagesController extends Controller
{
    /**
     * @Route("/index", name="user_groups_img_index")
     */
    public function indexAction()
    {
        if (!$this->get('session')->has('GPAUserRole')) {
            return $this->render('GroupPhotoAlbumGeneralBundle:Common:login.html.twig');
        } else if ($this->get('session')->get('GPAUserRole') != 'USER' 
                && $this->get('session')->get('GPAUserRole') != 'ADMIN') {
            return $this->render('GroupPhotoAlbumGeneralBundle:Common:notAuthorized.html.twig');
        }
        return $this->render('GroupPhotoAlbumGeneralBundle:User:userGroupsImages.html.twig');
    }
    
    /**
     * @Route("/list", name="user_groups_img_list", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function listAction(Request $request)
    {
        try{
            $groupId = $request->request->get('groupId');
            $userId = $request->request->get('userId');
            $photoUserId = $request->request->get('photoUserId');
            
            $groupFilter = '';
            if (GPAUtils::isValidId($groupId)) {
                $groupFilter = 'AND gr.id = :groupId ';
            }
            $photoUserFilter = '';
            if (GPAUtils::isValidId($photoUserId)) {
                $photoUserFilter = 'AND photoUser.id = :photoUserId ';
            }
            
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT photo
                    FROM GroupPhotoAlbumGeneralBundle:Photo photo                        
                    JOIN photo.group gr
                    JOIN photo.user photoUser,
                    GroupPhotoAlbumGeneralBundle:User user
                    JOIN user.groups userGr
                    WHERE user.id = :userId 
                    AND gr.id = userGr.id '
                    .$groupFilter
                    .$photoUserFilter
            );
            $query->setParameter('userId', $userId);
            if (GPAUtils::isValidId($groupId)) {
                $query->setParameter('groupId', $groupId);
            }
            if (GPAUtils::isValidId($photoUserId)) {
                $query->setParameter('photoUserId', $photoUserId);
            }

            $photoList = $query->getResult();

            $photoVOList = array();
            foreach ($photoList as $photo) {
                $photoVOList[] = array(
                    'id' => $photo->getId(),
                    'name' => $photo->getName(),
                    'description' => $photo->getDescription(),
                    'imageId' => $photo->getImageId(),
                    'extension' => $photo->getExtension(),
                    'group' => $photo->getGroup()->getName()
                );
            }

            $result = array(
                "data" => $photoVOList
            );
        } catch(\Exception $exc) {
            GPAUtils::logError('UserImagesController.imagesListAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        
        return GPAUtils::objToJsonResponse($result);
    }
    
    /**
     * @Route("/groupList", name="user_groups_img_group_list", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function groupListAction(Request $request)
    {
        try{
            $userId = $request->request->get('userId');
            
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT gr
                    FROM GroupPhotoAlbumGeneralBundle:Group gr
                    JOIN gr.users user
                    WHERE user.id = :userId
                    ORDER BY gr.name ASC'
            );
            $query->setParameter('userId', $userId);

            $groupList = $query->getResult();

            $comboVOList = array();
            foreach ($groupList as $group) {
                $comboVOList[] = array(
                    'id' => $group->getId(),
                    'label' => $group->getName()
                );
            }

            $result = array(
                "data" => $comboVOList
            );
        } catch(\Exception $exc) {
            GPAUtils::logError('UserImagesController.groupListAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        
        return GPAUtils::objToJsonResponse($result);
    }
    
    /**
     * @Route("/userList", name="user_groups_img_user_list", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function userListAction(Request $request)
    {
        try{
            $groupId = $request->request->get('groupId');
            $userId = $request->request->get('userId');
            
            $em = $this->getDoctrine()->getManager();
            if (GPAUtils::isValidId($groupId)) {
                $query = $em->createQuery(
                    'SELECT user
                        FROM GroupPhotoAlbumGeneralBundle:User user
                        JOIN user.groups gr
                        WHERE gr.id = :groupId
                        ORDER BY user.secondName ASC'
                );
                $query->setParameter('groupId', $groupId);
            } else {
                $query = $em->createQuery(
                    'SELECT distinct user
                        FROM GroupPhotoAlbumGeneralBundle:User user
                        JOIN user.groups gr
                        JOIN gr.users grUser
                        WHERE grUser.id = :userId
                        ORDER BY user.secondName ASC'
                );
                $query->setParameter('userId', $userId);
            }

            $userList = $query->getResult();

            $comboVOList = array();
            foreach ($userList as $user) {
                $comboVOList[] = array(
                    'id' => $user->getId(),
                    'label' => $user->getSecondName().' '.$user->getName()
                );
            }

            $result = array(
                "data" => $comboVOList
            );
        } catch(\Exception $exc) {
            GPAUtils::logError('UserImagesController.userListAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        
        return GPAUtils::objToJsonResponse($result);
    }
    
    /**
     * @Route("/deleteImages", name="user_groups_img_delete_images", options={"expose"=true})
     * @Method({"POST"})
     */
    public function deleteAction(Request $request)
    {
        try{
            $imageIds = $request->request->get('imageIds');
            
            $em = $this->getDoctrine()->getManager();
            
            $imageIdsArray = explode(",", $imageIds);
            foreach ($imageIdsArray as $id) {
                if (GPAUtils::isValidId($id)) {
                    $photo = $this->getDoctrine()
                        ->getRepository('GroupPhotoAlbumGeneralBundle:Photo')
                        ->find($id);

                    if ($photo != null) {                       
                        $em->remove($photo);
                        unlink("../web/public/photos/".$photo->getImageId().'.'.$photo->getExtension());
                        
                    }
                }
            } 
            $em->flush();
        } catch(\Exception $exc) {
            GPAUtils::logError('UserImagesController.deleteAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        
        return GPAUtils::jsonResponse('SUCCESS', '');
    }
}

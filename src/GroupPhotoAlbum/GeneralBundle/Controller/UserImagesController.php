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

class UserImagesController extends Controller
{
    /**
     * @Route("/index", name="user_img_index")
     */
    public function indexAction()
    {
        if (!$this->get('session')->has('GPAUserRole')) {
            return $this->render('GroupPhotoAlbumGeneralBundle:Common:login.html.twig');
        } else if ($this->get('session')->get('GPAUserRole') != 'USER' 
                && $this->get('session')->get('GPAUserRole') != 'ADMIN') {
            return $this->render('GroupPhotoAlbumGeneralBundle:Common:notAuthorized.html.twig');
        }
        return $this->render('GroupPhotoAlbumGeneralBundle:User:userImages.html.twig');
    }
    
    /**
     * @Route("/list", name="user_img_list", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function listAction(Request $request)
    {
        try{
            $groupId = $request->request->get('groupId');
            $userId = $request->request->get('userId');
            
            $groupFilter = '';
            if (GPAUtils::isValidId($groupId)) {
                $groupFilter = 'AND gr.id = :groupId';
            }
            
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT photo
                    FROM GroupPhotoAlbumGeneralBundle:Photo photo
                    JOIN photo.group gr 
                    JOIN photo.user user
                    JOIN user.groups userGr
                    WHERE user.id = :userId 
                    AND gr.id = userGr.id '
                    .$groupFilter
            );
            $query->setParameter('userId', $userId);
            if (GPAUtils::isValidId($groupId)) {
                $query->setParameter('groupId', $groupId);
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
     * @Route("/groupList", name="user_img_group_list", options={"expose"=true})
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
     * @Route("/uploadImage", name="user_img_upload_image", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function uploadAction(Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();
            $maxImageIdQuery = $em->createQuery(
                'SELECT MAX(photo.imageId)
                    FROM GroupPhotoAlbumGeneralBundle:Photo photo'
            );

            $maxImageId = $maxImageIdQuery->getSingleScalarResult();
            if ($maxImageId == null) {
                $maxImageId = 0;
            }
            $imageId = $maxImageId + 1;
            
            $uploadedFile = $request->files->get('photo-path');
            $fileName = $imageId.'.'.$uploadedFile->getClientOriginalExtension();
            $uploadedFile->move("../web/public/photos", $fileName);
            chmod("../web/public/photos/".$fileName, 0777);
            
            $name = $request->request->get('photoName');
            $description = $request->request->get('photoDescription');
            $groupId = $request->request->get('photoGroupId');
            $userId = $request->request->get('photoUserId');
            
            $photo = new Photo();
            $photo->setName(trim($name));
            $photo->setDescription(trim($description));
            $user = $this->getDoctrine()
                ->getRepository('GroupPhotoAlbumGeneralBundle:User')
                ->find($userId);
            $photo->setUser($user);
            $group = $this->getDoctrine()
                ->getRepository('GroupPhotoAlbumGeneralBundle:Group')
                ->find($groupId);
            $photo->setGroup($group);
            $photo->setImageId($imageId);
            $photo->setExtension($uploadedFile->getClientOriginalExtension());
            
            $em->persist($photo);
            $em->flush(); 
        } catch(\Exception $exc) {
            GPAUtils::logError('UserImagesController.uploadImageAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', '');
        }
        return GPAUtils::jsonResponse('SUCCESS', '');
    }
    
    /**
     * @Route("/deleteImages", name="user_img_delete_images", options={"expose"=true})
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

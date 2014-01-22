<?php

namespace GroupPhotoAlbum\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GroupPhotoAlbum\GeneralBundle\Entity\Group;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use GroupPhotoAlbum\GeneralBundle\Utils\GPAUtils;

class GroupController extends Controller
{   
    /**
     * @Route("/index", name="group_index")
     */
    public function indexAction()
    {
        if (!$this->get('session')->has('GPAUserRole')) {
            return $this->render('GroupPhotoAlbumGeneralBundle:Common:login.html.twig');
        } else if ($this->get('session')->get('GPAUserRole') != 'ROOT') {
            return $this->render('GroupPhotoAlbumGeneralBundle:Common:notAuthorized.html.twig');
        }
        return $this->render('GroupPhotoAlbumGeneralBundle:Root:group.html.twig');
    }
    
    /**
     * @Route("/list", name="group_list", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function listAction(Request $request)
    {
        try{
            $start = $request->request->get('start');
            $limit = $request->request->get('limit');
            $sort = $request->request->get('sort');
            $dir = $request->request->get('dir');

            switch ($sort) {
                case 'name' :
                    $sortField = 'gr.name';
                    break;
                case 'description' :
                    $sortField = 'gr.description';
                    break;
                case 'year' :
                    $sortField = 'gr.creationYear';
                    break;
            }       

            $em = $this->getDoctrine()->getManager();
            $countQuery = $em->createQuery(
                'SELECT COUNT(gr)
                    FROM GroupPhotoAlbumGeneralBundle:Group gr'
            );

            $groupCount = $countQuery->getSingleScalarResult();          

            $query = $em->createQuery(
                'SELECT gr
                    FROM GroupPhotoAlbumGeneralBundle:Group gr
                    ORDER BY '.$sortField.' '.$dir
            );
            $query->setFirstResult($start);
            $query->setMaxResults($limit);

            $groupList = $query->getResult();

            $groupVOList = array();
            foreach ($groupList as $group) {
                $groupVOList[] = array(
                    'id' => $group->getId(),
                    'name' => $group->getName(),
                    'description' => $group->getDescription(),
                    'year' => $group->getCreationYear()
                );
            }

            $result = array(
                "data" => $groupVOList,
                "totalCount" => $groupCount
            );
        
        } catch(\Exception $exc) {
            GPAUtils::logError('GroupController.listAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        
        return GPAUtils::objToJsonResponse($result);
    }
    
    /**
     * @Route("/save", name="group_save", options={"expose"=true})
     * @Method({"POST"})
     */
    public function saveAction(Request $request)
    {
        try{
            $id = $request->request->get('id');
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $year = $request->request->get('year');
            
            if (trim($name) == '') {
                return GPAUtils::jsonResponse('ERROR', 'Nume gol!');
            }
            
            if (GPAUtils::isValidId($id)) {
                $group = $this->getDoctrine()
                    ->getRepository('GroupPhotoAlbumGeneralBundle:Group')
                    ->find($id);
                if ($group == null) {
                    $group = new Group();
                }
            } else {
                $group = new Group();
            }
            
            $group->setName(trim($name));
            $group->setDescription(trim($description));
            if (is_numeric($year)) {
                $group->setCreationYear($year);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
            
        } catch(\Exception $exc) {
            GPAUtils::logError('GroupController.saveAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        return GPAUtils::jsonResponse('SUCCESS', '');
    }
    
    /**
     * @Route("/delete", name="group_delete", options={"expose"=true})
     * @Method({"POST"})
     */
    public function deleteAction(Request $request)
    {
        try{
            $id = $request->request->get('id');
            
            if (GPAUtils::isValidId($id)) {
                $group = $this->getDoctrine()
                    ->getRepository('GroupPhotoAlbumGeneralBundle:Group')
                    ->find($id);
                
                if ($group != null) {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($group);
                    $em->flush();
                }
            }
            if (!isset($group) || $group == null) {
                GPAUtils::logError('GroupController.deleteAction error : invalid id : '.$id);
                return GPAUtils::jsonResponse('ERROR', 'Id-ul este invalid!');
            }
        } catch(\Exception $exc) {
            GPAUtils::logError('GroupController.deleteAction error', $exc);
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
        return GPAUtils::jsonResponse('SUCCESS', '');
    }
}

<?php

namespace GroupPhotoAlbum\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GroupPhotoAlbum\GeneralBundle\Entity\User;
use GroupPhotoAlbum\GeneralBundle\Entity\Role;
use GroupPhotoAlbum\GeneralBundle\Entity\Group;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use GroupPhotoAlbum\GeneralBundle\Utils\GPAUtils;

class UserController extends Controller
{
    /**
     * @Route("/index", name="index")
     */
    public function indexAction()
    {
        return $this->render('GroupPhotoAlbumGeneralBundle:Welcome:index.html.twig');
    }
    
    /**
     * @Route("/list", name="user_list", options={"expose"=true})
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
                case 'secondName' :
                    $sortField = 'user.secondName';
                    break;
                case 'firstName' :
                    $sortField = 'user.name';
                    break;
                case 'login' :
                    $sortField = 'user.login';
                    break;
                case 'role' :
                    $sortField = 'role.name';
                    break;
                case 'email' :
                    $sortField = 'user.email';
                    break;
                case 'phone' :
                    $sortField = 'user.phone';
                    break;
                case 'birthDate' :
                    $sortField = 'user.birthDate';
                    break;
            }       

            $em = $this->getDoctrine()->getManager();
            $countQuery = $em->createQuery(
                'SELECT COUNT(user)
                    FROM GroupPhotoAlbumGeneralBundle:User user
                    JOIN user.role role
                    WHERE role.name != :rootCode'
            );
            $countQuery->setParameter('rootCode', 'ROOT');

            $userCount = $countQuery->getSingleScalarResult();          

            $query = $em->createQuery(
                'SELECT user
                    FROM GroupPhotoAlbumGeneralBundle:User user
                    JOIN user.role role
                    WHERE role.name != :rootCode
                    ORDER BY '.$sortField.' '.$dir
            );
            $query->setParameter('rootCode', 'ROOT');
            $query->setFirstResult($start);
            $query->setMaxResults($limit);

            $userList = $query->getResult();

            $userVOList = array();
            foreach ($userList as $user) {
                $groupList = $user->getGroups();
                $groupVOList = array();
                foreach ($groupList as $group) {
                    $groupVOList[] = array(
                        'id' => $group->getId(),
                        'name' => $group->getName()
                    );
                }
                $userVOList[] = array(
                    'id' => $user->getId(),
                    'firstName' => $user->getName(),
                    'secondName' => $user->getSecondName(),
                    'login' => $user->getLogin(),
                    'password' => $user->getPassword(),
                    'email' => $user->getEmail(),
                    'phone' => $user->getPhone(),
                    'birthDate' => ($user->getBirthDate() != null) ? $user->getBirthDate()->format('d/m/Y') : '',
                    'role' => $user->getRole()->getWording(),
                    'roleId' => $user->getRole()->getId(),
                    'groupList' => json_encode($groupVOList)
                );
            }

            $result = array(
                "data" => $userVOList,
                "totalCount" => $userCount
            );
        
        } catch(\Exception $exc) {
            $result = array(
                "result" => 'ERROR'
            );
        }
        
        $response = new JsonResponse();
        $response->setData($result);
        return $response;
    }
    
    /**
     * @Route("/roleList", name="role_list", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function roleListAction()
    {
        try{
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT role
                    FROM GroupPhotoAlbumGeneralBundle:Role role
                    WHERE role.name != :rootCode
                    ORDER BY role.wording ASC'
            );
            $query->setParameter('rootCode', 'ROOT');

            $roleList = $query->getResult();

            $comboVOList = array();
            foreach ($roleList as $role) {
                $comboVOList[] = array(
                    'id' => $role->getId(),
                    'label' => $role->getWording()
                );
            }

            $result = array(
                "data" => $comboVOList
            );
        } catch(\Exception $exc) {
            $result = array(
                "result" => 'ERROR'
            );
        }
        
        $response = new JsonResponse();
        $response->setData($result);
        return $response;
    }
    
    /**
     * @Route("/groupList", name="group_list", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function groupListAction()
    {
        try{
            $repository = $this->getDoctrine()->getRepository('GroupPhotoAlbumGeneralBundle:Group');

            $query = $repository->createQueryBuilder('gr')
                ->orderBy('gr.name', 'ASC')
                ->getQuery();

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
                "data" => $groupVOList
            );
        } catch(\Exception $exc) {
            $result = array(
                "result" => 'ERROR'
            );
        }
        
        $response = new JsonResponse();
        $response->setData($result);
        return $response;
    }
    
    /**
     * @Route("/save", name="user_save", options={"expose"=true})
     * @Method({"POST"})
     */
    public function saveAction(Request $request)
    {
        try{
            $id = $request->request->get('id');
            $firstName = $request->request->get('firstName');
            $secondName = $request->request->get('secondName');
            $login = $request->request->get('login');
            $password = $request->request->get('password');
            $roleId = $request->request->get('roleId');
            $email = $request->request->get('email');
            $phone = $request->request->get('phone');
            $birthDate = $request->request->get('birthDate');
            $groupIds = $request->request->get('groupIds');
            
            if (trim($secondName) == '') {
                return GPAUtils::jsonResponse('ERROR', 'Nume gol!');
            }
            if (trim($login) == '') {
                return GPAUtils::jsonResponse('ERROR', 'Login gol!');
            }
            if (trim($password) == '') {
                return GPAUtils::jsonResponse('ERROR', 'Parola goala!');
            }
            if (!GPAUtils::isValidId($roleId)) {
                return GPAUtils::jsonResponse('ERROR', 'Invalid id rol!');
            }
            if (trim($email) == '') {
                return GPAUtils::jsonResponse('ERROR', 'Email gol!');
            }
            
            if (GPAUtils::isValidId($id)) {
                $user = $this->getDoctrine()
                    ->getRepository('GroupPhotoAlbumGeneralBundle:User')
                    ->find($id);
                if ($user == null) {
                    $user = new User();
                }
            } else {
                $user = new User();
            }
            
            $user->setName(htmlspecialchars(trim($firstName), ENT_QUOTES));
            $user->setSecondName(htmlspecialchars(trim($secondName), ENT_QUOTES));
            $user->setLogin(htmlspecialchars(trim($login), ENT_QUOTES));
            $user->setPassword(htmlspecialchars(trim($password), ENT_QUOTES));
            $user->setEmail(htmlspecialchars(trim($email), ENT_QUOTES));
            $user->setPhone(htmlspecialchars(trim($phone), ENT_QUOTES)); 
            if ($birthDate != '') {
                try {
                    $user->setBirthDate(\DateTime::createFromFormat('d/m/Y', $birthDate));
                } catch(\Exception $exc) {
                    return GPAUtils::jsonResponse('ERROR', 'Data nasterii invalida!');
                }
            }

            //set role
            $role = $this->getDoctrine()
                ->getRepository('GroupPhotoAlbumGeneralBundle:Role')
                ->find($roleId);
            $user->setRole($role);
            
            //set groups
            foreach($user->getGroups() as $group) {
                $group->removeUser($user);
            }
            $groupIdsArray = explode(",", $groupIds);
            foreach ($groupIdsArray as $groupId) {
                if (!GPAUtils::isValidId($groupId)) {
                    return GPAUtils::jsonResponse('ERROR', 'Invalid id grup!');
                }
                $group = $this->getDoctrine()
                    ->getRepository('GroupPhotoAlbumGeneralBundle:Group')
                    ->find($groupId);
                if ($group != null) {
                    $group->addUser($user);
                    $user->addGroup($group);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return GPAUtils::jsonResponse('SUCCESS', '');
        } catch(\Exception $exc) {
            return GPAUtils::jsonResponse('ERROR', 'S-a produs o eroare tehnica!');
        }
    }
    
    /**
     * @Route("/delete", name="user_delete", options={"expose"=true})
     * @Method({"POST"})
     */
    public function deleteAction(Request $request)
    {
        try{
            $id = $request->request->get('id');
            
            if (GPAUtils::isValidId($id)) {
                $user = $this->getDoctrine()
                    ->getRepository('GroupPhotoAlbumGeneralBundle:User')
                    ->find($id);
                
                if ($user != null) {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($user);
                    $em->flush();

                    $result = array(
                        "result" => 'SUCCESS'
                    );
                }
            }
            if (!isset($user) || $user == null) {
                $result = array(
                    "result" => 'ERROR',
                    "message" => 'Id-ul este invalid!'
                );
            }
        } catch(\Exception $exc) {
            $result = array(
                "result" => 'ERROR',
                "message" => 'S-a produs o eroare tehnica!'
            );
        }
        
        $response = new JsonResponse();
        $response->setData($result);
        return $response;
    }
}

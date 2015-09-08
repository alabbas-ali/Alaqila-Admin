<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserreqController
 *
 * @author abass
 */

namespace Userreq\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Userreq\Model\Userreq;
use Notification\Model\Notification;
use Userreq\Form\UserreqForm;

use ZfcUserOver\Model\User;
use ZfcUserOver\Service\User as UserService;
use ZfcUser\Options\UserControllerOptionsInterface;
use ZfcUserOver\Model\RoleAssignment;

class UserreqController extends AbstractActionController
{

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var UserControllerOptionsInterface
     */
    protected $options;

    protected $entityManager;

    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->entityManager;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function getAllAction()
    {
        $user = $this->zfcUserAuthentication()->getIdentity();
        $userreqs = $this->getEntityManager()->getRepository('Userreq\Model\Userreq')->findAll();
        $data = array();
        //$i=0;
        foreach ($userreqs as $userreq) {
            $item = $userreq->getArrayCopy();
            $data[] = $item;
        }
        return new JsonModel(array("data" => $data));
    }



    public function acceptAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }

        $userreq = $this->getEntityManager()->find('Userreq\Model\Userreq', $id);
        if ($userreq) {
            $user = new User();
            $data=array();
            $data['id']='';
            $data['username']=$userreq->username;
            $data['email']=$userreq->email;
            $data['displayName']=$userreq->displayName.' '.$userreq->displayName2;
            $data['photo']=$userreq->photo;
            $data['password']=$this->getUserService()->getnewpass($userreq->password);
            $user->exchangeArray( $data);
            $user->setContent('');
            $user->setArcontent('');
            $user->setSubtitle('');
            $user->setSubartitle('');
            $user->password=$this->getUserService()->getnewpass($user->password);
            $user->isAdmin= false;
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
            $id = $user->getId();

            $roleAssignment = new RoleAssignment();

            $roleAssignment->context = 'country';
            $roleAssignment->instanceid = (int) $userreq->country->id;
            $roleAssignment->userid = $id;
            $this->getEntityManager()->persist($roleAssignment);
            $this->getEntityManager()->flush();

            $roleAssignment2 = new RoleAssignment();

            $roleAssignment2->context = 'page';
            $roleAssignment2->instanceid = (int) $userreq->page_id->id;
            $roleAssignment2->userid = $id;
            $this->getEntityManager()->persist($roleAssignment2);
            $this->getEntityManager()->flush();

            $this->getEntityManager()->remove($userreq);
            $this->getEntityManager()->flush();


            return new JsonModel(array("done" => 'true', 'message' => 'Userreq Has been accepted'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
        }

        $userreq = $this->getEntityManager()->find('Userreq\Model\Userreq', $id);
        if ($userreq) {
            $this->getEntityManager()->remove($userreq);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Userreq Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }

    public function getUserService()
    {
        if (!$this->userService) {
            $this->userService = $this->getServiceLocator()->get('zfcuserover_user_service');
        }
        return $this->userService;
    }

    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
        return $this;
    }


}
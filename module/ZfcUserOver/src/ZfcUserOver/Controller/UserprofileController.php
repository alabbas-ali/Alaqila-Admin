<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsersController
 *
 * @author abass
 */

namespace ZfcUserOver\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use ZfcUserOver\Model\User;          // <-- Add this import
use ZfcUserOver\Form\UserprofileForm;
use ZfcUserOver\Service\User as UserService;
use ZfcUser\Options\UserControllerOptionsInterface;



class UserprofileController extends AbstractActionController {
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var UserControllerOptionsInterface
     */
    protected $options;

    protected $em;

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }
    
    public function indexAction() {
        $id=$this->zfcUserAuthentication()->getIdentity();
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }

        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $id);
        if (!$user) {
            return $this->redirect()->toRoute('home');
        }
        $oldpassword=$user->password;
        $form = new UserprofileForm($this->getEntityManager());
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilterForProfile());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                if($user->password==''){
                    $user->password=$oldpassword;
                }else{
                    $user->password=$this->getUserService()->getnewpass($user->password);
                }
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Userprofile',array('action'=>'done'));
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }
    public function doneAction() {
        
    }

    public function getAllAction() {
        $users = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')->findAll();
        $data = array();
        foreach($users as $user) {
            $data[] = $user->getArrayCopy();
        }
        return new JsonModel(array("data"=>$data));
    }
    
    public function getByIDAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $data = array();
        if (!$id){
            return new JsonModel($data);
        }
        $users = $this->getEntityManager()->find('ZfcUserOver\Model\User', $id);
        $data = array();
        $data[] = $users->getArrayCopy();
        return new JsonModel($data);
    }

    /**
     * Getters/setters for DI stuff
     */

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

    /**
     * set options
     *
     * @param UserControllerOptionsInterface $options
     * @return UserController
     */
    public function setOptions(UserControllerOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * get options
     *
     * @return UserControllerOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof UserControllerOptionsInterface) {
            $this->setOptions($this->getServiceLocator()->get('zfcuser_module_options'));
        }
        return $this->options;
    }
   
}

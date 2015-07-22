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
use ZfcUserOver\Form\UserForm;
use ZfcUserOver\Form\SettingsForm;
use ZfcUserOver\Service\User as UserService;
use ZfcUser\Options\UserControllerOptionsInterface;


class UsersController extends AbstractActionController {
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
        return new ViewModel();
    }

    public function getAllAction() {
        $user=$this->zfcUserAuthentication()->getIdentity();
        if($user->isAdmin==2)
            $users = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')->findby(array('isAdmin' => '0'));
        else
            $users = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')->findAll();
        $data = array();
        foreach($users as $user) {
            $data[] = $user->getArrayCopy();
        }
        return new JsonModel(array("data"=>$data));
    }

    public function addAction() {
        $form = new UserForm($this->getEntityManager());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $user->exchangeArray($data);
                $user->password=$this->getUserService()->getnewpass($user->password);
                $user->isAdmin= false;
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Users');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Users', array(
                        'action' => 'add'
            ));
        }

        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $id);
        if (!$user) {
            return $this->redirect()->toRoute('Users', array(
                        'action' => 'index'
            ));
        }
        $oldpassword=$user->password;
        $form = new UserForm($this->getEntityManager());
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilterForEdit());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                
                if($user->password==''){
                    $user->password=$oldpassword;
                }else{
                    $user->password=$this->getUserService()->getnewpass($user->password);
                }
                    
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Users');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }
    public function settingsAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Users', array(
                        'action' => 'add'
            ));
        }

        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $id);
        if (!$user) {
            return $this->redirect()->toRoute('Users', array(
                        'action' => 'index'
            ));
        }
        $form = new SettingsForm($this->getEntityManager());
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilterForSettings());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Users');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
        }
        
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $id);
        if ($user) {
            $this->getEntityManager()->remove($user);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'User Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    public function chtypeAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $id);
        if ($user) {
            if($user->isAdmin==1) $user->isAdmin=0;
            else $user->isAdmin=1;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'User Have been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
    
    public function chtypesvAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $id);
        if ($user) {
            if($user->isAdmin==2) $user->isAdmin=0;
            else $user->isAdmin=2;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'User Have been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
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

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SettingsController
 *
 * @author abass
 */

namespace Settings\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Settings\Form\SettingsForm;

class SettingsController extends AbstractActionController {

    protected $entityManager;

    public function getEntityManager() {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->entityManager;
    }
    
    public function getAllAction() {
        $id = 1;
        $comments = $this->getEntityManager()->find('Settings\Model\Settings', $id);
        $data = array();
        $data[] = $comments->getArrayCopy();
        return new JsonModel($data);
    }

    public function indexAction() {
        $identity=$this->zfcUserAuthentication()->getIdentity();
        if(!$identity->isAdmin)
            return $this->redirect()->toRoute('home');
        $id = 1;
        $settings = $this->getEntityManager()->find('Settings\Model\Settings', $id);
        $form = new SettingsForm($this->getEntityManager());
        $form->bind($settings);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($settings->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Settings',array('action'=>'done'));
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }
    public function doneAction() {
        
    }
}

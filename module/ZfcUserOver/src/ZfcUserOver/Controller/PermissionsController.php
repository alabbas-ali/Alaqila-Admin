<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PermissionsController
 *
 * @author abass
 */

namespace ZfcUserOver\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcUserOver\Model\RoleAssignment;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class PermissionsController extends AbstractActionController {

    //put your code here
    protected $em;

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction() {
        $context = (String) $this->params()->fromRoute('context', 'NO');
        $id = (int) $this->params()->fromRoute('id', 0);

        $userroles = $this->getEntityManager()->getRepository('ZfcUserOver\Model\RoleAssignment')
                ->findby(array('context' => $context, 'instanceid' => $id));
        $m = '(';
        $userrole = array();
        foreach ($userroles as $role) {
            $userrole[] = $role->getArrayCopy();
            $m = $m . $role->userid . ',';
        }

        if (sizeof($m) > 2) {
            $m = substr($m, 0, -1);
            $m = $m . ')';
            $users = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')
                    ->createQueryBuilder('p')
                    ->where('p.id NOT IN ' . $m);
        } else {
            $users = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')->findAll();
        }
        $us = array();
        foreach ($users as $user) {
            $us[] = $user->getArrayCopy();
        }

        $return = new ViewModel();
        $return->setTerminal(true);
        $return->setVariables(array('users' => $us, 'userroles' => $userrole , 'id'=> $id, 'context'=> $context));
        return $return;
    }

    public function addAction() {
        $context = (String) $this->params()->fromRoute('context', 'NO');
        $id = (int) $this->params()->fromRoute('id', 0);
        $userId = (int) $this->params()->fromRoute('userid', 0);

        $roleAssignment = new RoleAssignment();

        $roleAssignment->context = $context;
        $roleAssignment->instanceid = $id;
        $roleAssignment->userid = $userId;

        $this->getEntityManager()->persist($roleAssignment);
        $this->getEntityManager()->flush();

        return new JsonModel(array("done" => 'Done', 'message' => 'User Role Added Success'));
    }

    public function deleteAction() {
        $context = (String) $this->params()->fromRoute('context', 'NO');
        $id = (int) $this->params()->fromRoute('id', 0);
        $userId = (int) $this->params()->fromRoute('userid', 0);

        $roleAssignment = $this->getEntityManager()->getRepository('ZfcUserOver\Model\RoleAssignment')
                ->findOneBy(array('context' => $context, 'instanceid' => $id, 'userid' => $userId));

        $this->getEntityManager()->remove($roleAssignment);
        $this->getEntityManager()->flush();

        return new JsonModel(array("done" => 'Done', 'message' => 'User Role Deleted Success'));
    }

}

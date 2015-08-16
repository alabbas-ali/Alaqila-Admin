<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageController
 *
 * @author abass
 */

namespace Pages\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Pages\Model\Page;
use Notification\Model\Notification;
use Pages\Form\PageForm;
use Doctrine\ORM\EntityManager;

class PageController extends AbstractActionController {

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
        $pages = $this->getEntityManager()->getRepository('Pages\Model\Page')->findAll();
        $data = array();
        //$i=0;
        foreach ($pages as $page) {
            $data[] = $page->getArrayCopy();
        }
        //var_dump($data);die();
        return new JsonModel(array("data" => $data));
    }

 public function orderAction() {

		   // id:3
		//fromPosition:3
		//toPosition:2
		//direction:back
		//direction:forward
		//group:
	     
	$request = $this->getRequest();
	if ($request->isPost()) {
		
           $data = $request->getPost();
           if( $data['direction'] == 'forward' ){
           	$q = $this->getEntityManager()->createQueryBuilder()->update('Pages\Model\Page' ,'u')
		      ->set('u.ord', 'u.ord - 1')->where('u.ord >'. $data['fromPosition']  .' and u.ord <= '. $data['toPosition']  )->getQuery()->execute();
		$q = $this->getEntityManager()->createQueryBuilder()->update('Pages\Model\Page' ,'u')
		      ->set('u.ord', $data['toPosition']  )->where('u.id ='. $data['id'] )->getQuery()->execute();
		}
		
	if( $data['direction'] == 'back' ){
           	$q = $this->getEntityManager()->createQueryBuilder()->update('Pages\Model\Page' ,'u')
		      ->set('u.ord', 'u.ord + 1')->where('u.ord <'. $data['fromPosition']  .' and u.ord >= '. $data['toPosition']  )->getQuery()->execute();
		$q = $this->getEntityManager()->createQueryBuilder()->update('Pages\Model\Page' ,'u')
		      ->set('u.ord', $data['toPosition']  )->where('u.id ='. $data['id'] )->getQuery()->execute();
		}
	}
	
	return new JsonModel();
    }
    

    public function getAllActiveAction() {
        $pages = $this->getEntityManager()->getRepository('Pages\Model\Page')
                ->findby(array('active' => 1), array('ord' => 'ASC'));
        $data = array();
        //$i=0;
        foreach ($pages as $page) {
            $data[] = $page->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getPageUsersAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
        $repository = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User');
        $querybuilder = $repository->createQueryBuilder('c');
        $querybuilder->select('c')
                ->leftJoin(
                        'ZfcUserOver\Model\RoleAssignment',
                        'r',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        "r.userid = c.id"
                )
                ->where("r.instanceid = $id AND r.context='page'")
                ->orderBy('c.username', 'DESC');
        $users=$querybuilder->getQuery()->getResult();
        //$page = $this->getEntityManager()->find('Pages\Model\Page', $id);
        $data = array();
        foreach($users as $user){
            if(!$user->isAdmin)
            $data[] = $user->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function addAction() {
        $form = new PageForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $page = new Page();
            $form->setInputFilter($page->getInputFilter());
            $form->setData($request->getPost());
               
            
            if ($form->isValid()) {
                $page->exchangeArray($form->getData());
                $page->active=1;
               
                $highest_order = $this->getEntityManager()->createQueryBuilder()->select('MAX(e.ord)')->from('Pages\Model\Page', 'e')
                			->getQuery()->getSingleScalarResult();
                $page->ord = $highest_order + 1;
                $this->getEntityManager()->persist($page);
                $this->getEntityManager()->flush();
                $id=$page->getId();
                $notification = new Notification();
                $notData=array('type'=>'page','type_id'=>$id,'user_type'=>'1');
                $notification->exchangeArray($notData);
                $this->getEntityManager()->persist($notification);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Pages');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Pages', array(
                        'action' => 'add'
            ));
        }

        $page = $this->getEntityManager()->find('Pages\Model\Page', $id);
        if (!$page) {
            return $this->redirect()->toRoute('Pages', array(
                        'action' => 'index'
            ));
        }

        $form = new PageForm();
        $form->bind($page);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($page->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Pages');
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
        
        $page = $this->getEntityManager()->find('Pages\Model\Page', $id);
        if ($page) {
            $this->getEntityManager()->remove($page);
            $this->getEntityManager()->flush();
            $q = $this->getEntityManager()->createQueryBuilder()->update('Pages\Model\Page' ,'u')
		      ->set('u.ord', 'u.ord - 1')->where('u.id >'. $id )->getQuery()->execute();
            return new JsonModel(array("done" => 'true', 'message' => 'Page Has been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $page = $this->getEntityManager()->find('Pages\Model\Page', $id);
        if ($page) {
            $page->active=!$page->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Page Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }

}
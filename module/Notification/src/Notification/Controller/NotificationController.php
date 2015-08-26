<?php
//test
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NotificationController
 *
 * @author abass
 */

namespace Notification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Notification\Model\Notification;
use Notification\Form\NotificationForm;

class NotificationController extends AbstractActionController {

    protected $entityManager;

    public function getEntityManager() {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->entityManager;
    }

    public function indexAction() {
        return new ViewModel();
    }

    public function getAllNotificationAction(){
        $user=$this->zfcUserAuthentication()->getIdentity();
        if (!$user->isAdmin) {
            $notifications = $this->getEntityManager()->getRepository('Notification\Model\Notification')->findby(array('user_id' => $user) , array('id' => 'DESC'));
            //var_dump($notifications);die;
        } else {
            //$notifications = $this->getEntityManager()->getRepository('Notification\Model\Notification')->findby(array('user_type' => '1') , array('id' => 'DESC'));
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('n.id','n.type','n.type_id','n.notification_date','n.user_type','n.user_id','n.message')
                ->from('Notification\Model\Notification', 'n')
                ->where("n.user_type='1'")
                ->orWhere("n.user_id='".$user->id."'");

            $query = $qb->getQuery();
            $notifications=$qb->getQuery()->getResult();
            //var_dump($olddata);die;
            
        }
        
        $data = array();
        //$i=0;
        foreach ($notifications as $notif) {
            if(is_object($notif))
                $notif=$notif->getArrayCopy();
                
            switch ($notif['type']){
                case 'news':
                    $notif['style']='fa-newspaper-o text-aqua';
                    
                    break;
                case 'video':
                    $notif['style']='fa-video-camera text-red';
                    break;
                case 'audio':
                    $notif['style']='fa-microphone text-black';
                    break;
                case 'photo':
                    $notif['style']='fa-camera text-orange';
                    break;
                case 'comment':
                    $notif['style']='fa-comment text-green';
                    break;
                case 'page':
                    $notif['style']='fa-file-text text-yellow';
                    break;
                default :
                    $notif['style']='fa-users text-aqua';
                    break;
            }
            $data[] = $notif;
        }
        return new JsonModel(array("num" => count($data),"data" => $data));
    }
    
    public function goAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $notification = $this->getEntityManager()->find('Notification\Model\Notification', $id);
        $this->getEntityManager()->remove($notification);
        $this->getEntityManager()->flush();
        switch ($notification->type){
                case 'news':
                    $url=$this->redirect()->toRoute('News',array('action'=>'edit','id'=>$notification->type_id));
                    break;
                case 'video':
                    $url=$this->redirect()->toRoute('Video',array('action'=>'edit','id'=>$notification->type_id));
                    break;
                case 'audio':
                    $url=$this->redirect()->toRoute('Audio',array('action'=>'edit','id'=>$notification->type_id));
                    break;
                case 'photo':
                    $url=$this->redirect()->toRoute('Photo',array('action'=>'edit','id'=>$notification->type_id));
                    break;
                case 'comment':
                    $url=$this->redirect()->toRoute('Comment',array('action'=>'edit','id'=>$notification->type_id));
                    break;
                default :
                    $url=$this->redirect()->toRoute('home');
                    break;
            }
        return $url;
    }

    public function getAllAction() {
        $user=$this->zfcUserAuthentication()->getIdentity();
        if (!$user->isAdmin) {
            $notifications = $this->getEntityManager()->getRepository('Notification\Model\Notification')->findby(array('user_id' => $user) , array('id' => 'DESC'));
            //var_dump($notifications);die;
        } else {
            //$notifications = $this->getEntityManager()->getRepository('Notification\Model\Notification')->findby(array('user_type' => '1') , array('id' => 'DESC'));
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('n.id','n.type','n.type_id','n.notification_date','n.user_type','n.user_id')
                ->from('Notification\Model\Notification', 'n')
                ->where("n.user_type='1'")
                ->orWhere("n.user_id='".$user->id."'");

            $query = $qb->getQuery();
            $notifications=$qb->getQuery()->getResult();
            //var_dump($olddata);die;
            
        }
        $data = array();
        //$i=0;
        foreach ($notifications as $notif) {
            if(is_object($notif))
                $notif=$notif->getArrayCopy();
            $data[] = $notif;
        }
        return new JsonModel(array("data" => $data));
    }
    
    public function notificationsAction(){
        $type = $this->params()->fromRoute('type', 0);
        $id = (int) $this->params()->fromRoute('id', 0);
        return new ViewModel(array('type' => $type,'id' => $id));
    }
    public function getNotificationsAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
        $type = $this->params()->fromRoute('type', 0);
        if (!$id) {
            return $this->redirect()->toRoute('News');
        }
        $notifications = $this->getEntityManager()->getRepository('Notification\Model\Notification')
                ->findBy(array('type' => $type, 'type_id' => $id) , array('id' => 'DESC'));
        //var_dump($notifications);die;
        $data = array();
        //$i=0;
        foreach ($notifications as $notification) {
            $data[] = $notification->getArrayCopy();
        }
        return new JsonModel(array("data" => $data));
    }
    
    public function getActiveNotificationsAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
        $type = $this->params()->fromRoute('type', 0);
        $notifications = $this->getEntityManager()->getRepository('Notification\Model\Notification')
                ->findBy(array('type' => $type, 'type_id' => $id , 'active' => true) , array('id' => 'DESC'));
        //var_dump($notifications);die;
        $data = array();
        //$i=0;
        foreach ($notifications as $notification) {
            $data[] = $notification->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $notification = new Notification();
            $data = get_object_vars($request->getPost());
            $notification->exchangeArray($data);
            $notification->active=0;
            $this->getEntityManager()->persist($notification);
            $this->getEntityManager()->flush();
            return new JsonModel();
        }
        return new JsonModel();
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Notification', array(
                        'action' => 'add'
            ));
        }

        $notification = $this->getEntityManager()->find('Notification\Model\Notification', $id);
        if (!$notification) {
            return $this->redirect()->toRoute('Notification', array(
                        'action' => 'index'
            ));
        }

        $form = new NotificationForm();
        $form->bind($notification);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($notification->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Notification');
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
        
        $notification = $this->getEntityManager()->find('Notification\Model\Notification', $id);
        if ($notification) {
            $this->getEntityManager()->remove($notification);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Notification Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $notification = $this->getEntityManager()->find('Notification\Model\Notification', $id);
        if ($notification) {
            $notification->active=!$notification->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Notification Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
}
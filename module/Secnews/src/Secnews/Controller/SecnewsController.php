<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SecnewsController
 *
 * @author abass
 */

namespace Secnews\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Secnews\Model\Secnews;
use Notification\Model\Notification;
use Secnews\Form\SecnewsForm;
use Secnews\Form\SecnewsvideoForm;
use Secnews\Form\SecnewsaudioForm;
use Secnews\Form\SecnewsimageForm;
use Sections\Model\Section;
use Visit\Model\Visit;

class SecnewsController extends AbstractActionController {

    protected $entityManager;

    public function getEntityManager() {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->entityManager;
    }

    public function indexAction() {
        $secid = (int) $this->params()->fromRoute('id', 0);
        if(!$secid){
            return $this->redirect()->toRoute('Sections');
        }

        return new ViewModel(array('secid' => $secid));
    }

    public function getAllAction() {
        $user=$this->zfcUserAuthentication()->getIdentity();
        if (!$user->isAdmin) {
            $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')->findby(array('user' => $user) , array('id' => 'DESC'));
        } else {
            $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')->findAll();
        }
        //echo $user->isAdmin;die;
        
        $data = array();
        //$i=0;
        foreach ($secnewss as $secnews) {
            $item=$secnews->getArrayCopy();
            $type='secnews';
            $visits = $this->getEntityManager()->getRepository('Visit\Model\Visit')->findBy(array('type' => $type, 'type_id' => $item['id']));
            $item['visits']=count($visits);
            $data[] = $item;
        }
        return new JsonModel(array("data" => $data));
    }
    public function getAllPagesAction()
    {
        $secid = (int) $this->params()->fromRoute('id', 0);

        $user = $this->zfcUserAuthentication()->getIdentity();

        $draw = isset ($_GET['draw']) ? intval($_GET['draw']) : 0;
        $start = isset ($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset ($_GET['length']) ? intval($_GET['length']) : 10;

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array('table'))
            ->from('Secnews\Model\Secnews', 'table')
        ->where("table.secid='$secid'");

        $columns = array(0 => 'id', 1 => 'title', 2 => 'country', 3 => 'user', 4 => 'news_date');
        $order = '';
        if (isset($_GET['order'])) {
            $orderBy = array();
            for ($i = 0, $ien = count($_GET['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($_GET['order'][$i]['column']);
                if(isset($columns[$columnIdx])){
                    $column = $columns[$columnIdx];
                    $dir = $_GET['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';
                    $qb->orderBy('table.'.$column, $dir);

                }

            }

        }

        if (!$user->isAdmin)
            $qb->where("table.user='$user->id'");


        $all_count = count($qb->getQuery()->getResult());

        $qb->setFirstResult($start);
        $qb->setMaxResults($length);
        //var_dump($qb->getQuery());die;
        $secnewss = $qb->getQuery()->getResult();
        $data = array();
        //$i=0;
        foreach ($secnewss as $secnews) {
            $item = $secnews->getArrayCopy();
            $type = 'secnews';
            $visits = $this->getEntityManager()->getRepository('Visit\Model\Visit')->findBy(array('type' => $type, 'type_id' => $item['id']));
            $item['visits'] = count($visits);
            $data[] = $item;
        }

        $arrayff = [
            "draw" => $draw,
            "recordsTotal" => $all_count,
            "recordsFiltered" => $all_count,
            "data" => $data
        ];
        return new JsonModel($arrayff);
        die;
    }
    
    public function getAllActiveAction() {        
        $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), 16 , 6);
        $data = array();
        foreach ($secnewss as $secnews) {
            $data[] = $secnews->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
     public function getAllActiveNewAction() {
        
        $start = isset ($_GET['start']) ? intval($_GET['start']) : 6;
        $length = isset ($_GET['length']) ? intval($_GET['length']) : 16;
        $userId = isset ($_GET['userId']) ? intval($_GET['userId']) : 0;
        
        if($userId != 0){
            $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userId);
            $secnewsCount = $this->getEntityManager()->createQueryBuilder()->select('count(u)')
                ->from('Secnews\Model\Secnews', 'u')
                ->where ('u.active = :active AND u.user = :user')
                ->setParameter('active' , 1)
                ->setParameter('user' , $user)
                ->orderBy('u.id' , 'DESC');
            $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')
                    ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC'), $length , $start); 
            
        }else{
            $secnewsCount = $this->getEntityManager()->createQueryBuilder()->select('count(q)')
                ->from('Secnews\Model\Secnews', 'q')
                ->where ('q.active = 1 ')
                ->orderBy('q.id' , 'DESC');
            $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), $length , $start);
        }
        
        $all_count = $secnewsCount->getQuery()->getSingleScalarResult();
 
        $data = array();
        foreach ($secnewss as $secnews) {
            $data[] = $secnews->getArrayCopy();
        }
        
        $arrayff = [
            "total" => $all_count,
            "data" => $data
        ];
        
        //var_dump($arrayff); die();
        return new JsonModel($arrayff);        
    }
    
    public function getPublicAction() {
        $userId = isset ($_GET['userId']) ? intval($_GET['userId']) : 0;
        if($userId != 0){
            $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userId);
            $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')
                ->findby(array('active' => 1, 'user' => $user), array('id' => 'DESC') , 6 , 0);
        }else{
            $user = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')
                ->findby(array('isPublic' => 1));
             $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')
                ->findby(array('active' => 1, 'user' => $user), array('id' => 'DESC') , 6 , 0);
            
        }
        $data = array();
        foreach ($secnewss as $secnews) {
            $data[] = $secnews->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getHomePublicAction() {
        $user = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')
                ->findby(array('isPublic' => 1));
        $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')
                ->findby(array('active' => 1, 'user' => $user), array('id' => 'DESC') , 6 , 0);
        $data = array();
        foreach ($secnewss as $secnews) {
            $data[] = $secnews->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getResentSecnewsAction(){
        $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')
                ->findby(array('active' => 1), array('id' => 'DESC') , 10 , 0);
        $data = array();
        foreach ($secnewss as $secnews) {
            $data[] = $secnews->getArrayCopy();
        }
        return new JsonModel($data);
        
    }
    
    public function getByUserAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')
                ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC') , 6 , 0);
        $data = array();
        foreach ($secnewss as $secnews) {
            $data[] = $secnews->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getUserPublicAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')
                ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC'), 6 , 0);
        $data = array();
        foreach ($secnewss as $secnews) {
            $data[] = $secnews->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getAllUserActiveAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $secnewss = $this->getEntityManager()->getRepository('Secnews\Model\Secnews')
                ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC'), 16 , 6);
        $data = array();
        foreach ($secnewss as $secnews) {
            $data[] = $secnews->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getByIDAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $visit = new Visit();
        $visit->ip_address=$_SERVER['REMOTE_ADDR'];
        $visit->type='secnews';
        $visit->type_id=$id;
        $visit->visit_date=date('Y-m-d H:i:s');
        $this->getEntityManager()->persist($visit);
        $this->getEntityManager()->flush();
                
        $secnews = $this->getEntityManager()->find('Secnews\Model\Secnews', $id);
        $data = array();
        $data[] = $secnews->getArrayCopy();
        return new JsonModel($data);
    }

    public function addAction() {
        $secid = (int) $this->params()->fromRoute('id', 0);
        if(!$secid){
            return $this->redirect()->toRoute('Sections');
        }
        $section = $this->getEntityManager()->find('Sections\Model\Section', $secid);
        switch($section->type){
            case'audio':
                $form = new SecnewsaudioForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
                break;
            case'video':
                $form = new SecnewsvideoForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
                break;
            case'image':
                $form = new SecnewsimageForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
                break;
            default:
                $form = new SecnewsForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
                break;
        }
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $secnews = new Secnews();
            $form->setInputFilter($secnews->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $userid=$this->zfcUserAuthentication()->getIdentity();
                if(!$userid->isAdmin){
                    $repository = $this->getEntityManager()->getRepository('Countries\Model\Country');
                    $querybuilder = $repository->createQueryBuilder('c');
                    $querybuilder->select('c')
                            ->leftJoin(
                                    'ZfcUserOver\Model\RoleAssignment',
                                    'r',
                                    \Doctrine\ORM\Query\Expr\Join::WITH,
                                    "r.instanceid = c.id"
                            )
                            ->where("r.userid = $userid->id AND r.context='country'");
                    $countries=$querybuilder->getQuery()->getResult();
                    if(count($countries)<=0){
                        return array('form' => $form);
                    }
//var_dump($countries[0]); die();
                }else{
                    $countries = $this->getEntityManager()->getRepository('Countries\Model\Country')->findby(array('id' => '1'));
                }
                $data['country'] = $countries[0];
                /*$data['country'] = $this->getEntityManager()->getRepository('Countries\Model\Country')
                        ->findby(array('id' => $data['country']));
                $data['country'] = $data['country'][0];            */    
                
                
                $data['user'] = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')
                        ->findby(array('id' => $userid));
                $data['user'] = $data['user'][0];
                $secnews->exchangeArray($data);
                $secnews->secid=$secid;
                $secnews->active=1;

                
                $this->getEntityManager()->persist($secnews);
                $this->getEntityManager()->flush();
                $id=$secnews->getId();
                /*$notification = new Notification();
                $message='قام '.$data['user']->displayName.' بإضافة خبر جديد';
                $notData=array('type'=>'secnews','type_id'=>$id,'user_type'=>'1','message'=>$message);
                $notification->exchangeArray($notData);
                $this->getEntityManager()->persist($notification);
                $this->getEntityManager()->flush();*/
                
                // Redirect to list of albums
                return $this->redirect()->toRoute('Secnews',array('id'=>$secid));
            }
        }
        return array('form' => $form,'secid' => $secid);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Secnews', array(
                        'action' => 'add'
            ));
        }

        $secnews = $this->getEntityManager()->find('Secnews\Model\Secnews', $id);
        //echo '<pre>';print_r($secnews);die;
        $secnews->country=$secnews->country->id;
        $secnews->user=$secnews->user->id;
        
        if (!$secnews) {
            return $this->redirect()->toRoute('Section', array(
                        'action' => 'index'
            ));
        }

        $section = $this->getEntityManager()->find('Sections\Model\Section',$secnews->secid);
        switch($section->type){
            case'audio':
                $form = new SecnewsaudioForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
                break;
            case'video':
                $form = new SecnewsvideoForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
                break;
            case'image':
                $form = new SecnewsimageForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
                break;
            default:
                $form = new SecnewsForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
                break;
        }
        //$form = new SecnewsForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
        $form->bind($secnews);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($secnews->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $secnews->country=$this->getEntityManager()->find('Countries\Model\Country', $secnews->country);
                $secnews->user=$this->getEntityManager()->find('ZfcUserOver\Model\User', $secnews->user);
                //echo '<pre>';print_r($secnews->country);die;
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Secnews',array('id'=>$secnews->secid));
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
        
        $secnews = $this->getEntityManager()->find('Secnews\Model\Secnews', $id);
        if ($secnews) {
            $this->getEntityManager()->remove($secnews);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Secnews Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $secnews = $this->getEntityManager()->find('Secnews\Model\Secnews', $id);
        if ($secnews) {
            $secnews->active=!$secnews->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Secnews Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
}
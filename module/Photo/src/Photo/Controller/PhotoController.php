<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhotoController
 *
 * @author abass
 */

namespace Photo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Photo\Model\Photo;
use Notification\Model\Notification;
use Photo\Form\PhotoForm;
use Visit\Model\Visit;

class PhotoController extends AbstractActionController {

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

    public function getAllAction() {
        $user=$this->zfcUserAuthentication()->getIdentity();
        if(!$user->isAdmin)
            $photos = $this->getEntityManager()->getRepository('Photo\Model\Photo')->findby(array('user' => $user) , array('id' => 'DESC'));
        else 
            $photos = $this->getEntityManager()->getRepository('Photo\Model\Photo')->findAll();
        $data = array();
        //$i=0;
        foreach ($photos as $photo) {
            $item=$photo->getArrayCopy();
            $type='photo';
            $visits = $this->getEntityManager()->getRepository('Visit\Model\Visit')->findBy(array('type' => $type, 'type_id' => $item['id']));
            $item['visits']=count($visits);
            $data[] = $item;
        }
        return new JsonModel(array("data" => $data));
    }
    public function getAllPagesAction()
    {
        $user = $this->zfcUserAuthentication()->getIdentity();

        $draw = isset ($_GET['draw']) ? intval($_GET['draw']) : 0;
        $start = isset ($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset ($_GET['length']) ? intval($_GET['length']) : 10;

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array('table'))
            ->from('Photo\Model\Photo', 'table');

        $columns = array(0 => 'id', 1 => 'title', 2 => 'country', 3 => 'user', 4 => 'photo_date');
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
        $photos = $qb->getQuery()->getResult();
        $data = array();
        //$i=0;
        foreach ($photos as $photo) {
            $item = $photo->getArrayCopy();
            $type = 'photo';
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
        $photos = $this->getEntityManager()->getRepository('Photo\Model\Photo')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), 16 ,6 );
        $data = array();
        foreach ($photos as $photo) {
            $data[] = $photo->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getAllActiveNewAction() {
        
        $start = isset ($_GET['start']) ? intval($_GET['start']) : 6;
        $length = isset ($_GET['length']) ? intval($_GET['length']) : 16;
        $userId = isset ($_GET['userId']) ? intval($_GET['userId']) : 0;
        
        if($userId != 0){
            $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userId);
            $photosCount = $this->getEntityManager()->createQueryBuilder()->select('count(u)')
                ->from('Photo\Model\Photo', 'u')
                ->where ('u.active = :active AND u.user = :user')
                ->setParameter('active' , 1)
                ->setParameter('user' , $user)
                ->orderBy('u.id' , 'DESC');
            $photos = $this->getEntityManager()->getRepository('Photo\Model\Photo')
                    ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC'), $length , $start); 
            
        }else{
            $photosCount = $this->getEntityManager()->createQueryBuilder()->select('count(q)')
                ->from('Photo\Model\Photo', 'q')
                ->where ('q.active = 1 ')
                ->orderBy('q.id' , 'DESC');
            $photos = $this->getEntityManager()->getRepository('Photo\Model\Photo')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), $length , $start);
        }

        $all_count = $photosCount->getQuery()->getSingleScalarResult();

        $data = array();
        foreach ($photos as $photo) {
            $data[] = $photo->getArrayCopy();
        }
        
        $arrayff = [
            "total" => $all_count,
            "data" => $data
        ];
        
        return new JsonModel($arrayff);        
    }

    public function getPublicAction() {
//        $num = (int) $this->params()->fromRoute('id', 0);
//        $qb = $this->getEntityManager()->createQueryBuilder();
//
//        $qb->select('t.id','COUNT(v.id) visits')
//                ->from('Photo\Model\Photo', 't')
//                ->leftJoin('Visit\Model\Visit', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,"v.type_id=t.id")
//                ->where("v.type='photo'")
//                ->groupBy('t.id')
//                ->orderBy('visits', 'DESC')
//                ->setMaxResults( $num );
//
//        $query = $qb->getQuery();
//        //var_dump($query);die;
//        $result=$qb->getQuery()->getResult();
//        $data = array();
//        foreach ($result as $row) {
//            $photo = $this->getEntityManager()->find('Photo\Model\Photo', $row['id']);
//            $data[] = $photo->getArrayCopy();
//            
//        }
//        return new JsonModel($data);
        $photos = $this->getEntityManager()->getRepository('Photo\Model\Photo')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), 6 ,0 );
        $data = array();
        foreach ($photos as $photo) {
            $data[] = $photo->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
     public function getHomePublicAction() {
//        $num = (int) $this->params()->fromRoute('id', 0);
//        $qb = $this->getEntityManager()->createQueryBuilder();
//
//        $qb->select('t.id','COUNT(v.id) visits')
//                ->from('Photo\Model\Photo', 't')
//                ->leftJoin('Visit\Model\Visit', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,"v.type_id=t.id")
//                ->where("v.type='photo'")
//                ->groupBy('t.id')
//                ->orderBy('visits', 'DESC')
//                ->setMaxResults( $num );
//
//        $query = $qb->getQuery();
//        //var_dump($query);die;
//        $result=$qb->getQuery()->getResult();
//        $data = array();
//        foreach ($result as $row) {
//            $photo = $this->getEntityManager()->find('Photo\Model\Photo', $row['id']);
//            $data[] = $photo->getArrayCopy();
//            
//        }
//        return new JsonModel($data);
         $photos = $this->getEntityManager()->getRepository('Photo\Model\Photo')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), 3 ,0 );
        $data = array();
        foreach ($photos as $photo) {
            $data[] = $photo->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getByIDAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $visit = new Visit();
        $visit->ip_address=$_SERVER['REMOTE_ADDR'];
        $visit->type='photo';
        $visit->type_id=$id;
        $visit->visit_date=date('Y-m-d H:i:s');
        $this->getEntityManager()->persist($visit);
        $this->getEntityManager()->flush();
                
        $photo = $this->getEntityManager()->find('Photo\Model\Photo', $id);
        $data = array();
        $data[] = $photo->getArrayCopy();
        return new JsonModel($data);
    }
    
    public function getByUserAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $photos = $this->getEntityManager()->getRepository('Photo\Model\Photo')
                ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC') , 3 ,0);
        $data = array();
        foreach ($photos as $photo) {
            $data[] = $photo->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getUserPublicAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $photos = $this->getEntityManager()->getRepository('Photo\Model\Photo')
                ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC'), 6 , 0);
        $data = array();
        foreach ($photos as $photo) {
            $data[] = $photo->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getAllUserActiveAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $photos = $this->getEntityManager()->getRepository('Photo\Model\Photo')
                ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC'), 16 , 6);
        $data = array();
        foreach ($photos as $photo) {
            $data[] = $photo->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function addAction() {
        $form = new PhotoForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $photo = new Photo();
            $form->setInputFilter($photo->getInputFilter());
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
                $photo->exchangeArray($data);
                $photo->active=1;
                
                $this->getEntityManager()->persist($photo);
                $this->getEntityManager()->flush();
                $id=$photo->getId();
                $notification = new Notification();
                $message='قام '.$data['user']->displayName.' بإضافة صورة جديدة';
                $notData=array('type'=>'photo','type_id'=>$id,'user_type'=>'1','message'=>$message);
                $notification->exchangeArray($notData);
                $this->getEntityManager()->persist($notification);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Photo');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Photo', array(
                        'action' => 'add'
            ));
        }

        $photo = $this->getEntityManager()->find('Photo\Model\Photo', $id);
        //echo '<pre>';print_r($photo);die;
        $photo->country=$photo->country->id;
        $photo->user=$photo->user->id;
        
        if (!$photo) {
            return $this->redirect()->toRoute('Photo', array(
                        'action' => 'index'
            ));
        }

        $form = new PhotoForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
        $form->bind($photo);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($photo->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $photo->country=$this->getEntityManager()->find('Countries\Model\Country', $photo->country);
                $photo->user=$this->getEntityManager()->find('ZfcUserOver\Model\User', $photo->user);
                //echo '<pre>';print_r($photo->country);die;
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Photo');
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
        
        $photo = $this->getEntityManager()->find('Photo\Model\Photo', $id);
        if ($photo) {
            $this->getEntityManager()->remove($photo);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Photo Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $photo = $this->getEntityManager()->find('Photo\Model\Photo', $id);
        if ($photo) {
            $photo->active=!$photo->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Photo Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
}
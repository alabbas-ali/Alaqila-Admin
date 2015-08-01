<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NewsController
 *
 * @author abass
 */

namespace News\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use News\Model\News;
use News\Form\NewsForm;
use Visit\Model\Visit;

class NewsController extends AbstractActionController {

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
        if (!$user->isAdmin) {
            $newss = $this->getEntityManager()->getRepository('News\Model\News')->findby(array('user' => $user) , array('id' => 'DESC'));
        } else {
            $newss = $this->getEntityManager()->getRepository('News\Model\News')->findAll();
        }
        //echo $user->isAdmin;die;
        
        $data = array();
        //$i=0;
        foreach ($newss as $news) {
            $item=$news->getArrayCopy();
            $type='news';
            $visits = $this->getEntityManager()->getRepository('Visit\Model\Visit')->findBy(array('type' => $type, 'type_id' => $item['id']));
            $item['visits']=count($visits);
            $data[] = $item;
        }
        return new JsonModel(array("data" => $data));
    }
    
//    public function commentsAction(){
//        $id = (int) $this->params()->fromRoute('id', 0);
//        return new ViewModel(array('id' => $id));
//    }
//    public function getcommentsAction(){
//        $id = (int) $this->params()->fromRoute('id', 0);
//        if (!$id) {
//            return $this->redirect()->toRoute('News');
//        }
//        $comments = $this->getEntityManager()->getRepository('Comment\Model\Comment')->findBy(array('type' => 'news', 'type_id' => $id));
//        //var_dump($comments);die;
//        $data = array();
//        //$i=0;
//        foreach ($comments as $comment) {
//            $data[] = $comment->getArrayCopy();
//        }
//        return new JsonModel(array("data" => $data));
//    }
    
    public function getAllActiveAction() {
        $newss = $this->getEntityManager()->getRepository('News\Model\News')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), 16 , 6);
        $data = array();
        foreach ($newss as $news) {
            $data[] = $news->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getPublicAction() {
//        $num = (int) $this->params()->fromRoute('id', 0);
//        $qb = $this->getEntityManager()->createQueryBuilder();
//
//        $qb->select('t.id','COUNT(v.id) visits')
//                ->from('News\Model\News', 't')
//                ->leftJoin('Visit\Model\Visit', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,"v.type_id=t.id")
//                ->where("v.type='news'")
//                ->groupBy('t.id')
//                ->orderBy('visits', 'DESC')
//                ->setMaxResults( $num );
//
//        $query = $qb->getQuery();
//        //var_dump($query);die;
//        $result=$qb->getQuery()->getResult();
//        $data = array();
//        foreach ($result as $row) {
//            $news = $this->getEntityManager()->find('News\Model\News', $row['id']);
//            $data[] = $news->getArrayCopy();
//            
//        }
//        return new JsonModel($data);
        
        $newss = $this->getEntityManager()->getRepository('News\Model\News')
                ->findby(array('active' => 1), array('id' => 'DESC') , 6 , 0);
        $data = array();
        foreach ($newss as $news) {
            $data[] = $news->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getHomePublicAction() {
//        $num = (int) $this->params()->fromRoute('id', 0);
//        $qb = $this->getEntityManager()->createQueryBuilder();
//
//        $qb->select('t.id','COUNT(v.id) visits')
//                ->from('News\Model\News', 't')
//                ->leftJoin('Visit\Model\Visit', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,"v.type_id=t.id")
//                ->where("v.type='news'")
//                ->groupBy('t.id')
//                ->orderBy('visits', 'DESC')
//                ->setMaxResults( $num );
//
//        $query = $qb->getQuery();
//        //var_dump($query);die;
//        $result=$qb->getQuery()->getResult();
//        $data = array();
//        foreach ($result as $row) {
//            $news = $this->getEntityManager()->find('News\Model\News', $row['id']);
//            $data[] = $news->getArrayCopy();
//            
//        }
//        return new JsonModel($data);
        
        $newss = $this->getEntityManager()->getRepository('News\Model\News')
                ->findby(array('active' => 1), array('id' => 'DESC') , 6 , 0);
        $data = array();
        foreach ($newss as $news) {
            $data[] = $news->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getResentNewsAction(){
        $newss = $this->getEntityManager()->getRepository('News\Model\News')
                ->findby(array('active' => 1), array('id' => 'DESC') , 10 , 0);
        $data = array();
        foreach ($newss as $news) {
            $data[] = $news->getArrayCopy();
        }
        return new JsonModel($data);
        
    }
    
    public function getByUserAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $newss = $this->getEntityManager()->getRepository('News\Model\News')
                ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC') , 6 , 0);
        $data = array();
        foreach ($newss as $news) {
            $data[] = $news->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getUserPublicAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $newss = $this->getEntityManager()->getRepository('News\Model\News')
                ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC'), 6 , 0);
        $data = array();
        foreach ($newss as $news) {
            $data[] = $news->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getAllUserActiveAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $newss = $this->getEntityManager()->getRepository('News\Model\News')
                ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC'), 16 , 6);
        $data = array();
        foreach ($newss as $news) {
            $data[] = $news->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getByIDAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $visit = new Visit();
        $visit->ip_address=$_SERVER['REMOTE_ADDR'];
        $visit->type='news';
        $visit->type_id=$id;
        $visit->visit_date=date('Y-m-d H:i:s');
        $this->getEntityManager()->persist($visit);
        $this->getEntityManager()->flush();
                
        $news = $this->getEntityManager()->find('News\Model\News', $id);
        $data = array();
        $data[] = $news->getArrayCopy();
        return new JsonModel($data);
    }

    public function addAction() {
        $form = new NewsForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $news = new News();
            $form->setInputFilter($news->getInputFilter());
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
                $news->exchangeArray($data);
                $news->active=1;
                
                $this->getEntityManager()->persist($news);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('News');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('News', array(
                        'action' => 'add'
            ));
        }

        $news = $this->getEntityManager()->find('News\Model\News', $id);
        //echo '<pre>';print_r($news);die;
        $news->country=$news->country->id;
        $news->user=$news->user->id;
        
        if (!$news) {
            return $this->redirect()->toRoute('News', array(
                        'action' => 'index'
            ));
        }

        $form = new NewsForm($this->getEntityManager(),$this->zfcUserAuthentication()->getIdentity()->getId());
        $form->bind($news);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($news->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $news->country=$this->getEntityManager()->find('Countries\Model\Country', $news->country);
                $news->user=$this->getEntityManager()->find('ZfcUserOver\Model\User', $news->user);
                //echo '<pre>';print_r($news->country);die;
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('News');
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
        
        $news = $this->getEntityManager()->find('News\Model\News', $id);
        if ($news) {
            $this->getEntityManager()->remove($news);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'News Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $news = $this->getEntityManager()->find('News\Model\News', $id);
        if ($news) {
            $news->active=!$news->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'News Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
}
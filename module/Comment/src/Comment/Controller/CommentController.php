<?php
//test
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CommentController
 *
 * @author abass
 */

namespace Comment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Comment\Model\Comment;
use Notification\Model\Notification;
use Comment\Form\CommentForm;

class CommentController extends AbstractActionController {

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
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('c.id','c.content','c.username','c.type','c.type_id','c.comment_date','c.active')
                ->from('Comment\Model\Comment', 'c')
                ->where("c.type_id in (select news.id from News\Model\News news where news.user='".$user->id."') and c.type='news'")
                ->orWhere("c.type_id in (select audio.id from Audio\Model\Audio audio where audio.user='".$user->id."') and c.type='audio'")
                ->orWhere("c.type_id in (select photo.id from Photo\Model\Photo photo where photo.user='".$user->id."') and c.type='photo'")
                ->orWhere("c.type_id in (select video.id from Video\Model\Video video where video.user='".$user->id."') and c.type='video'");

            $query = $qb->getQuery();
            $olddata=$qb->getQuery()->getResult();
            $data=array();
            foreach($olddata as $row){
                $row['userid']=$user->id;
                $data[]=$row;
            }
        } else {
            $comments = $this->getEntityManager()->getRepository('Comment\Model\Comment')->findAll();
            $data = array();
            //$i=0;
            $typeTableArr=array('news'=>'News\Model\News','video'=>'Video\Model\Video',
                'audio'=>'Audio\Model\Audio','photo'=>'Photo\Model\Photo');
            foreach ($comments as $comment) {
                $item= $comment->getArrayCopy();
                
                if(isset($typeTableArr[$item['type']])){
                    $typerec = $this->getEntityManager()->find($typeTableArr[$item['type']], $item['type_id']);
                    if($typerec){
                        $typeData=$typerec->getArrayCopy();
                        $item['userid']=$typerec->user->id;
                    }
                }
                if(!isset($item['userid'])){
                    $item['userid']=$user->id;
                }
                $data[] =$item;
            }
            
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
        $qbcount = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array('table'))
            ->from('Comment\Model\Comment', 'table');

        $qbcount->select('count(table)')
            ->from('Comment\Model\Comment', 'table');

        $columns = array(0 => 'id', 1 => 'content', 2 => 'username', 3 => 'comment_date', 4 => 'userid', 5 => 'type');
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

        if (!$user->isAdmin){
        //$qb->where("table.user='$user->id'");
            $qb->where("table.type_id in (select news.id from News\Model\News news where news.user='".$user->id."') and table.type='news'");
            $qb->orWhere("table.type_id in (select audio.id from Audio\Model\Audio audio where audio.user='".$user->id."') and table.type='audio'");
            $qb->orWhere("table.type_id in (select photo.id from Photo\Model\Photo photo where photo.user='".$user->id."') and table.type='photo'");
            $qb->orWhere("table.type_id in (select video.id from Video\Model\Video video where video.user='".$user->id."') and table.type='video'");
            
            $qbcount->where("table.type_id in (select news.id from News\Model\News news where news.user='".$user->id."') and table.type='news'");
            $qbcount->orWhere("table.type_id in (select audio.id from Audio\Model\Audio audio where audio.user='".$user->id."') and table.type='audio'");
            $qbcount->orWhere("table.type_id in (select photo.id from Photo\Model\Photo photo where photo.user='".$user->id."') and table.type='photo'");
            $qbcount->orWhere("table.type_id in (select video.id from Video\Model\Video video where video.user='".$user->id."') and table.type='video'");
            
        }
        $all_count = $qbcount->getQuery()->getSingleScalarResult();
        $qb->setFirstResult($start);
        $qb->setMaxResults($length);
        $comments = $qb->getQuery()->getResult();
        $data = array();
        if (!$user->isAdmin){
            foreach ($comments as $comment) {
                $item = $comment->getArrayCopy();
                $item['userid']=$user->id;
                $data[] = $item;
            }
        }else{
            $typeTableArr=array('news'=>'News\Model\News','video'=>'Video\Model\Video',
                'audio'=>'Audio\Model\Audio','photo'=>'Photo\Model\Photo');
            foreach ($comments as $comment) {
                $item= $comment->getArrayCopy();
                
                if(isset($typeTableArr[$item['type']])){
                    $typerec = $this->getEntityManager()->find($typeTableArr[$item['type']], $item['type_id']);
                    if($typerec){
                        $typeData=$typerec->getArrayCopy();
                        $item['userid']=$typerec->user->id;
                    }
                }
                if(!isset($item['userid'])){
                    $item['userid']=$user->id;
                }
                $data[] =$item;
            }
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
    
    public function commentsAction(){
        $type = $this->params()->fromRoute('type', 0);
        $id = (int) $this->params()->fromRoute('id', 0);
        return new ViewModel(array('type' => $type,'id' => $id));
    }
    
    public function getCommentsAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
        $type = $this->params()->fromRoute('type', 0);
        if (!$id) {
            return $this->redirect()->toRoute('News');
        }
        $comments = $this->getEntityManager()->getRepository('Comment\Model\Comment')
                ->findBy(array('type' => $type, 'type_id' => $id) , array('id' => 'DESC'));
        //var_dump($comments);die;
        $data = array();
        //$i=0;
        foreach ($comments as $comment) {
            $data[] = $comment->getArrayCopy();
        }
        return new JsonModel(array("data" => $data));
    }
    
    public function getActiveCommentsAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
        $type = $this->params()->fromRoute('type', '');
        
                
        $start = isset ($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset ($_GET['length']) ? intval($_GET['length']) : 10;
        
        //var_dump($start);        var_dump($lenght);        die();
        
        $commentsCount = $this->getEntityManager()->createQueryBuilder()->select('count(q)')
            ->from('Comment\Model\Comment', 'q')
            ->where ('q.type = :type AND q.type_id = :type_id')
            ->setParameter('type' , $type)
            ->setParameter('type_id' , $id)
            ->orderBy('q.id' , 'DESC');
        
        $qb = $this->getEntityManager()->createQueryBuilder()->select('q')
            ->from('Comment\Model\Comment', 'q')
            ->where ('q.type = :type AND q.type_id = :type_id')
            ->setParameter('type' , $type)
            ->setParameter('type_id' , $id)
            ->orderBy('q.id' , 'DESC')
            ->setFirstResult($start)
            ->setMaxResults($length);
        
        $all_count = $commentsCount->getQuery()->getSingleScalarResult();
        $comments = $qb->getQuery()->getResult();
        //var_dump($comments);die;
        $data = array();
        //$i=0;
        foreach ($comments as $comment) {
            $data[] = $comment->getArrayCopy();
        }
        
        $arrayff = [
            "total" => $all_count,
            "data" => $data
        ];
        
        //var_dump($arrayff); die();
        return new JsonModel($arrayff);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $comment = new Comment();
            $data = get_object_vars($request->getPost());
            $comment->exchangeArray($data);
            $comment->active=0;
            $this->getEntityManager()->persist($comment);
            $this->getEntityManager()->flush();
            $id=$comment->getId();
            
            $comment = $this->getEntityManager()->find('Comment\Model\Comment', $id);
            $typeTableArr=array('news'=>'News\Model\News','video'=>'Video\Model\Video',
                'audio'=>'Audio\Model\Audio','photo'=>'Photo\Model\Photo');
            if(isset($typeTableArr[$comment->type])){
                $typerec = $this->getEntityManager()->find($typeTableArr[$comment->type], $comment->type_id);
                if($typerec){
                    $typeData=$typerec->getArrayCopy();
                    $userid=$typerec->user->id;
                    $notification = new Notification();
                    $message='قام '.$data['username'].' بإضافة تعليق جديد';
                    $notData=array('type'=>'comment','type_id'=>$id,'user_id'=>$userid,'message'=>$message);
                    $notification->exchangeArray($notData);
                    $this->getEntityManager()->persist($notification);
                    $this->getEntityManager()->flush();
                }
            }
            
            
            return new JsonModel();
        }
        return new JsonModel();
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Comment', array(
                        'action' => 'add'
            ));
        }

        $comment = $this->getEntityManager()->find('Comment\Model\Comment', $id);
        if (!$comment) {
            return $this->redirect()->toRoute('Comment', array(
                        'action' => 'index'
            ));
        }

        $form = new CommentForm();
        $form->bind($comment);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($comment->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Comment');
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
        
        $comment = $this->getEntityManager()->find('Comment\Model\Comment', $id);
        if ($comment) {
            $this->getEntityManager()->remove($comment);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Comment Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $comment = $this->getEntityManager()->find('Comment\Model\Comment', $id);
        if ($comment) {
            $comment->active=!$comment->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Comment Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
}
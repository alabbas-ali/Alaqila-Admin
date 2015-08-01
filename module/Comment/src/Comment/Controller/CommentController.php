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
            $qb->select('c.id','c.title','c.content','c.username','c.type','c.type_id','c.comment_date','c.active')
                ->from('Comment\Model\Comment', 'c')
                ->where("c.type_id in (select news.id from News\Model\News news where news.user='".$user->id."') and c.type='news'")
                ->orWhere("c.type_id in (select audio.id from Audio\Model\Audio audio where audio.user='".$user->id."') and c.type='audio'")
                ->orWhere("c.type_id in (select photo.id from Photo\Model\Photo photo where photo.user='".$user->id."') and c.type='photo'")
                ->orWhere("c.type_id in (select video.id from Video\Model\Video video where video.user='".$user->id."') and c.type='video'")
                ;

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
        $type = $this->params()->fromRoute('type', 0);
        $comments = $this->getEntityManager()->getRepository('Comment\Model\Comment')
                ->findBy(array('type' => $type, 'type_id' => $id , 'active' => true) , array('id' => 'DESC'));
        //var_dump($comments);die;
        $data = array();
        //$i=0;
        foreach ($comments as $comment) {
            $data[] = $comment->getArrayCopy();
        }
        return new JsonModel($data);
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

        $form = new CommentForm($this->getEntityManager());
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

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VideoController
 *
 * @author abass
 */

namespace Video\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Video\Model\Video;
use Notification\Model\Notification;
use Video\Form\VideoForm;
use Visit\Model\Visit;

class VideoController extends AbstractActionController
{

    protected $entityManager;

    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->entityManager;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function getAllAction()
    {
        $user = $this->zfcUserAuthentication()->getIdentity();
        if (!$user->isAdmin) {
            $videos = $this->getEntityManager()->getRepository('Video\Model\Video')->findby(array('user' => $user), array('id' => 'DESC'));
        } else {
            $videos = $this->getEntityManager()->getRepository('Video\Model\Video')->findAll();
        }

        $data = array();
        //$i=0;
        foreach ($videos as $video) {
            $item = $video->getArrayCopy();
            $type = 'video';
            $visits = $this->getEntityManager()->getRepository('Visit\Model\Visit')->findBy(array('type' => $type, 'type_id' => $item['id']));
            $item['visits'] = count($visits);
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
            ->from('Video\Model\Video', 'table');

        $columns = array(0 => 'id', 1 => 'title', 2 => 'country', 3 => 'user', 4 => 'video_date');
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
        $videos = $qb->getQuery()->getResult();
        $data = array();
        //$i=0;
        foreach ($videos as $video) {
            $item = $video->getArrayCopy();
            $type = 'video';
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

    public function getAllActiveAction()
    {
        $videos = $this->getEntityManager()->getRepository('Video\Model\Video')
            ->findBy(array('active' => '1'), array('id' => 'DESC'), 16, 6);
        $data = array();
        foreach ($videos as $video) {
            $data[] = $video->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
     public function getAllActiveNewAction() {
        
        $start = isset ($_GET['start']) ? intval($_GET['start']) : 6;
        $length = isset ($_GET['length']) ? intval($_GET['length']) : 16;
        $userId = isset ($_GET['userId']) ? intval($_GET['userId']) : 0;
        
        if($userId != 0){
            $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userId);
            $videosCount = $this->getEntityManager()->createQueryBuilder()->select('count(u)')
                ->from('Video\Model\Video', 'u')
                ->where ('u.active = :active AND u.user = :user')
                ->setParameter('active' , 1)
                ->setParameter('user' , $user)
                ->orderBy('u.id' , 'DESC');
            $videos = $this->getEntityManager()->getRepository('Video\Model\Video')
                    ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC'), $length , $start); 
            
        }else{
            $videosCount = $this->getEntityManager()->createQueryBuilder()->select('count(q)')
                ->from('Video\Model\Video', 'q')
                ->where ('q.active = 1 ')
                ->orderBy('q.id' , 'DESC');
            $videos = $this->getEntityManager()->getRepository('Video\Model\Video')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), $length , $start);
        }
         
        
        $all_count = $videosCount->getQuery()->getSingleScalarResult();
        
        $data = array();
        foreach ($videos as $video) {
            $data[] = $video->getArrayCopy();
        }
        
        $arrayff = [
            "total" => $all_count,
            "data" => $data
        ];
        
        return new JsonModel($arrayff);        
    }

    public function getPublicAction()
    {

        $userId = isset ($_GET['userId']) ? intval($_GET['userId']) : 0;
        if($userId != 0){
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userId);
        $videos = $this->getEntityManager()->getRepository('Video\Model\Video')
            ->findBy(array('active' => '1', 'user' => $user ), array('id' => 'DESC'), 6, 0);
        }else{
            $user = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')
                ->findby(array('isPublic' => 1));
            $videos = $this->getEntityManager()->getRepository('Video\Model\Video')
            ->findBy(array('active' => '1', 'user' => $user), array('id' => 'DESC'), 6, 0);
        }
        $data = array();
        foreach ($videos as $video) {
            $data[] = $video->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function getHomePublicAction()
    {
        $user = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')
                ->findby(array('isPublic' => 1));
        $videos = $this->getEntityManager()->getRepository('Video\Model\Video')
            ->findBy(array('active' => '1', 'user' => $user), array('id' => 'DESC'), 6, 0);
        $data = array();
        foreach ($videos as $video) {
            $data[] = $video->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function getByIDAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $visit = new Visit();
        $visit->ip_address = $_SERVER['REMOTE_ADDR'];
        $visit->type = 'video';
        $visit->type_id = $id;
        $visit->visit_date = date('Y-m-d H:i:s');
        $this->getEntityManager()->persist($visit);
        $this->getEntityManager()->flush();

        $video = $this->getEntityManager()->find('Video\Model\Video', $id);
        $data = array();
        $data[] = $video->getArrayCopy();
        return new JsonModel($data);
    }

    public function getByUserAction()
    {
        $userID = (int)$this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $videos = $this->getEntityManager()->getRepository('Video\Model\Video')
            ->findby(array('active' => 1, 'user' => $user), array('id' => 'DESC'), 6, 0);
        $data = array();
        foreach ($videos as $video) {
            $data[] = $video->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function getUserPublicAction()
    {
        $userID = (int)$this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $videos = $this->getEntityManager()->getRepository('Video\Model\Video')
            ->findby(array('active' => 1, 'user' => $user), array('id' => 'DESC'), 6, 0);
        $data = array();
        foreach ($videos as $video) {
            $data[] = $video->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function getAllUserActiveAction()
    {
        $userID = (int)$this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $videos = $this->getEntityManager()->getRepository('Video\Model\Video')
            ->findby(array('active' => 1, 'user' => $user), array('id' => 'DESC'), 16, 6);
        $data = array();
        foreach ($videos as $video) {
            $data[] = $video->getArrayCopy();
        }
        return new JsonModel($data);
    }

//    public function commentsAction(){
//        $id = (int) $this->params()->fromRoute('id', 0);
//        return new ViewModel(array('id' => $id));
//    }
//    public function getcommentsAction(){
//        $id = (int) $this->params()->fromRoute('id', 0);
//        if (!$id) {
//            return $this->redirect()->toRoute('Video');
//        }
//        $comments = $this->getEntityManager()->getRepository('Comment\Model\Comment')->findBy(array('type' => 'video', 'type_id' => $id));
//        //var_dump($comments);die;
//        $data = array();
//        //$i=0;
//        foreach ($comments as $comment) {
//            $data[] = $comment->getArrayCopy();
//        }
//        return new JsonModel(array("data" => $data));
//    }

    public function addAction()
    {
        $form = new VideoForm($this->getEntityManager(), $this->zfcUserAuthentication()->getIdentity()->getId());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $video = new Video();
            $form->setInputFilter($video->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $userid = $this->zfcUserAuthentication()->getIdentity();
                if (!$userid->isAdmin) {
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
                    $countries = $querybuilder->getQuery()->getResult();
                    if (count($countries) <= 0) {
                        return array('form' => $form);
                    }
                } else {
                    $countries = $this->getEntityManager()->getRepository('Countries\Model\Country')->findby(array('id' => '1'));
                }
                $data['country'] = $countries[0];

                /*$data['country'] = $this->getEntityManager()->getRepository('Countries\Model\Country')
                        ->findby(array('id' => $data['country']));
                $data['country'] = $data['country'][0];            */


                $data['user'] = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')
                    ->findby(array('id' => $userid));
                $data['user'] = $data['user'][0];
                $video->exchangeArray($data);
                $video->active = 1;

                $this->getEntityManager()->persist($video);
                $this->getEntityManager()->flush();
                $id = $video->getId();
                $notification = new Notification();
                $message = 'قام ' . $data['user']->displayName . ' بإضافة فيديو جديد';
                $notData = array('type' => 'video', 'type_id' => $id, 'user_type' => '1', 'message' => $message);
                $notification->exchangeArray($notData);
                $this->getEntityManager()->persist($notification);
                $this->getEntityManager()->flush();
                $this->sendgsmNotficationMessage($video);
                // Redirect to list of albums
                return $this->redirect()->toRoute('Video');
            }
        }
        return array('form' => $form);
    }
    
    function sendgsmNotficationMessage($data){
        
        $content = array("en" => $data->subtitle , "ar" => $data->subartitle);
        $headings = array("en" => $data->title , "ar" => $data->artitle);
        $dataContent = array( "id" => $data->getId(),"type" => "videoDetials");
        
        $fields = array(
            'app_id' => "511620c8-50c4-4936-bdea-98567623d54f",
            'included_segments' => array('All'),
            'data' => $dataContent,
            'contents' => $content,
            'headings' => $headings,
            'big_picture' => $data->image
        );

        $fieldss = json_encode($fields);
        //print("\nJSON sent:\n");
        //var_dump($fields); die();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic MmNmYWYwM2QtMWM5NC00YzdhLTk1NDAtNjg4MWM4M2Q3NjVk'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldss);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_exec($ch);
        curl_close($ch);
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Video', array(
                'action' => 'add'
            ));
        }

        $video = $this->getEntityManager()->find('Video\Model\Video', $id);
        //echo '<pre>';print_r($video);die;
        $video->country = $video->country->id;
        $video->user = $video->user->id;

        if (!$video) {
            return $this->redirect()->toRoute('Video', array(
                'action' => 'index'
            ));
        }

        $form = new VideoForm($this->getEntityManager(), $this->zfcUserAuthentication()->getIdentity()->getId());
        $form->bind($video);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($video->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $video->country = $this->getEntityManager()->find('Countries\Model\Country', $video->country);
                $video->user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $video->user);
                //echo '<pre>';print_r($video->country);die;
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Video');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
        }

        $video = $this->getEntityManager()->find('Video\Model\Video', $id);
        if ($video) {
            $this->getEntityManager()->remove($video);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Video Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }

    public function chstatusAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }

        $video = $this->getEntityManager()->find('Video\Model\Video', $id);
        if ($video) {
            $video->active = !$video->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Video Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
}

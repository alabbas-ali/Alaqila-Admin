<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AudioController
 *
 * @author abass
 */

namespace Audio\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Audio\Model\Audio;
use Notification\Model\Notification;
use Audio\Form\AudioForm;
use Visit\Model\Visit;

class AudioController extends AbstractActionController {

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
        $user = $this->zfcUserAuthentication()->getIdentity();
        if (!$user->isAdmin)
            $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')->findby(array('user' => $user), array('id' => 'DESC'));
        else
            $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')->findAll();
        $data = array();
        //$i=0;
        foreach ($audios as $audio) {
            $item = $audio->getArrayCopy();
            $type = 'audio';
            $visits = $this->getEntityManager()->getRepository('Visit\Model\Visit')->findBy(array('type' => $type, 'type_id' => $item['id']));
            $item['visits'] = count($visits);
            $data[] = $item;
        }
        return new JsonModel(array("data" => $data));
    }

    public function getAllPagesAction() {
        $user = $this->zfcUserAuthentication()->getIdentity();

        $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset($_GET['length']) ? intval($_GET['length']) : 10;

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array('table'))
                ->from('Audio\Model\Audio', 'table');

        $columns = array(0 => 'id', 1 => 'title', 2 => 'country', 3 => 'user', 4 => 'audio_date');
        $order = '';
        if (isset($_GET['order'])) {
            $orderBy = array();
            for ($i = 0, $ien = count($_GET['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($_GET['order'][$i]['column']);
                if (isset($columns[$columnIdx])) {
                    $column = $columns[$columnIdx];
                    $dir = $_GET['order'][$i]['dir'] === 'asc' ?
                            'ASC' :
                            'DESC';
                    $qb->orderBy('table.' . $column, $dir);
                }
            }
        }

        if (!$user->isAdmin)
            $qb->where("table.user='$user->id'");


        $all_count = count($qb->getQuery()->getResult());

        $qb->setFirstResult($start);
        $qb->setMaxResults($length);
        //var_dump($qb->getQuery());die;
        $audios = $qb->getQuery()->getResult();
        $data = array();
        //$i=0;
        foreach ($audios as $audio) {
            $item = $audio->getArrayCopy();
            $type = 'audio';
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
        $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), 16, 6);
        $data = array();
        foreach ($audios as $audio) {
            $data[] = $audio->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function getAllActiveNewAction() {
        $start = isset($_GET['start']) ? intval($_GET['start']) : 6;
        $length = isset($_GET['length']) ? intval($_GET['length']) : 16;
        $userId = isset ($_GET['userId']) ? intval($_GET['userId']) : 0;
        
        if($userId != 0){
            $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userId);
            $audiosCount = $this->getEntityManager()->createQueryBuilder()->select('count(u)')
                ->from('Audio\Model\Audio', 'u')
                ->where ('u.active = :active AND u.user = :user')
                ->setParameter('active' , 1)
                ->setParameter('user' , $user)
                ->orderBy('u.id' , 'DESC');
            $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')
                    ->findby(array('active' => 1 , 'user' => $user), array('id' => 'DESC'), $length , $start); 
            
        }else{
            $audiosCount = $this->getEntityManager()->createQueryBuilder()->select('count(q)')
                ->from('Audio\Model\Audio', 'q')
                ->where ('q.active = 1 ')
                ->orderBy('q.id' , 'DESC');
            $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), $length , $start);
        }

        $all_count = $audiosCount->getQuery()->getSingleScalarResult();
        $data = array();
        foreach ($audios as $audio) {
            $data[] = $audio->getArrayCopy();
        }

        $arrayff = [
            "total" => $all_count,
            "data" => $data
        ];

        return new JsonModel($arrayff);
    }

    public function getPublicAction() {
        $userId = isset ($_GET['userId']) ? intval($_GET['userId']) : 0;
        if($userId != 0){
            $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userId);
            $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')
                ->findBy(array('active' => '1', 'user' => $user), array('id' => 'DESC'), 6, 0);
        }else{
           $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), 6, 0);
        }
        
        $data = array();
        foreach ($audios as $audio) {
            $data[] = $audio->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function getHomePublicAction() {
//        $num = (int) $this->params()->fromRoute('id', 0);
//        $qb = $this->getEntityManager()->createQueryBuilder();
//
//        $qb->select('t.id','COUNT(v.id) visits')
//                ->from('Audio\Model\Audio', 't')
//                ->leftJoin('Visit\Model\Visit', 'v',\Doctrine\ORM\Query\Expr\Join::WITH,"v.type_id=t.id")
//                ->where("v.type='audio'")
//                ->groupBy('t.id')
//                ->orderBy('visits', 'DESC')
//                ->setMaxResults( $num );
//
//        $query = $qb->getQuery();
//        //var_dump($query);die;
//        $result=$qb->getQuery()->getResult();
//        $data = array();
//        foreach ($result as $row) {
//            $audio = $this->getEntityManager()->find('Audio\Model\Audio', $row['id']);
//            $data[] = $audio->getArrayCopy();
//            
//        }
//        return new JsonModel($data);

        $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')
                ->findBy(array('active' => '1'), array('id' => 'DESC'), 3, 0);
        $data = array();
        foreach ($audios as $audio) {
            $data[] = $audio->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function getByIDAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $visit = new Visit();
        $visit->ip_address = $_SERVER['REMOTE_ADDR'];
        $visit->type = 'audio';
        $visit->type_id = $id;
        $visit->visit_date = date('Y-m-d H:i:s');
        $this->getEntityManager()->persist($visit);
        $this->getEntityManager()->flush();

        $audio = $this->getEntityManager()->find('Audio\Model\Audio', $id);
        $data = array();
        $data[] = $audio->getArrayCopy();
        return new JsonModel($data);
    }

    public function getByUserAction() {
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')
                ->findby(array('active' => 1, 'user' => $user), array('id' => 'DESC'), 3, 0);
        $data = array();
        foreach ($audios as $audio) {
            $data[] = $audio->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function getUserPublicAction() {
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')
                ->findby(array('active' => 1, 'user' => $user), array('id' => 'DESC'), 6, 0);
        $data = array();
        foreach ($audios as $audio) {
            $data[] = $audio->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function getAllUserActiveAction() {
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $audios = $this->getEntityManager()->getRepository('Audio\Model\Audio')
                ->findby(array('active' => 1, 'user' => $user), array('id' => 'DESC'), 16, 6);
        $data = array();
        foreach ($audios as $audio) {
            $data[] = $audio->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function addAction() {
        $form = new AudioForm($this->getEntityManager(), $this->zfcUserAuthentication()->getIdentity()->getId());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $audio = new Audio();
            $form->setInputFilter($audio->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $userid = $this->zfcUserAuthentication()->getIdentity();
                if (!$userid->isAdmin) {
                    $repository = $this->getEntityManager()->getRepository('Countries\Model\Country');
                    $querybuilder = $repository->createQueryBuilder('c');
                    $querybuilder->select('c')
                            ->leftJoin(
                                    'ZfcUserOver\Model\RoleAssignment', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, "r.instanceid = c.id"
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

                /* $data['country'] = $this->getEntityManager()->getRepository('Countries\Model\Country')
                  ->findby(array('id' => $data['country']));
                  $data['country'] = $data['country'][0]; */


                $data['user'] = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User')
                        ->findby(array('id' => $userid));
                $data['user'] = $data['user'][0];
                $audio->exchangeArray($data);
                $audio->active = 1;

                $this->getEntityManager()->persist($audio);
                $this->getEntityManager()->flush();
                $id = $audio->getId();
                $notification = new Notification();
                $message = 'قام ' . $data['user']->displayName . ' بإضافة صوت جديد';
                $notData = array('type' => 'audio', 'type_id' => $id, 'user_type' => '1', 'message' => $message);
                $notification->exchangeArray($notData);
                $this->getEntityManager()->persist($notification);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Audio');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Audio', array(
                        'action' => 'add'
            ));
        }

        $audio = $this->getEntityManager()->find('Audio\Model\Audio', $id);
        //echo '<pre>';print_r($audio);die;
        $audio->country = $audio->country->id;
        $audio->user = $audio->user->id;

        if (!$audio) {
            return $this->redirect()->toRoute('Audio', array(
                        'action' => 'index'
            ));
        }

        $form = new AudioForm($this->getEntityManager(), $this->zfcUserAuthentication()->getIdentity()->getId());
        $form->bind($audio);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($audio->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $audio->country = $this->getEntityManager()->find('Countries\Model\Country', $audio->country);
                $audio->user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $audio->user);
                //echo '<pre>';print_r($audio->country);die;
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                // return $this->redirect()->toRoute('Audio');
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

        $audio = $this->getEntityManager()->find('Audio\Model\Audio', $id);
        if ($audio) {
            $this->getEntityManager()->remove($audio);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Audio Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }

    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }

        $audio = $this->getEntityManager()->find('Audio\Model\Audio', $id);
        if ($audio) {
            $audio->active = !$audio->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Audio Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }

}

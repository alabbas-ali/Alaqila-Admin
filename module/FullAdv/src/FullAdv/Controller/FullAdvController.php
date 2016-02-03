<?php
//test
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FullAdvController
 *
 * @author abass
 */

namespace FullAdv\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use FullAdv\Model\FullAdv;
use FullAdv\Form\FullAdvForm;
use Visit\Model\Visit;
use Doctrine\ORM\Query\ResultSetMapping;

class FullAdvController extends AbstractActionController {

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
            $fullAdvs = $this->getEntityManager()->getRepository('FullAdv\Model\FullAdv')->findby(array('user' => $user), array('id' => 'DESC'));
        else 
            $fullAdvs = $this->getEntityManager()->getRepository('FullAdv\Model\FullAdv')->findAll();
        $data = array();
        //$i=0;
        foreach ($fullAdvs as $fullAdv) {
            $item=$fullAdv->getArrayCopy();
            $type='FullAdv';
            $visits = $this->getEntityManager()->getRepository('Visit\Model\Visit')->findBy(array('type' => $type, 'type_id' => $item['id']));
            $item['visits']=count($visits);
            $data[] = $item;
        }
        return new JsonModel(array("data" => $data));
    }
    
    public function getAllPagesAction(){
        $user = $this->zfcUserAuthentication()->getIdentity();

        $draw = isset ($_GET['draw']) ? intval($_GET['draw']) : 0;
        $start = isset ($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset ($_GET['length']) ? intval($_GET['length']) : 10;

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array('table'))
            ->from('FullAdv\Model\FullAdv', 'table');

        $columns = array(0 => 'id', 1 => 'title', 2 => 'url', 3 => 'country', 4 => 'user', 5 => 'date');
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
        $fullAdvs = $qb->getQuery()->getResult();
        $data = array();
        //$i=0;
        foreach ($fullAdvs as $fullAdv) {
            $item = $fullAdv->getArrayCopy();
            $type = 'fullAdv';
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
        $fullAdvs = $this->getEntityManager()->getRepository('FullAdv\Model\FullAdv')
                ->findBy(array('active' => '1'), array('id' => 'DESC'));
        $data = array();
        foreach ($fullAdvs as $fullAdv) {
            $data[] = $fullAdv->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getByUserAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $fullAdvs = $this->getEntityManager()->getRepository('FullAdv\Model\FullAdv')
                ->findby(array('active' => 1 , 'user' => $user));
        $data = array();
        foreach ($fullAdvs as $fullAdv) {
            $data[] = $fullAdv->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getRandomOneAction(){
        $fullAdvs = $this->getEntityManager()->getRepository('FullAdv\Model\FullAdv')
                ->findby(array('active' => 1 ));
        $data = array();
        foreach ($fullAdvs as $fullAdv) {
            $data[] = $fullAdv->getArrayCopy();
        }
        
        
        $arrayff = [
            "draw" => '',
            "recordsTotal" => sizeof($fullAdvs),
            "recordsFiltered" => sizeof($fullAdvs),
            "data" => $data
        ];
        return new JsonModel($arrayff);
    }
    
    public function getByIDAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $visit = new Visit();
        $visit->ip_address=$_SERVER['REMOTE_ADDR'];
        $visit->type='fullAdv';
        $visit->type_id=$id;
        $visit->visit_date=date('Y-m-d H:i:s');
        $this->getEntityManager()->persist($visit);
        $this->getEntityManager()->flush();
                
        $fullAdv = $this->getEntityManager()->find('FullAdv\Model\FullAdv', $id);
        $data = array();
        $data[] = $fullAdv->getArrayCopy();
        return new JsonModel($data);
    }

    public function addAction() {
        $form = new FullAdvForm($this->getEntityManager());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $fullAdv = new FullAdv();
            $form->setInputFilter($fullAdv->getInputFilter());
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
                $fullAdv->exchangeArray($data);
                if($userid->isAdmin)
                    $fullAdv->active=1;
                else
                    $fullAdv->active=0;
                
                $this->getEntityManager()->persist($fullAdv);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('FullAdv');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('FullAdv', array(
                        'action' => 'add'
            ));
        }

        $fullAdv = $this->getEntityManager()->find('FullAdv\Model\FullAdv', $id);
        //echo '<pre>';print_r($fullAdv);die;
        $fullAdv->country=$fullAdv->country->id;
        $fullAdv->user=$fullAdv->user->id;
        
        if (!$fullAdv) {
            return $this->redirect()->toRoute('FullAdv', array(
                        'action' => 'index'
            ));
        }

        $form = new FullAdvForm($this->getEntityManager());
        $form->bind($fullAdv);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($fullAdv->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $fullAdv->country=$this->getEntityManager()->find('Countries\Model\Country', $fullAdv->country);
                $fullAdv->user=$this->getEntityManager()->find('ZfcUserOver\Model\User', $fullAdv->user);
                //echo '<pre>';print_r($fullAdv->country);die;
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('FullAdv');
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
        
        $fullAdv = $this->getEntityManager()->find('FullAdv\Model\FullAdv', $id);
        if ($fullAdv) {
            $this->getEntityManager()->remove($fullAdv);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'FullAdv Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    
    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $fullAdv = $this->getEntityManager()->find('FullAdv\Model\FullAdv', $id);
        if ($fullAdv) {
            $fullAdv->active=!$fullAdv->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'FullAdv Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
}

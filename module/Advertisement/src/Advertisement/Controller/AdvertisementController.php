<?php
//test
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdvertisementController
 *
 * @author abass
 */

namespace Advertisement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Advertisement\Model\Advertisement;
use Advertisement\Form\AdvertisementForm;
use Visit\Model\Visit;

class AdvertisementController extends AbstractActionController {

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
            $advertisements = $this->getEntityManager()->getRepository('Advertisement\Model\Advertisement')->findby(array('user' => $user), array('id' => 'DESC'));
        else 
            $advertisements = $this->getEntityManager()->getRepository('Advertisement\Model\Advertisement')->findAll();
        $data = array();
        //$i=0;
        foreach ($advertisements as $advertisement) {
            $item=$advertisement->getArrayCopy();
            $type='advertisement';
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
            ->from('Advertisement\Model\Advertisement', 'table');

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
        $advertisements = $qb->getQuery()->getResult();
        $data = array();
        //$i=0;
        foreach ($advertisements as $advertisement) {
            $item = $advertisement->getArrayCopy();
            $type = 'advertisement';
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
        $advertisements = $this->getEntityManager()->getRepository('Advertisement\Model\Advertisement')
                ->findBy(array('active' => '1'), array('id' => 'DESC'));
        $data = array();
        foreach ($advertisements as $advertisement) {
            $data[] = $advertisement->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getByUserAction(){
        $userID = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getEntityManager()->find('ZfcUserOver\Model\User', $userID);
        $advertisements = $this->getEntityManager()->getRepository('Advertisement\Model\Advertisement')
                ->findby(array('active' => 1 , 'user' => $user));
        $data = array();
        foreach ($advertisements as $advertisement) {
            $data[] = $advertisement->getArrayCopy();
        }
        return new JsonModel($data);
    }
    
    public function getByIDAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $visit = new Visit();
        $visit->ip_address=$_SERVER['REMOTE_ADDR'];
        $visit->type='advertisement';
        $visit->type_id=$id;
        $visit->visit_date=date('Y-m-d H:i:s');
        $this->getEntityManager()->persist($visit);
        $this->getEntityManager()->flush();
                
        $advertisement = $this->getEntityManager()->find('Advertisement\Model\Advertisement', $id);
        $data = array();
        $data[] = $advertisement->getArrayCopy();
        return new JsonModel($data);
    }

    public function addAction() {
        $form = new AdvertisementForm($this->getEntityManager());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $advertisement = new Advertisement();
            $form->setInputFilter($advertisement->getInputFilter());
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
                $advertisement->exchangeArray($data);
                if($userid->isAdmin)
                    $advertisement->active=1;
                else
                    $advertisement->active=0;
                
                $this->getEntityManager()->persist($advertisement);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Advertisement');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Advertisement', array(
                        'action' => 'add'
            ));
        }

        $advertisement = $this->getEntityManager()->find('Advertisement\Model\Advertisement', $id);
        //echo '<pre>';print_r($advertisement);die;
        $advertisement->country=$advertisement->country->id;
        $advertisement->user=$advertisement->user->id;
        
        if (!$advertisement) {
            return $this->redirect()->toRoute('Advertisement', array(
                        'action' => 'index'
            ));
        }

        $form = new AdvertisementForm($this->getEntityManager());
        $form->bind($advertisement);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($advertisement->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $advertisement->country=$this->getEntityManager()->find('Countries\Model\Country', $advertisement->country);
                $advertisement->user=$this->getEntityManager()->find('ZfcUserOver\Model\User', $advertisement->user);
                //echo '<pre>';print_r($advertisement->country);die;
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Advertisement');
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
        
        $advertisement = $this->getEntityManager()->find('Advertisement\Model\Advertisement', $id);
        if ($advertisement) {
            $this->getEntityManager()->remove($advertisement);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Advertisement Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    
    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $advertisement = $this->getEntityManager()->find('Advertisement\Model\Advertisement', $id);
        if ($advertisement) {
            $advertisement->active=!$advertisement->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Advertisement Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
}

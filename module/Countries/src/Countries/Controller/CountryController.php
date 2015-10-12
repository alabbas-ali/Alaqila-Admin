<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AlbumController
 *
 * @author abass
 */

namespace Countries\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Countries\Model\Country;
use Countries\Form\CountryForm;
use Zend\View\Model\JsonModel;

class CountryController extends AbstractActionController {

    protected $em;

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction() {
        return new ViewModel();
    }

    public function getAllAction() {
        $countries = $this->getEntityManager()->getRepository('Countries\Model\Country')->findAll();
        $data = array();
        //$i=0;
        foreach ($countries as $country) {
            $data[] = $country->getArrayCopy();
        }
        //var_dump($data);die();
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
            ->from('Countries\Model\Country', 'table');

        $columns = array(0 => 'id', 1 => 'arname', 2 => 'name');
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

        $all_count = count($qb->getQuery()->getResult());

        $qb->setFirstResult($start);
        $qb->setMaxResults($length);
        //var_dump($qb->getQuery());die;
        $countries = $qb->getQuery()->getResult();
        $data = array();
        //$i=0;
        foreach ($countries as $country) {
            $item = $country->getArrayCopy();
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

    public function addAction() {
        $form = new CountryForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $country = new Country();
            $form->setInputFilter($country->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $country->exchangeArray($form->getData());
                $country->active= true;
                $this->getEntityManager()->persist($country);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Countries');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Countries', array(
                        'action' => 'add'
            ));
        }

        $country = $this->getEntityManager()->find('Countries\Model\Country', $id);
        if (!$country) {
            return $this->redirect()->toRoute('Countries', array(
                        'action' => 'index'
            ));
        }

        $form = new CountryForm();
        $form->bind($country);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($country->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Countries');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id==1) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
        }
        
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
        }
        
        $country = $this->getEntityManager()->find('Countries\Model\Country', $id);
        if ($country) {
            $this->getEntityManager()->remove($country);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Country Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    
   public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $country = $this->getEntityManager()->find('Countries\Model\Country', $id);
        if ($country) {
            $country->active=!$country->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Country Have been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }


}

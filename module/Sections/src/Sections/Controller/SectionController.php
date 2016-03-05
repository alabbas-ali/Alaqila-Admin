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

namespace Sections\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Sections\Model\Section;
use Sections\Form\SectionForm;
use Zend\View\Model\JsonModel;

class SectionController extends AbstractActionController {

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
        $sections = $this->getEntityManager()->getRepository('Sections\Model\Section')->findAll();
        $data = array();
        //$i=0;
        foreach ($sections as $section) {
            $data[] = $section->getArrayCopy();
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
            ->from('Sections\Model\Section', 'table');

        $columns = array(0 => 'id', 1 => 'name', 2 => 'type');
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
        $sections = $qb->getQuery()->getResult();
        $data = array();
        //$i=0;
        foreach ($sections as $section) {
            $item = $section->getArrayCopy();
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
        $form = new SectionForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $section = new Section();
            $form->setInputFilter($section->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $section->exchangeArray($form->getData());
                $section->active= true;
                $this->getEntityManager()->persist($section);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Sections');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Sections', array(
                        'action' => 'add'
            ));
        }

        $section = $this->getEntityManager()->find('Sections\Model\Section', $id);
        if (!$section) {
            return $this->redirect()->toRoute('Sections', array(
                        'action' => 'index'
            ));
        }

        $form = new SectionForm();
        $form->bind($section);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($section->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Sections');
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
        
        $section = $this->getEntityManager()->find('Sections\Model\Section', $id);
        if ($section) {
            $this->getEntityManager()->remove($section);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Section Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    
   public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $section = $this->getEntityManager()->find('Sections\Model\Section', $id);
        if ($section) {
            $section->active=!$section->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Section Have been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }


}

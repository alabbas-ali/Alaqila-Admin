<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VisitController
 *
 * @author abass
 */

namespace Visit\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Visit\Model\Visit;
use Visit\Form\VisitForm;

class VisitController extends AbstractActionController {

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
            $qb->select('v.id','v.ip_address','v.type','v.type_id','v.visit_date')
                ->from('Visit\Model\Visit', 'v')
                ->where("v.type_id in (select news.id from News\Model\News news where news.user='".$user->id."') and v.type='news'")
                ->orWhere("v.type_id in (select audio.id from Audio\Model\Audio audio where audio.user='".$user->id."') and v.type='audio'")
                ->orWhere("v.type_id in (select photo.id from Photo\Model\Photo photo where photo.user='".$user->id."') and v.type='photo'")
                ->orWhere("v.type_id in (select video.id from Video\Model\Video video where video.user='".$user->id."') and v.type='video'")
                ->orWhere("v.type_id in (select advertisement.id from Advertisement\Model\Advertisement advertisement where advertisement.user='".$user->id."') and v.type='advertisement'")
                ;

            $query = $qb->getQuery();
            $data=$qb->getQuery()->getResult();
        } else {
            $visits = $this->getEntityManager()->getRepository('Visit\Model\Visit')->findAll();
            $data = array();
            //$i=0;
            foreach ($visits as $visit) {
                $data[] = $visit->getArrayCopy();
            }
        }
        return new JsonModel(array("data" => $data));
    }
    
    public function visitsAction(){
        $type = $this->params()->fromRoute('type', 0);
        $id = (int) $this->params()->fromRoute('id', 0);
        return new ViewModel(array('type' => $type,'id' => $id));
    }
    public function getvisitsAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
        $type = $this->params()->fromRoute('type', 0);
        if (!$id) {
            return $this->redirect()->toRoute('News');
        }
        $visits = $this->getEntityManager()->getRepository('Visit\Model\Visit')
                ->findBy(array('type' => $type, 'type_id' => $id), array('id' => 'DESC'));
        //var_dump($visits);die;
        $data = array();
        //$i=0;
        foreach ($visits as $visit) {
            $data[] = $visit->getArrayCopy();
        }
        return new JsonModel(array("data" => $data));
    }

    public function addAction() {
        $form = new VisitForm($this->getEntityManager());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $visit = new Visit();
            $form->setInputFilter($visit->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $visit->exchangeArray($data);
                $visit->active=0;
                $this->getEntityManager()->persist($visit);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Visit');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Visit', array(
                        'action' => 'add'
            ));
        }

        $visit = $this->getEntityManager()->find('Visit\Model\Visit', $id);
        if (!$visit) {
            return $this->redirect()->toRoute('Visit', array(
                        'action' => 'index'
            ));
        }

        $form = new VisitForm($this->getEntityManager());
        $form->bind($visit);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($visit->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Visit');
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
        
        $visit = $this->getEntityManager()->find('Visit\Model\Visit', $id);
        if ($visit) {
            $this->getEntityManager()->remove($visit);
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Visit Have been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }
        
        $visit = $this->getEntityManager()->find('Visit\Model\Visit', $id);
        if ($visit) {
            $visit->active=!$visit->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Visit Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }
}

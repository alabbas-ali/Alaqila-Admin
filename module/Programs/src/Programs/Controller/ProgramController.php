<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProgramController
 *
 * @author abass
 */

namespace Programs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Programs\Model\Program;
use Programs\Form\ProgramForm;

class ProgramController extends AbstractActionController {

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
        $day = $this->params()->fromRoute('id');
        //$day = ($dayid == 2 ? 'tomorrow' : 'today');
        //var_dump($day);
        $Programs = $this->getEntityManager()->getRepository('Programs\Model\Program')->findby(array('program_day' => $day));
        $data = array();
        //$i=0;
        foreach ($Programs as $Program) {
            $data[] = $Program->getArrayCopy();
        }
        //var_dump($data);die();
        return new JsonModel(array("data" => $data));
    }

    public function orderAction() {

        // id:3
        //fromPosition:3
        //toPosition:2
        //direction:back
        //direction:forward
        //group:

        $request = $this->getRequest();
        if ($request->isPost()) {

            $data = $request->getPost();
            if ($data['direction'] == 'forward') {
                $q = $this->getEntityManager()->createQueryBuilder()->update('Programs\Model\Program', 'u')
                                ->set('u.ord', 'u.ord - 1')->where('u.ord >' . $data['fromPosition'] . ' and u.ord <= ' . $data['toPosition'])->getQuery()->execute();
                $q = $this->getEntityManager()->createQueryBuilder()->update('Programs\Model\Program', 'u')
                                ->set('u.ord', $data['toPosition'])->where('u.id =' . $data['id'])->getQuery()->execute();
            }

            if ($data['direction'] == 'back') {
                $q = $this->getEntityManager()->createQueryBuilder()->update('Programs\Model\Program', 'u')
                                ->set('u.ord', 'u.ord + 1')->where('u.ord <' . $data['fromPosition'] . ' and u.ord >= ' . $data['toPosition'])->getQuery()->execute();
                $q = $this->getEntityManager()->createQueryBuilder()->update('Programs\Model\Program', 'u')
                                ->set('u.ord', $data['toPosition'])->where('u.id =' . $data['id'])->getQuery()->execute();
            }
        }

        return new JsonModel();
    }

    public function getAllActiveAction() {
        $Programs = $this->getEntityManager()->getRepository('Programs\Model\Program')
                ->findby(array('active' => 1), array('ord' => 'ASC'));
        $data = array();
        //$i=0;
        foreach ($Programs as $Program) {
            $prgm=$Program->getArrayCopy();
            if($prgm['program_day']==2){
                $dateComplation=date("F j, Y ",strtotime("+1 day")); 
            }else{
                $dateComplation=date("F j, Y "); 
            }
            //echo $prgm['start_date'];die;
            $prgm['start_date']=$dateComplation.$prgm['start_date'];
            
            $prgm['duration']=date("F j, Y H:i:s",strtotime("+".$prgm['duration']." minute",strtotime($prgm['start_date'])));
            $data[] = $prgm;
            //$data[] = $Program->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function getProgramUsersAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $repository = $this->getEntityManager()->getRepository('ZfcUserOver\Model\User');
        $querybuilder = $repository->createQueryBuilder('c');
        $querybuilder->select('c')
                ->leftJoin(
                        'ZfcUserOver\Model\RoleAssignment', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, "r.userid = c.id"
                )
                ->where("r.instanceid = $id AND r.context='program'");
        $users = $querybuilder->getQuery()->getResult();
        //$Program = $this->getEntityManager()->find('Programs\Model\Program', $id);
        $data = array();
        foreach ($users as $user) {
            if (!$user->isAdmin)
                $data[] = $user->getArrayCopy();
        }
        return new JsonModel($data);
    }

    public function addAction() {
        $form = new ProgramForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $Program = new Program();
            $form->setInputFilter($Program->getInputFilter());
            $form->setData($request->getPost());


            if ($form->isValid()) {
                $Program->exchangeArray($form->getData());
                $Program->active = 1;

                $highest_order = $this->getEntityManager()->createQueryBuilder()->select('MAX(e.ord)')->from('Programs\Model\Program', 'e')
                                ->getQuery()->getSingleScalarResult();
                $Program->ord = $highest_order + 1;
                $this->getEntityManager()->persist($Program);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Programs');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Programs', array(
                        'action' => 'add'
            ));
        }

        $Program = $this->getEntityManager()->find('Programs\Model\Program', $id);
        if (!$Program) {
            return $this->redirect()->toRoute('Programs', array(
                        'action' => 'index'
            ));
        }

        $form = new ProgramForm();
        $form->bind($Program);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($Program->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('Programs');
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

        $Program = $this->getEntityManager()->find('Programs\Model\Program', $id);
        if ($Program) {
            $this->getEntityManager()->remove($Program);
            $this->getEntityManager()->flush();
            $q = $this->getEntityManager()->createQueryBuilder()->update('Programs\Model\Program', 'u')
                            ->set('u.ord', 'u.ord - 1')->where('u.id >' . $id)->getQuery()->execute();
            return new JsonModel(array("done" => 'true', 'message' => 'Program Has been Deletet'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }

    public function chstatusAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
        }

        $Program = $this->getEntityManager()->find('Programs\Model\Program', $id);
        if ($Program) {
            $Program->active = !$Program->active;
            $this->getEntityManager()->flush();
            return new JsonModel(array("done" => 'true', 'message' => 'Program Has been Edited'));
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Edit This'));
    }

}

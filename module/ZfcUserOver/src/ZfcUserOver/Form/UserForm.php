<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AlbumForm
 *
 * @author abass
 */
namespace ZfcUserOver\Form;

use Zend\Form\Form;
use Doctrine\ORM\EntityManager;

class UserForm extends Form {
    
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
     {
         // we want to ignore the name passed
         parent::__construct('User');

         $this->entityManager = $entityManager;

        $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
        $this->add(array(
             'name' => 'facebook',
             'type' => 'Hidden',
         ));
        $this->add(array(
             'name' => 'instagram',
             'type' => 'Hidden',
         ));
        $this->add(array(
             'name' => 'twitter',
             'type' => 'Hidden',
         ));
        $this->add(array(
             'name' => 'livestream',
             'type' => 'Hidden',
         ));
        
        $this->add(array(
             'name' => 'username',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Username',
                    'id' =>'username'
                ),
         ));
        $this->add(array(
             'name' => 'displayName',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Display Name',
                    'id' =>'displayName'
                ),
        ));
        $this->add(array(
             'name' => 'photo',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Photo',
                    'id' =>'photo'
                ),
        ));
        $this->add(array(
             'name' => 'email',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                    'id' =>'email'
                ),
        ));
        $this->add(array(
             'name' => 'password',
             'type' => 'Password',
             'attributes' => array(
                    'class' => 'form-control',
                    'id' =>'password'
                ),
        ));
        $this->add(array(
             'name' => 'passwordconfirm',
             'type' => 'Password',
             'attributes' => array(
                    'class' => 'form-control',
                    'id' =>'passwordconfirm'
                ),
        ));
        /*$this->add(array(
            'name' => 'page',
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Page',
                'multiple'=>false,
                'id' => 'page'
            ),
            'options' => array(
                'object_manager' => $this->entityManager,
                'target_class' => 'Pages\Model\Page',
                'property' => 'name',
            ),
        ));*/
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
     }
 }

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

class SettingsForm extends Form {
    
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
             'name' => 'username',
             'type' => 'Hidden',
         ));
        $this->add(array(
             'name' => 'displayName',
             'type' => 'Hidden',
         ));
        $this->add(array(
             'name' => 'photo',
             'type' => 'Hidden',
         ));
        $this->add(array(
             'name' => 'email',
             'type' => 'Hidden',
         ));
        $this->add(array(
             'name' => 'password',
             'type' => 'Hidden',
         ));
        $this->add(array(
             'name' => 'facebook',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Facebook URL',
                    'id' =>'facebook'
                ),
        ));
        $this->add(array(
             'name' => 'instagram',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Instagram URL',
                    'id' =>'instagram'
                ),
        ));
        $this->add(array(
             'name' => 'twitter',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Twitter URL',
                    'id' =>'twitter'
                ),
        ));
        $this->add(array(
             'name' => 'livestream',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Live Stream URL',
                    'id' =>'livestream'
                ),
        ));
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

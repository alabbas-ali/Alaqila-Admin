<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FullAdvForm
 *
 * @author abass
 */

namespace FullAdv\Form;

use Zend\Form\Form;
use Doctrine\ORM\EntityManager;

class FullAdvForm extends Form {

    protected $entityManager;

    public function __construct(EntityManager $entityManager) {
        parent::__construct('Advertisement');

        $this->entityManager = $entityManager;

        // we want to ignore the name passed

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');


        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'date',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Title',
                'id' => 'title'
            ),
        ));

        $this->add(array(
            'name' => 'artitle',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Title',
                'id' => 'artitle'
            ),
        ));

        $this->add(array(
             'name' => 'image',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Photo',
                    'id' =>'photo'
                ),
        ));
        $this->add(array(
            'name' => 'url',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'URL',
                'id' => 'url'
            ),
        ));

        /*$this->add(array(
            'name' => 'country',
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Country',
                'multiple'=>false,
                'id' => 'country'
            ),
            'options' => array(
                'object_manager' => $this->entityManager,
                'target_class' => 'Countries\Model\Country',
                'property' => 'arname',
                'is_method'      => true,
                'find_method'    => array(
                    'name'   => 'getMyCountries',
                    'params' => array(
                        'userid' => $userid,
                    ),
                ),
            ),
        ));
         * 
         */
        
        /*$this->add(array(
            'name' => 'user',
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'User',
                'multiple'=>false,
                'id' => 'country'
            ),
            'options' => array(
                'object_manager' => $this->entityManager,
                'target_class' => 'ZfcUserOver\Model\User',
                'property' => 'displayName',
            ),
        ));*/
        $this->add(array(
            'name' => 'country',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'user',
            'type' => 'Hidden',
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

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

namespace Visit\Form;

use Zend\Form\Form;
use Doctrine\ORM\EntityManager;

class VisitForm extends Form {

    protected $entityManager;

    public function __construct(EntityManager $entityManager) {
        parent::__construct('Visit');

        $this->entityManager = $entityManager;

        // we want to ignore the name passed

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');


        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'visit_date',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'ip_address',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'IP Address',
                'id' => 'ip_address'
            ),
        ));
        $this->add(array(
             'name' => 'type',
             'type' => 'select',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type',
                    'id' =>'visitlink'
                ),
            'options' => array(
                'label' => 'Type',
                'value_options' => array(
                    array(
                        'value' => 'news',
                        'label' => 'News',
                    ),
                    array(
                        'value' => 'video',
                        'label' => 'Video',
                    ),
                    array(
                        'value' => 'audio',
                        'label' => 'Audio',
                    ),
                    array(
                        'value' => 'photo',
                        'label' => 'Photo',
                    ),
                    array(
                        'value' => 'advertisement',
                        'label' => 'Advertisement',
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'type_id',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Type ID',
                'id' => 'type_id'
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

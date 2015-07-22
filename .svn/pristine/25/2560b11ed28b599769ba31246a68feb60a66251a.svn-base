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

namespace Settings\Form;

use Zend\Form\Form;
use Doctrine\ORM\EntityManager;

class SettingsForm extends Form {

    protected $entityManager;

    public function __construct(EntityManager $entityManager) {
        parent::__construct('Comment');

        $this->entityManager = $entityManager;

        // we want to ignore the name passed

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');


        $this->add(array(
            'name' => 'id',
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
             'name' => 'youtube',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Youtube URL',
                    'id' =>'youtube'
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
             'name' => 'frequency',
             'type' => 'Textarea',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Frequency',
                    'id' =>'frequency'
                ),
        ));
        $this->add(array(
             'name' => 'news_num',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'News Number',
                    'id' =>'news_num'
                ),
        ));
        $this->add(array(
             'name' => 'upload_folder',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Upload Folder',
                    'id' =>'upload_folder'
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

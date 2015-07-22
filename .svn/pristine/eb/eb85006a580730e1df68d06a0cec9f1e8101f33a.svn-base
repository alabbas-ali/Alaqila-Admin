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

namespace Comment\Form;

use Zend\Form\Form;
use Doctrine\ORM\EntityManager;

class CommentForm extends Form {

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
            'name' => 'comment_date',
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
            'name' => 'content',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Content',
                'id' => 'content'
            ),
        ));

        $this->add(array(
             'name' => 'username',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'User Name',
                    'id' =>'photo'
                ),
        ));
        $this->add(array(
             'name' => 'type',
             'type' => 'select',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type',
                    'id' =>'commentlink'
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

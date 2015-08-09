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


class CommentForm extends Form {



    public function __construct() {
        parent::__construct('Comment');

        // we want to ignore the name passed

        $this->setAttribute('method', 'post');
 
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'comment_date',
            'type' => 'Hidden',
        ));
        

        $this->add(array(
            'name' => 'content',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Content',
                'id' => 'content',
                'maxlength'=> '255',
                'required' => 'required',
            ),
        ));

        $this->add(array(
             'name' => 'username',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'User Name',
                    'id' =>'photo',
                    'maxlength'=> '60',
                    'required' => 'required',
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
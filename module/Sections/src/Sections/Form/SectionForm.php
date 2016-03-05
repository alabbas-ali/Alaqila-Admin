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

namespace Sections\Form;

use Zend\Form\Form;

class SectionForm extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('Section');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Name',
                'id' => 'name',
                'maxlength' => '60',
                'required' => 'required',
                'pattern' => '^[a-zA-Z]+( [a-zA-Z]*)*$',
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
                ),
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

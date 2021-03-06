<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageForm
 *
 * @author abass
 */
namespace Pages\Form;

use Zend\Form\Form;

class PageForm extends Form {
    
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('Page');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'ord',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'name',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Arabic Name',
                    'id' =>'name'
                ),
         ));
         $this->add(array(
             'name' => 'enname',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'English Name',
                    'id' =>'enname'
                ),
         ));
         $this->add(array(
             'name' => 'image',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Image',
                    'id' =>'photo'
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

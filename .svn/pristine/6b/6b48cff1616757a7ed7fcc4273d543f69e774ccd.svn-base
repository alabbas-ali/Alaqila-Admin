<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProgramForm
 *
 * @author abass
 */
namespace Programs\Form;

use Zend\Form\Form;

class ProgramForm extends Form {
    
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('Program');

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
             'name' => 'program_day',
             'type' => 'select',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Program Day',
                    'id' =>'program_day'
                ),
            'options' => array(
                'value_options' => array(
                    array(
                        'value' => '1',
                        'label' => 'Today',
                    ),
                    array(
                        'value' => '2',
                        'label' => 'Tomorrow',
                    ),
                ),
            ),
        ));
        $timeOptions=array();
        for ($i = 0; $i <= 47; $i++){
            $start=mktime(0, 0, 0)+$i*30*60;
            $value=date('H:i:s',$start);
            $label=date('H:i',$start);
            $timeOptions[]=array('value'=>$value,'label'=>$label);
            //$timeOptions[$value]=$value;
        }
        //var_dump($timeOptions);die;
        $this->add(array(
             'name' => 'start_date',
             'type' => 'select',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Start Date',
                    'id' =>'start_date'
                ),
            'options' => array(
                'value_options' => $timeOptions
            ),
        ));
        $this->add(array(
            'name' => 'duration',
            'type' => 'Text',
            'attributes' => array(
                   'class' => 'form-control',
                   'placeholder' => 'Duration',
                   'id' =>'duration'
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

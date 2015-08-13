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

namespace Countries\Form;

use Zend\Form\Form;

class CountryForm extends Form {

    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct('Country');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'English Name',
                'id' => 'name',
                'maxlength' => '60',
                'required' => 'required',
                'pattern' => '^[a-zA-Z]+( [a-zA-Z]*)*$',
            ),
        ));
        $this->add(array(
            'name' => 'arname',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Arabic Name',
                'id' => 'arname',
                'maxlength'=> '60',
                'required' => 'required',
                'pattern' => '^[ض ص ث ق ف غ ع ه خ ح ج د ش س ي ب ل ا ت ن م ك ط ئ ء ؤ ر لا ى ة و ز ظ أ إ آ]+( [ض ص ث ق ف غ ع ه خ ح ج د ش س ي ب ل ا ت ن م ك ط ئ ء ؤ ر لا ى ة و ز ظ أ إ آ]*)*$',
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

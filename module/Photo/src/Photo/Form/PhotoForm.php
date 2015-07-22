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

namespace Photo\Form;

use Zend\Form\Form;
use Doctrine\ORM\EntityManager;

class PhotoForm extends Form {

    protected $entityManager;

    public function __construct(EntityManager $entityManager,$userid) {
        parent::__construct('Photo');

        $this->entityManager = $entityManager;

        // we want to ignore the name passed

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');


        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'photo_date',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Title',
                'id' => 'title',
                'maxlength'=> '60',
                'required' => 'required',
                'pattern' => '^[a-zA-Z0-9?;,{}[\]\-_+=!@#$%\^&*|]+( [a-zA-Z0-9?;,{}[\]\-_+=!@#$%\^&*|]*)*$',
            ),
        ));

        $this->add(array(
            'name' => 'subtitle',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Title',
                'id' => 'subtitle',
                'maxlength'=> '60',
                'pattern' => '^[a-zA-Z0-9?;,{}()[\]\-_+=!@#$%\^&*|]+( [a-zA-Z0-9?;,{}()[\]\-_+=!@#$%\^&*|]*)*$',
            ),
        ));

        $this->add(array(
            'name' => 'artitle',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Title',
                'id' => 'artitle',
                'maxlength'=> '60',
                'required' => 'required',
                'pattern' => '^[ض ص ث ق ف غ ع ه خ ح ج د ش س ي ب ل ا ت ن م ك ط ئ ء ؤ ر لا ى ة و ز ظ أ إ آ?;,{}()[\]\-_+=!@#$%\^&*|]+( [ض ص ث ق ف غ ع ه خ ح ج د ش س ي ب ل ا ت ن م ك ط ئ ء ؤ ر لا ى ة و ز ظ أ إ آ?;,{}()[\]\-_+=!@#$%\^&*|]*)*$',
            ),
        ));

        $this->add(array(
            'name' => 'subartitle',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Title',
                'id' => 'subartitle',
                'maxlength'=> '60',
                'pattern' => '^[ض ص ث ق ف غ ع ه خ ح ج د ش س ي ب ل ا ت ن م ك ط ئ ء ؤ ر لا ى ة و ز ظ أ إ آ?;,{}()[\]\-_+=!@#$%\^&*|]+( [ض ص ث ق ف غ ع ه خ ح ج د ش س ي ب ل ا ت ن م ك ط ئ ء ؤ ر لا ى ة و ز ظ أ إ آ?;,{}()[\]\-_+=!@#$%\^&*|]*)*$',
            ),
        ));

        $this->add(array(
             'name' => 'photo',
             'type' => 'Text',
             'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Photo',
                    'id' =>'photo'
                ),
        ));
        $this->add(array(
            'name' => 'content',
            'type' => 'Textarea',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Content',
                'id' => 'content',
                'maxlength'=> '1500',
                'required' => 'required',
            ),
        ));

        $this->add(array(
            'name' => 'arcontent',
            'type' => 'Textarea',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Content',
                'id' => 'arcontent',
                'maxlength'=> '1500',
                'required' => 'required',
                'dir' => 'rtl',
            ),
        ));

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

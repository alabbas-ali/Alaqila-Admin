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

namespace Imagesuploads\Form;

use Zend\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;
use Imagesuploads\Filter\ResizeFilter;

class ImagesuploadsForm extends Form {

    //protected $folder;

    public function __construct($name = null, $folder, $options = array()) {
        parent::__construct($name, $options);
        $this->addElements();
        $this->setInputFilter($this->createInputFilter($folder));
        //$folder = $folder;
    }

    public function addElements() {

        // File Input
        $file = new Element\File('file');
        $file->setLabel('File Input')
                ->setAttributes(array(
                    'id' => 'file',
                    'multiple' => true,
                    'accept' => 'video/*,audio/*'
        ));
        $this->add($file);
    }

    public function createInputFilter($folder) {
        $inputFilter = new InputFilter\InputFilter();

        // File Input
        $file = new InputFilter\FileInput('file');
        $file->setRequired(true);

        // You only need to define validators and filters
        // as if only one file was being uploaded. All files
        // will be run through the same validators and filters
        // automatically.


        $file->getFilterChain()->attachByName('filerenameupload', array('target' => './public/advuploads/' . $folder . '/orginal/',
            'overwrite' => true,
            'use_upload_name' => true,
            'randomize' => true));


        $file->getFilterChain()->attach(new ResizeFilter(array('directory' => './public/advuploads/' . $folder . '/360-480',
            'target' => './public/advuploads/' . $folder . '/360-480',
            'width' => 360,
            'height' => 480,
            'keepRatio' => true,)));

        $file->getFilterChain()->attach(new ResizeFilter(array('directory' => './public/advuploads/' . $folder . '/640-960',
            'target' => './public/advuploads/' . $folder . '/640-960',
            'width' => 640,
            'height' => 960,
            'keepRatio' => true,)));

        $file->getFilterChain()->attach(new ResizeFilter(array('directory' => './public/advuploads/' . $folder . '/640-1136',
            'target' => './public/advuploads/' . $folder . '/640-1136',
            'width' => 640,
            'height' => 1136,
            'keepRatio' => true,)));

        $file->getFilterChain()->attach(new ResizeFilter(array('directory' => './public/advuploads/' . $folder . '/750-1334',
            'target' => './public/advuploads/' . $folder . '/750-1334',
            'width' => 750,
            'height' => 1334,
            'keepRatio' => true,)));
        //var_dump($file); die();
        $inputFilter->add($file);
        return $inputFilter;
    }

}

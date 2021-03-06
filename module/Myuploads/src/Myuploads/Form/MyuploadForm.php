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

namespace Myuploads\Form;

use Zend\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;
use Myuploads\Filter\ResizeFilter;

class MyuploadForm extends Form {
    
    //protected $folder;
    
    public function __construct($name = null, $folder ,$options = array()) {
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
        

        $file->getFilterChain()->attachByName( 'filerenameupload', array( 'target' => './public/uploads/'.$folder.'/orginal/',
                                                                          'overwrite' => true,
                                                                          'use_upload_name' => true,
                                                                          'randomize' => true));
        

        $file->getFilterChain()->attach(new ResizeFilter(array( 'directory' => './public/uploads/'.$folder.'/300-300',
                                                                'target' => './public/uploads/'.$folder.'/300-300',
                                                                'width' => 300,
                                                                'height' => 300,
                                                                'keepRatio' => true,))); 
        
        $file->getFilterChain()->attach(new ResizeFilter(array( 'directory' => './public/uploads/'.$folder.'/600-600',
                                                                'target' => './public/uploads/'.$folder.'/600-600',
                                                                'width' => 600,
                                                                'height' => 600,
                                                                'keepRatio' => true,)));
        
        $file->getFilterChain()->attach(new ResizeFilter(array( 'directory' => './public/uploads/'.$folder.'/350-175',
                                                                'target' => './public/uploads/'.$folder.'/350-175',
                                                                'width' => 300,
                                                                'height' => 175,
                                                                'keepRatio' => true,)));
        
        $file->getFilterChain()->attach(new ResizeFilter(array( 'directory' => './public/uploads/'.$folder.'/700-350',
                                                                'target' => './public/uploads/'.$folder.'/700-350',
                                                                'width' => 700,
                                                                'height' => 350,
                                                                'keepRatio' => true,)));
        //var_dump($file); die();
        $inputFilter->add($file);
        return $inputFilter;
    }

}

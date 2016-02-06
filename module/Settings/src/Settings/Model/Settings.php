<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Album
 *
 * @author abass
 */

namespace Settings\Model;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="settings")
 */
class Settings implements InputFilterAwareInterface {

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $facebook;

    /**
     * @ORM\Column(type="string")
     */
    protected $twitter;

    /**
     * @ORM\Column(type="string")
     */
    protected $youtube;

    /**
     * @ORM\Column(type="string")
     */
    protected $livestream;

    /**
     * @ORM\Column(type="string")
     */
    protected $frequency;

    /**
     * @ORM\Column(type="string")
     */
    protected $news_num;

    /**
     * @ORM\Column(type="string")
     */
    protected $instagram;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $googleaddcode;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $upload_folder;

    /**
     * @param string $property
     * @return mixed
     */
    public function __get($property) {
        return $this->$property;
    }

    /**
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value) {
        $this->$property = $value;
    }

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->facebook = (isset($data['facebook'])) ? $data['facebook'] : null;
        $this->twitter = (isset($data['twitter'])) ? $data['twitter'] : null;
        $this->youtube = (isset($data['youtube'])) ? $data['youtube'] : null;
        $this->livestream = (isset($data['livestream'])) ? $data['livestream'] : null;
        $this->frequency = (isset($data['frequency'])) ? $data['frequency'] : null;
        $this->news_num = (isset($data['news_num'])) ? $data['news_num'] : null;
        $this->instagram = (isset($data['instagram'])) ? $data['instagram'] : null;
        $this->upload_folder = (isset($data['upload_folder'])) ? $data['upload_folder'] : null;
        $this->googleaddcode = (isset($data['googleaddcode'])) ? $data['googleaddcode'] : null;
    }

    // Add the following method:
    public function getArrayCopy() {
        $data = array();
        foreach ($this as $key => $value) {
            
            if (is_object($value) && $key!='inputFilter') {
                $data[$key] = $value->getArrayCopy();
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'upload_folder',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
            ));

            
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Program
 *
 * @author abass
 */

namespace Programs\Model;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="program")
 */
class Program implements InputFilterAwareInterface {

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $name;

    /**  @ORM\Column(type="string") */
    protected $enname;
    
    /**  @ORM\Column(type="string") */
    protected $image;
    
    /**  @ORM\Column(type="string") */
    protected $program_day;
    
    /**  @ORM\Column(type="string") */
    protected $start_date;
    
    /**  @ORM\Column(type="string") */
    protected $duration;
    
    /**  @ORM\Column(type="boolean") */
    protected $active;

    /**  @ORM\Column(type="integer") */
    protected $ord;

    function getName() {
        return $this->name;
    }

    function getEnname() {
        return $this->enname;
    }

    function getImage() {
        return $this->image;
    }

    function getActive() {
        return $this->active;
    }

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

    /**
     * @return array
     */
    public function getArrayCopy() {
        return get_object_vars($this);
    }

    /**
     * @param array $data
     */
    public function exchangeArray($data = array()) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->enname = $data['enname'];
        $this->image = $data['image'];
        $this->program_day = $data['program_day'];
        $this->start_date = $data['start_date'];
        $this->duration = $data['duration'];
        $this->ord = $data['ord'];
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'enname',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'image',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'program_day',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'start_date',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'duration',
                'required' => true,
            ));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}

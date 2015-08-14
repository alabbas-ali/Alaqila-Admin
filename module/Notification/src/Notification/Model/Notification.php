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

namespace Notification\Model;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="notification")
 */
class Notification implements InputFilterAwareInterface {

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
    protected $type;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_type;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $notification_date;

    /**  @ORM\Column(type="boolean") */
    protected $seen;

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
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->type_id = (isset($data['type_id'])) ? $data['type_id'] : null;
        $this->user_type = (isset($data['user_type'])) ? $data['user_type'] : null;
        $this->user_id = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->notification_date = (isset($data['notification_date'])) ? $data['notification_date'] : date('Y-m-d H:i:s');
        $this->seen = (isset($data['seen'])) ? $data['seen'] : '0';
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
                'name' => 'content',
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
                            'max' => 255,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'username',
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
            

            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}

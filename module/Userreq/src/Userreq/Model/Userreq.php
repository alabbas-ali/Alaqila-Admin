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

namespace Userreq\Model;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;



/**
 * @ORM\Entity
 * @ORM\Table(name="userrequest")
 */
class Userreq implements InputFilterAwareInterface {

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
    protected $username;

    /**
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="Countries\Model\Country")
     * @ORM\JoinColumn(name="country", referencedColumnName="id")
     * */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Pages\Model\Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     * */
    protected $page_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $displayName;

    /**
     * @ORM\Column(type="string")
     */
    protected $displayName2;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    protected $photo;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    protected $password;


    public function getId() {
        return $this->id;
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

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->country = (isset($data['country'])) ? $data['country'] : null;
        $this->page_id = (isset($data['page_id'])) ? $data['page_id'] : null;
        $this->displayName = (isset($data['displayName'])) ? $data['displayName'] : null;
        $this->displayName2 = (isset($data['displayName2'])) ? $data['displayName2'] : null;
        $this->photo = (isset($data['photo'])) ? $data['photo'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
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
                'name' => 'title',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'subtitle',
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 60,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'artitle',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'subartitle',
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 60,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'userreq',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'content',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 4000,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'arcontent',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 4000,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'country',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 'user',
                'required' => false,
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}

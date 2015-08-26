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

namespace News\Model;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="news")
 */
class News implements InputFilterAwareInterface {

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
    protected $content;

    /**
     * @ORM\Column(type="string")
     */
    protected $arcontent;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string")
     */
    protected $subtitle;

    /**
     * @ORM\Column(type="string")
     */
    protected $artitle;

    /**
     * @ORM\Column(type="string")
     */
    protected $subartitle;

    /**
     * @ORM\Column(type="string")
     */
    protected $image;

    /**
     * @ORM\ManyToOne(targetEntity="Countries\Model\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * */
    protected $country;
    
    /**
     * @ORM\ManyToOne(targetEntity="ZfcUserOver\Model\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $news_date;

    /**  @ORM\Column(type="boolean") */
    protected $active;

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
        $this->title = (isset($data['title'])) ? $data['title'] : null;
        $this->artitle = (isset($data['artitle'])) ? $data['artitle'] : null;
        $this->subtitle = (isset($data['subtitle'])) ? $data['subtitle'] : null;
        $this->subartitle = (isset($data['subartitle'])) ? $data['subartitle'] : null;
        $this->image = (isset($data['image'])) ? $data['image'] : null;
        $this->content = (isset($data['content'])) ? $data['content'] : null;
        $this->arcontent = (isset($data['arcontent'])) ? $data['arcontent'] : null;
        $this->country = (isset($data['country'])) ? $data['country'] : null;
        $this->user = (isset($data['user'])) ? $data['user'] : null;
        $this->news_date = (isset($data['news_date'])) ? $data['news_date'] : date('Y-m-d');
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
                'name' => 'image',
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

<?php

/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZfcUserOver\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ZfcUser\Entity\UserInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements UserInterface,InputFilterAwareInterface {

    protected $inputFilter;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true,  length=255)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $displayName;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $photo;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    protected $password;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $state;
    
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $isAdmin;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $facebook;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $instagram;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $twitter;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $livestream;
    
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $content;
    
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $arcontent;
    
    
    /**
     * @ORM\Column(type="string")
    */
    protected $subtitle;

    /**
     * @ORM\Column(type="string")
     */
    protected $subartitle;
    
    /**
     * Initialies the roles variable.
     */
    public function __construct() {
        $this->roles = new ArrayCollection();
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
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return void
     */
    public function setId($id) {
        $this->id = (int) $id;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return void
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return void
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName() {
        return $this->displayName;
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     *
     * @return void
     */
    public function setDisplayName($displayName) {
        $this->displayName = $displayName;
    }

    /**
     * Get photo.
     *
     * @return string
     */
    public function getPhoto() {
        return $this->photo;
    }
            
    /**
     * Set photo.
     *
     * @param string $photo
     *
     * @return void
     */
    public function setPhoto($photo) {
        $this->photo = $photo;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return void
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return void
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * Get isAdmin.
     *
     * @return int
     */
    public function getIsAdmin() {
        return $this->isAdmin;
    }

    /**
     * Set isAdmin.
     *
     * @param int $isAdmin
     *
     * @return void
     */
    public function setIsAdmin($isAdmin) {
        $this->isAdmin = $isAdmin;
    }
    
    function getContent() {
        return $this->content;
    }

    function getArcontent() {
        return $this->arcontent;
    }
    
    function setContent($content) {
        $this->content = $content;
    }

    function setArcontent($arcontent) {
        $this->arcontent = $arcontent;
    }
    
    function getSubtitle() {
        return $this->subtitle;
    }

    function getSubartitle() {
        return $this->subartitle;
    }

    function setSubtitle($subtitle) {
        $this->subtitle = $subtitle;
    }

    function setSubartitle($subartitle) {
        $this->subartitle = $subartitle;
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
    /*public function getArrayCopy() {
        return get_object_vars($this);
    }*/
    
    /**
     * @param array $data
     */
    public function exchangeArray($data = array()) {
        $this->id = $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->displayName = $data['displayName'];
        $this->photo = $data['photo'];
        $this->password = $data['password'];
        $this->facebook =  (isset($data['facebook'])) ? $data['facebook'] : null;
        $this->instagram =  (isset($data['instagram'])) ? $data['instagram'] : null;
        $this->twitter =  (isset($data['twitter'])) ? $data['twitter'] : null;
        $this->livestream =  (isset($data['livestream'])) ? $data['livestream'] : null;
        $this->content = (isset($data['content'])) ? $data['content'] : null;
        $this->arcontent = (isset($data['arcontent'])) ? $data['arcontent'] : null;
        $this->subartitle = (isset($data['subartitle'])) ? $data['subartitle'] : null;
        $this->subtitle = (isset($data['subtitle'])) ? $data['subtitle'] : null;
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

            $inputFilter->add(array(
                'name' => 'displayName',
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
                'name' => 'photo',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 7,
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'passwordconfirm',
                'required' => true,
                'filters' => array(),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 7,
                            'max' => 100,
                        ),
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password',
                            'messages' => array(
                            \Zend\Validator\Identical::NOT_SAME => 'The password is mis matched'
                            )
                        )
                    ),
                ),
            ));
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    public function getInputFilterForEdit() {
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

            $inputFilter->add(array(
                'name' => 'displayName',
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
                'name' => 'photo',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'password',
                'required' => false,
                'filters' => array(),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 7,
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'passwordconfirm',
                'required' => false,
                'filters' => array(),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 7,
                            'max' => 100,
                        ),
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password',
                            'messages' => array(
                            \Zend\Validator\Identical::NOT_SAME => 'The password is mis matched'
                            )
                        )
                    ),
                ),
            ));
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
    public function getInputFilterForSettings() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
    public function getInputFilterForProfile() {
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

            $inputFilter->add(array(
                'name' => 'displayName',
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
                'name' => 'photo',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'password',
                'required' => false,
                'filters' => array(),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 7,
                            'max' => 100,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'passwordconfirm',
                'required' => false,
                'filters' => array(),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 7,
                            'max' => 100,
                        ),
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password',
                            'messages' => array(
                            \Zend\Validator\Identical::NOT_SAME => 'The password is mis matched'
                            )
                        )
                    ),
                ),
            ));
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}

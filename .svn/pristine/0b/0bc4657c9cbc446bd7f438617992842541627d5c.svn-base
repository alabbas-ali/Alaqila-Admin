<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RoleAssignment
 *
 * @author abass
 */
namespace ZfcUserOver\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="role_assignment")
 *
 */
class RoleAssignment {
    /** 
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    public $id;
    /** @ORM\Column(type="string") */
    public $context;
    /** @ORM\Column(type="integer") */
    public $userid;
    /** @ORM\Column(type="integer") */
    public $instanceid;
    
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
        $this->context = $data['context'];
        $this->userid = $data['userid'];
        $this->instanceid = $data['instanceid'];
    }
}

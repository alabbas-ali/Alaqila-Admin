<?php

namespace ZfcUserOver\Service;

use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Crypt\Password\Bcrypt;
use Zend\Stdlib\Hydrator;
use ZfcBase\EventManager\EventProvider;
use ZfcUser\Mapper\UserInterface as UserMapperInterface;
use ZfcUser\Options\UserServiceOptionsInterface;

use ZfcUser\Service\User as userServ;

class User extends userServ
{

    /**
     * change the current users password
     *
     * @param array $data
     * @return boolean
     */
    public function getnewpass($data)
    {
        $bcrypt = new Bcrypt;
        $bcrypt->setCost($this->getOptions()->getPasswordCost());
        $pass = $bcrypt->create($data);

        return $pass;
    }

    
}

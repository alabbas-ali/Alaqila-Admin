<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CountriesRepository
 *
 * @author abass
 */

namespace Countries\Repository;

use Doctrine\ORM\EntityRepository;

class CountriesRepository extends EntityRepository {

    public function getMyCountries($userid) {
        /*$querybuilder = $this->createQueryBuilder('c');
        return $querybuilder->select('c')
                        ->orderBy('c.id', 'DESC')
                        ->getQuery()->getResult();*/
        $querybuilder = $this->createQueryBuilder('c');
        $querybuilder->select('c')
        ->leftJoin(
            'ZfcUserOver\Model\RoleAssignment',
            'r',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            "r.instanceid = c.id"
        )
        ->where("r.userid = $userid AND r.context='country'");

    return $querybuilder->getQuery()->getResult();
        
    }

}

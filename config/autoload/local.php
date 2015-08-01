<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    /*'host'     => '144.76.234.101', */
                    'host'     => 'alaqila.tv',
                    'port'     => '3306',
                    'user'     => 'alaq365_zend',
                    'password' => 'no1stand@last',
                    'dbname'   => 'alaq365_zend', 
                    'encoding' => "UTF-8",
                    'charset' => "utf8",
                    /*'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => '',
                    'dbname'   => 'zf2tutorial', */
                )
            )
        )
    ),
    'locale' => array(
        'default' => 'ar_SY',
        'available'     => array(
            'en_US' => 'English',
            'ar_SY' => 'Arabic',
        ),
    ),
 );

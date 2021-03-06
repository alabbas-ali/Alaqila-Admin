<?php

return array(
    'doctrine' => array(
        'driver' => array(
            // overriding zfc-user-doctrine-orm's config
            'zfcuserover_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => __DIR__ . '/../src/ZfcUserOver/Model',
            ),
 
            'orm_default' => array(
                'drivers' => array(
                    'ZfcUserOver\Model' => 'zfcuserover_entity',
                ),
            ),
        ),
    ),
 
    'zfcuser' => array(
        // telling ZfcUser to use our own class
        'user_entity_class'       => 'ZfcUserOver\Model\User',
        // telling ZfcUserDoctrineORM to skip the entities it defines
        'enable_default_entities' => false,
    ),
    
    'controllers' => array(
        'invokables' => array(
            'ZfcUserOver\Controller\Userprofile' => 'ZfcUserOver\Controller\UserprofileController',
            'ZfcUserOver\Controller\Users' => 'ZfcUserOver\Controller\UsersController',
            'ZfcUserOver\Controller\Permissions' => 'ZfcUserOver\Controller\PermissionsController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'Users' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/Users[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ZfcUserOver\Controller\Users',
                        'action' => 'index',
                    ),
                ),
            ),
            'Userprofile' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/Userprofile[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ZfcUserOver\Controller\Userprofile',
                        'action' => 'index',
                    ),
                ),
            ),
            'Permissions' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/permissions[/:context][/:action][/:id][/:userid]',
                    'constraints' => array(
                        'context' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'userid' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ZfcUserOver\Controller\Permissions',
                        'action' => 'index',
                    ),
                ),
            )
            
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'zfc-user' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
         ),
    ),
);

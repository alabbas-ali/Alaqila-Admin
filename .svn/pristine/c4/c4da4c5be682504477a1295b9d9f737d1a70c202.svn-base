<?php
namespace Settings;

return array(
     'controllers' => array(
         'invokables' => array(
             __NAMESPACE__.'\Controller\\' .__NAMESPACE__ => __NAMESPACE__.'\Controller\SettingsController',
         ),
     ),
    
    //'form_elements' => array(
    //    'factories' => array(
    //        'Settings\Form\SettingsForm' => 'Settings\Form\Factory\SettingsFormFactory',  
     //   ),
    //),
    
    // The following section is new and should be added to your file
     'router' => array(
         'routes' => array(
             'Settings' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/Settings[/:action]/[:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Settings\Controller\Settings',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),

     'view_manager' => array(
         'template_path_stack' => array(
             'Settings' => __DIR__ . '/../view',
         ),
         'strategies' => array(
            'ViewJsonStrategy',
         ),
     ),
    
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Model')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Model' => __NAMESPACE__ . '_driver'
                )
            )
        )
    )
 );
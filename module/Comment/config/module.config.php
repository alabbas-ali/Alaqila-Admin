<?php
namespace Comment;

return array(
     'controllers' => array(
         'invokables' => array(
             __NAMESPACE__.'\Controller\\' .__NAMESPACE__ => __NAMESPACE__.'\Controller\CommentController',
         ),
     ),
    
    //'form_elements' => array(
    //    'factories' => array(
    //        'Comment\Form\CommentForm' => 'Comment\Form\Factory\CommentFormFactory',  
     //   ),
    //),
    
    // The following section is new and should be added to your file
     'router' => array(
         'routes' => array(
            'Comment' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/Comment[/:action]/[:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Comment\Controller\Comment',
                         'action'     => 'index',
                     ),
                 ),
             ),
            'viewComments' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/Comments[/:action]/[:type]/[:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'type' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Comment\Controller\Comment',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),

     'view_manager' => array(
         'template_path_stack' => array(
             'Comment' => __DIR__ . '/../view',
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
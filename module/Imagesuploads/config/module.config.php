<?php

namespace Imagesuploads;

return array(
    'controllers' => array(
        'invokables' => array(
            __NAMESPACE__ . '\Controller\\' . __NAMESPACE__ => __NAMESPACE__ . '\Controller\ImagesuploadsController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'Imagesuploads' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/Imagesuploads[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\\' . __NAMESPACE__,
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'upload-progress' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/upload-progress',
                            'defaults' => array(
                                'controller' => __NAMESPACE__ . '\Controller\\' . __NAMESPACE__,
                                'action' => 'uploadProgress',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __NAMESPACE__ => __DIR__ . '/../view',
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

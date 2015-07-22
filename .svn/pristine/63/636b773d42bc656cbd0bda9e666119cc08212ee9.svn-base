<?php

$settings = array(
    'user_entity_class' => 'ZfcUserOver\Model\User',
    'enable_username' => true,
    'login_redirect_route' => 'home',
        //'auth_adapters' => array(100 => 'ZfcUser\Authentication\Adapter\Db'),
        //'enable_display_name' => true,
        //'auth_identity_fields' => array( 'email' ),
        //'login_form_timeout' => 300,
        //'user_form_timeout' => 300,
        //'login_after_registration' => true,
        //'use_registration_form_captcha' => false,
        /* 'form_captcha_options' => array(
          'class'   => 'figlet',
          'options' => array(
          'wordLen'    => 5,
          'expiration' => 300,
          'timeout'    => 300,
          ),
          ), */
        //'use_redirect_parameter_if_present' => true,
        //'user_login_widget_view_template' => 'zfc-user/user/login.phtml',
        //'logout_redirect_route' => 'zfcuser/login',
        //'password_cost' => 14,
        //'enable_user_state' => true,
        //'default_user_state' => 1,
        //'allowed_login_states' => array( null, 1 ),
        //'table_name' => 'user',
);

return array(
    'zfcuser' => $settings,
    'service_manager' => array(
        'aliases' => array(
            'zfcuser_zend_db_adapter' => (isset($settings['zend_db_adapter'])) ? $settings['zend_db_adapter'] : 'Zend\Db\Adapter\Adapter',
        ),
    ),
);

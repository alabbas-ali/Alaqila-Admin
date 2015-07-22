<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class Module {

    protected $whitelist = array(
        'zfcuser/login/login',
        'Pages/getAllActive',
        'Pages/getPageUsers',
        'News/getAllActive',
        'News/getPublic',
        'News/getByID',
        'News/getByUser',
        'News/getResentNews',
        'News/getHomePublic',
        'News/getUserPublic',
        'News/getAllUserActive',
        'Video/getAllActive',
        'Video/getPublic',
        'Video/getByID',
        'Video/getByUser',
        'Video/getHomePublic',
        'Video/getUserPublic',
        'Video/getAllUserActive',
        'Audio/getAllActive',
        'Audio/getPublic',
        'Audio/getByID',
        'Audio/getByUser',
        'Audio/getHomePublic',
        'Audio/getUserPublic',
        'Audio/getAllUserActive',
        'Photo/getAllActive',
        'Photo/getPublic',
        'Photo/getByID',
        'Photo/getByUser',
        'Photo/getHomePublic',
        'Photo/getUserPublic',
        'Photo/getAllUserActive',
        'Advertisement/getAllActive',
        'viewComments/getActiveComments',
        'Programs/getAllActive',
        'Settings/getAll',
        'Userprofile/getByID',
        'Comment/add',
        'error/404',
    );

    /* public function onBootstrap(MvcEvent $e) {
      $eventManager = $e->getApplication()->getEventManager();
      $moduleRouteListener = new ModuleRouteListener();
      $moduleRouteListener->attach($eventManager);
      } */

    public function onBootstrap(MvcEvent $e) {
        $app = $e->getApplication();
        $em = $app->getEventManager();
        $sm = $app->getServiceManager();

        $list = $this->whitelist;
        $auth = $sm->get('zfcuser_auth_service');
        
        $em->attach(MvcEvent::EVENT_ROUTE, function($e) use ($list, $auth) {
            $match = $e->getRouteMatch();
             
            // No route match, this is a 404
            if (!$match instanceof RouteMatch) {
                return;
            }

            // Route is whitelisted
            $name = $match->getMatchedRouteName().'/'.$match->getParam('action');
            //var_dump($name) ; die();
            if (in_array($name, $list)) {
                return;
            }

            // User is authenticated
            if ($auth->hasIdentity()) {
                return;
            }

            // Redirect to the user login page, as an example
            $router = $e->getRouter();
            $url = $router->assemble(array(), array(
                'name' => 'zfcuser/login'
            ));

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }, -100);
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

}

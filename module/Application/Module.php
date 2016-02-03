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
use Zend\Session\Container;

class Module {

    protected $whitelist = array(
        'zfcuser/login/login',
        'Pages/getAllActive',
        'Pages/getPageUsers',
        'News/getAllActive',
        'News/getAllActiveNew',
        'News/getPublic',
        'News/getByID',
        'News/getByUser',
        'News/getResentNews',
        'News/getHomePublic',
        'News/getUserPublic',
        'News/getAllUserActive',
        'Video/getAllActive',
        'Video/getAllActiveNew',
        'Video/getPublic',
        'Video/getByID',
        'Video/getByUser',
        'Video/getHomePublic',
        'Video/getUserPublic',
        'Video/getAllUserActive',
        'Audio/getAllActive',
        'Audio/getAllActiveNew',
        'Audio/getPublic',
        'Audio/getByID',
        'Audio/getByUser',
        'Audio/getHomePublic',
        'Audio/getUserPublic',
        'Audio/getAllUserActive',
        'Photo/getAllActive',
        'Photo/getAllActiveNew',
        'Photo/getPublic',
        'Photo/getByID',
        'Photo/getByUser',
        'Photo/getHomePublic',
        'Photo/getUserPublic',
        'Photo/getAllUserActive',
        'Advertisement/getAllActive',
        'viewComments/getActiveComments',
        'viewComments/getActiveCommentsNew',
        'Programs/getAllActive',
        'Settings/getAll',
        'Userprofile/getByID',
        'FullAdv/getRandomOne',
        'Comment/add',
        'error/404',
    );

    /* public function onBootstrap(MvcEvent $e) {
      $eventManager = $e->getApplication()->getEventManager();
      $moduleRouteListener = new ModuleRouteListener();
      $moduleRouteListener->attach($eventManager);
      } */

    public function onBootstrap(MvcEvent $e) {
        
        // Just a call to the translator, nothing special!
        $this->initTranslator($e);
        
        
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
    
    protected function initTranslator(MvcEvent $event){
        $serviceManager = $event->getApplication()->getServiceManager();

        // Zend\Session\Container
        $session = New Container('language');

        $translator = $serviceManager->get('translator');
        $translator->setLocale($session->language)
            ->setFallbackLocale('ar_SY');
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

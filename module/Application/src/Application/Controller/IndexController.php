<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{
    public function changeLocaleAction() {
        // New Container will get he Language Session if the SessionManager already knows the language session.
        $session = new Container('language');
        $language = $this->params()->fromRoute('param', 'ar_SY');
        $config = $this->serviceLocator->get('config');
        if (isset($config['locale']['available'][$language])) {
            $session->language = $language;
            $this->serviceLocator->get('translator')->setLocale($session->language);
        }
        $view = new ViewModel();
        $view->setTemplate('application/index/index.phtml'); // path to phtml file under view folder
        return $view;
    }
    public function indexAction() {
        return new ViewModel();
    }
    
    public function getlangusgesAction() {
        $config = $this->serviceLocator->get('config');
        return new JsonModel($config['locale']['available']);
    }
}

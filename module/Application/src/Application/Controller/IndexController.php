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

class IndexController extends AbstractActionController
{
    public function changeLocaleAction() {
        // New Container will get he Language Session if the SessionManager already knows the language session.
        $session = new Container('language');
        $language = $request->getPost()->language;
        $config = $this->serviceLocator->get('config');
        if (isset($config['locale']['available'][$language])) {
            $session->language = $language;
            $this->serviceLocator->get('translator')->setLocale($session->language);
        }
        return new ViewModel();
    }
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function getlangusgesAction() {
        $config = $this->serviceLocator->get('config');
        return new JsonModel($config['locale']['available']);
    }
}

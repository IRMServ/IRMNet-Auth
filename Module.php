<?php

namespace Auth;

use Zend\Mvc\MvcEvent;

class Module {

    public function onBootstrap(MvcEvent $e) {
        $app = $e->getApplication();
        $app->getEventManager()->attach('route', array($this, 'checkACL'), -100);
    }

    public function checkACL($e) {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            return;
        }
        $app = $e->getApplication();
        $locator = $app->getServiceManager();
        $auth = $locator->get('ControllerPluginManager')->get('Permission')->doAuthorization($e, $locator);
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

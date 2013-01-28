<?php

namespace Auth\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;

class Permission extends AbstractPlugin {

    public function doAuthorization($e, $sm) {
        
        $controller = $e->getRouteMatch()->getParam('controller');
        $route = $e->getRouteMatch()->getMatchedRouteName();
        
        $action = $e->getRouteMatch()->getParam('action');
        $auth = new AuthenticationService;
        
        if (!$auth->hasIdentity() && $route != 'login' ) {
            $router = $e->getRouter();
            $url = $router->assemble(array(), array('name' => 'login'));
            $response = $e->getResponse();
            $response->setStatusCode(302);
            $response->getHeaders()->addHeaderLine('Location', $url);
        }
    }

}

?>

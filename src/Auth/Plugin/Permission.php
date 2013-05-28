<?php

namespace Auth\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;

class Permission extends AbstractPlugin {

    public function doAuthorization($e, $sm) {
        
        $controller = $e->getRouteMatch()->getParam('controller');
        $route = $e->getRouteMatch()->getMatchedRouteName();
        
        $action = $e->getRouteMatch()->getParam('action');
        $auth = $sm->get('Auth');
         $convites = $sm->get('CHEAprov');
        if ($convites != 0) {
            
            $userdata = $auth->getStorage()->read();
            $userdata['convites-hora-extra'] = $convites;
            $auth->getStorage()->write($userdata);
        }
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

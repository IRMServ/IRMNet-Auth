<?php

namespace Auth\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Acl,
    Zend\Permissions\Acl\Role\GenericRole as Role,
    Zend\Permissions\Acl\Resource\GenericResource as Resource;

class Permission extends AbstractPlugin {

    public function doAuthorization($e, $sm) {

        $controller = $e->getRouteMatch()->getParam('controller');
        $route = $e->getRouteMatch()->getMatchedRouteName();

        $action = $e->getRouteMatch()->getParam('action');

        $moduleNamespace = substr($controller, 0, strpos($controller, '\\'));

        $auth = $sm->get('Auth');
        $aclconfig = $sm->get('Config');
        $aclrules = $aclconfig['acl'];

        $userdata = $auth->getStorage()->read();
       

        $acl = new Acl();
        $acl->deny();
        $acl->addRole(new Role('guest'));
        $acl->addResource(new Resource('Auth'));
        $acl->addResource(new Resource('TI'));

        $acl->allow('guest', 'Auth', 'Auth\Controller\Index:index');
        $acl->allow('guest', 'Auth', 'Auth\Controller\Index:logout');
        $acl->allow('guest', 'TI', 'TI\Controller\Impressao:importar');

        foreach ($aclrules['Roles'] as $role => $painel) {
            $acl->addRole(new Role($role), 'guest');
        }
        foreach ($aclrules['Resources'] as $resource) {
            $acl->addResource(new Resource($resource));
        }


        if (isset($aclrules[$moduleNamespace])) {
            foreach ($aclrules[$moduleNamespace] as $role => $permission) {
                foreach ($permission as $perm) {
                    $acl->allow($role, $moduleNamespace, $perm);
                }
            }
        }


        $convites = $sm->get('CHEAprov');
        if ($convites != 0) {
            $userdata['convites-hora-extra'] = $convites;
            $auth->getStorage()->write($userdata);
        }


        $controller = $e->getTarget();
        $controllerClass = get_class($controller);
        $moduleName = strtoupper(substr($controllerClass, 0, strpos($controllerClass, '\\')));
        $routeMatch = $e->getRouteMatch();
        $controllerName = $routeMatch->getParam('controller');

        $role = isset($userdata['departamento']) ? strtoupper($userdata['departamento']) : 'guest';

        if (!$acl->isAllowed($role, $moduleNamespace, "{$controllerName}:{$action}")) {

            $application = $e->getApplication();
            $sm = $application->getServiceManager();
            $sharedManager = $application->getEventManager()->getSharedManager();

            $router = $sm->get('router');
            $request = $sm->get('request');



            $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) use ($sm) {
                        $sm->get('ControllerPluginManager')->get('FlashMessenger')
                                ->addMessage("Acesso não autorizado");
                    }, 2
            );


            $router = $e->getRouter();
            $url = $router->assemble(array(), array('name' => 'login'));
            $response = $e->getResponse();
            $response->setStatusCode(302);
            $response->getHeaders()->addHeaderLine('Location', $url);
        }
         if (isset($userdata['displayname'])) {
            $userdata['painel'] = $aclrules['Roles'][$userdata['departamento']];
            $auth->getStorage()->write($userdata);
        }

        
    }

}

?>

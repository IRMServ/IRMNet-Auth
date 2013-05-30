<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Index' => 'Auth\Controller\IndexController'
        ),
    ),
    'acl' => array(
        'Roles' => array(
            'TI' =>'ti',
            'ALMOXARIFADO'=>'almoxarifado',
            'COMPRAS'=>'compras',
            'DIRETORIA'=>'diretoria',
            'FABRICAÇÃO'=>'fabricacao',
            'FINANCEIRO - CONTÁBIL'=>'financeiro',
            'FISCAL'=>'fiscal',
            'LOGÍSTICA'=>'logistica',
            'OCTG - ADMINISTRATIVO'=>'octg',
            'OCTG - INSPEÇÃO'=>'octg',
            'OCTG - MACHINE SHOP'=>'octg',
            'OCTG - VÁLVULAS'=>'octg',
            'OPERACIONAL - OFFSHORE'=>'operacional',
            'PROJETOS ESPECIAIS'=>'projetos-especiais',
            'QUALIDADE'=>'qualidade',
            'RELATÓRIO'=>'relatorio',
            'RH'=>'rh',
            'RH - ADP'=>'dp',
            'RH - GT'=>'rh',
            'RH - T&D'=>'td',
            'SMS'=>'sms',
            'TRANSPORTE'=>'logistica',
        ),
        'Resources'=>array(
            'DP',
            'Helpdesk',           
            'Base',
            'TI',
            'ProjetosEspeciais',
            'RH',
            'MailService',
            'Relatorio',
            'Qualidade',
            'Planejamento',
            'Operacional',
            'OCTG',
            'Logistica',
            'Juridico',
            'HSE',
            'Fiscal',
            'Financeiro',
            'Fabricacao',
            'Diretoria',
            'Controladoria',
            'Contabilidade',
            'Compras',
            'Almoxarifado',
        )
    ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Index',
                        'action' => 'logout',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Ldap' => function($sm) {
                $config = include __DIR__ . '/ldap.config.php';
                unset($config['log_path']);
                $ldap = new \Zend\Ldap\Ldap($config['server']);
                $ldap->bind();
                return $ldap;
            },
            'Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'UsersPair' => function($sm) {
                $ldap = $sm->get('Ldap');
                $config = include 'ldap.config.php';
                $f2 = \Zend\Ldap\Filter::equals('objectCategory', 'person');
                $f3 = \Zend\Ldap\Filter::equals('objectClass', 'user');
                $f5 = \Zend\Ldap\Filter::equals('useraccountcontrol', '66048');
                $f4 = \Zend\Ldap\Filter::andFilter($f2, $f3, $f5);

                $result = $ldap->search($f4, $config['server']['baseDn'], \Zend\Ldap\Ldap::SEARCH_SCOPE_SUB);

                $users = array();
                $noArray = array('Germano', 'Funcionário', 'teste', 'WKRADAR', 'Recepcao', 'Dswk', 'Root');
                foreach ($result as $item) {
                    if (!in_array($item['displayname'][0], $noArray)) {
                        $users[$item['displayname'][0]] = $item['displayname'][0];
                    }
                }
                return $users;
            },
        ),
        'services' => array(
            'Auth' => new \Zend\Authentication\AuthenticationService()
        )
    ),
    'ldap-config' => include __DIR__ . '/ldap.config.php',
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => realpath(__DIR__ . '/../../base/view/layout/layout.phtml'),
            'application/index/index' => __DIR__ . '/../../base/view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host' => 'localhost',
                    'port' => '3306',
                    'user' => '',
                    'password' => '',
                    'dbname' => 'zf2tutorial',
                )
            )
        )
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'Permission' => 'Auth\Plugin\Permission',
            'FilterMemberOf' => 'Auth\Plugin\FilterMemberOf',
        )
    ),
    'navigation' => array(
// The DefaultNavigationFactory we configured in (1) uses 'default' as the sitemap key
        'default' => array(
// And finally, here is where we define our page hierarchy
            'auth' => array(
                'label' => 'Sair',
                'route' => 'logout',
            ),
        ),
    ),
);

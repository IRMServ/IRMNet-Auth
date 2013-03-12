<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Index' => 'Auth\Controller\IndexController'
        ),
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

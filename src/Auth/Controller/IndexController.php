<?php

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Form\LoginInit;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\Ldap as AuthAdapter;
use Zend\Config\Config;

class IndexController extends AbstractActionController {

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction() {
        $form = new LoginInit;

        $ldapconfig = $this->getServiceLocator()->get('Config');

        $view = array();

        $view['form'] = $form;

        $ldap = $this->getServiceLocator()->get('Ldap');
        
        $messages = $this->flashMessenger()->getMessages();
        $view['messages'] = $messages;
        $config = new Config($ldapconfig['ldap-config'], true);
        $options = (array) $config->toArray();

        if ($this->getRequest()->isPost()) {

            $login = $this->getRequest()->getPost('login');
            $senha = $this->getRequest()->getPost('senha');

            $auth = $this->getServiceLocator()->get('Auth');

            unset($options['log_path']);

            $adapter = new AuthAdapter((array) $options, $login, $senha);

            $result = $auth->authenticate($adapter);
            $messages = $result->getMessages();

            $errors = array();

            if (end(explode(' ', $messages[3])) === 'successful') {
                $result = $ldap->search("(samaccountname={$login})", $config->server->baseDn, \Zend\Ldap\Ldap::SEARCH_SCOPE_SUB);
                $userdata = array();
                foreach ($result as $item) {

                    $userdata['displayname'] = $item['displayname'][0];
                    $userdata['email'] = $item['mail'][0];
                    $userdata['departamento'] = $item['department'][0];

                    if(isset($item['manager']))
                    {
                        $q = explode(',', $item['manager'][0]);
                    $z = explode('=', $q[0]);
                    $result2 = $ldap->search("{$q[0]}", $config->server->baseDn, \Zend\Ldap\Ldap::SEARCH_SCOPE_SUB);
                    foreach ($result2 as $item2) {

                        $userdata['gerente-mail'] = $item2['mail'][0];
                    }
                    $userdata['gerente'] = $z[1];
                    }
                }
                $auth->getStorage()->write($userdata);

                $userdata['convites-hora-extra'] = $this->getServiceLocator()->get('CHEAprov');

                $auth->getStorage()->write($userdata);


                return $this->redirect()->toRoute('home');
            } else {


                $view['messages'] = reset(explode(':', $messages[3]));
            }
        }

        return new ViewModel($view);
    }

    public function logoutAction() {
        $auth = new AuthenticationService();
        $auth->getStorage()->clear();
        return $this->redirect()->toRoute('home');
    }

}

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
                    $userdata['displayname'] = $item['displayname'];
                    $filter = $this->FilterMemberOf();
                    $userdata['memberof'] = $filter->filter($item['memberof']);
                    $auth->getStorage()->write($userdata);
                }
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

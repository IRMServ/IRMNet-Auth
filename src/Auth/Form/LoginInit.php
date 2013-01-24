<?php
namespace Auth\Form;
use Zend\Form\Form;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LoginInit
 *
 * @author icarvalho
 */
class LoginInit extends Form {
   
    public function __construct($name = null) {
        parent::__construct('logininit');
        $this->setAttribute('method', 'post');
        $this->login();
        $this->senha();
        $this->submit();
    }
    
    public function login(){
       $this->add(array(
            'name' => 'login',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'User : ',
            ),
        ));
    }
    public function senha(){
        $this->add(array(
            'name' => 'senha',
            'attributes' => array(
                'type'  => 'password',
            ),
            'options' => array(
                'label' => 'Password : ',
            ),
        ));
    }
    public function submit(){
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Log In',
                'id' => 'submitbutton',
                'class'=>'btn btn-success'
                ),
        ));
    }
}

?>

<?php

Core::depends('Controller');

/**
 * Description of AppController
 *
 * @author usuario
 */
abstract class AppController extends Controller {
  
  protected $auth = false;
  
  public function beforeExecute() {
    
    if ($this->auth === true) {
      $in_login = strpos($this->url, 'login') === 0;

      if ($in_login && $this->logged) {
        // ir para home
        $this->response->redirect('');
        return false;
      } elseif (!$in_login && !$this->logged) {
        // ir para login
        $this->response->redirect('login');
        return false;
      }
    }
    
    return parent::beforeExecute();
  }
  
}
<?php

Core::uses('Request', 'core/network');
Core::uses('Response', 'core/network');

class Controller {
  
  public $request;
  public $response;
  protected $session;
  
  protected $logged = null;
  protected $ip = null;
  protected $token = null;
  protected $user;
  
  public function __construct() {
    $this->session =& $_SESSION;
   
    $this->request = new Request();
    $this->response = new Response();
    
    $this->ip = $this->request->clientIp();
    $this->user = $this->session['user'];
    $this->logged = is_object($this->user) && $this->user->id > 0;
    $this->token = $this->session['token'];
  }
  
  public function beforeExecute() {
    return true;
  }
  
  public function afterExecute() {
    return;
  }
  
}
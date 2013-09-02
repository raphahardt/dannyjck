<?php

Core::depends('Request');
Core::depends('Response');

class ControllerException extends CoreException {};

abstract class Controller {
  
  public $request;
  public $response;
  protected $session;
  
  protected $logged = null;
  protected $ip = null;
  protected $token = null;
  protected $user;
  protected $url;
  
  public function __construct($url) {
    global $Session;
    
    if (isset($Session))
      $this->session = &$Session;
    else {
      Core::depends('Session');
      $this->session = new Session();
    }
   
    $this->request = new Request();
    $this->response = new Response();
    
    $this->ip = $this->request->clientIp();
    if (!isset($this->user))
      $this->user = $_SESSION[SESSION_USER_NAME];
    
    $this->logged = is_object($this->user) && $this->user->id > 0;
    $this->token = $_SESSION[SESSION_TOKEN_NAME];
    $this->url = $url;
  }
  
  public function beforeExecute() {
    return true;
  }
  
  public function afterExecute() {
    return;
  }
  
  abstract function index();
  
}
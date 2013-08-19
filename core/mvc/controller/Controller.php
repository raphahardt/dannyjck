<?php

Core::depends('Request');
Core::depends('Response');

abstract class Controller {
  
  public $request;
  public $response;
  protected $session;
  
  protected $logged = null;
  protected $ip = null;
  protected $token = null;
  protected $user;
  
  public function __construct() {
    $this->session = new Session();
   
    $this->request = new Request();
    $this->response = new Response();
    
    $this->ip = $this->request->clientIp();
    $this->user = $_SESSION['user'];
    $this->logged = is_object($this->user) && $this->user->id > 0;
    $this->token = $_SESSION['token'];
  }
  
  public function beforeExecute() {
    return true;
  }
  
  public function afterExecute() {
    return;
  }
  
  abstract function index();
  
}
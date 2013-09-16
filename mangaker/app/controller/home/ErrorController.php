<?php

Core::uses('AppController', 'controller');

/**
 * Description of HomeController
 *
 * @author usuario
 */
class ErrorController extends AppController {
  
  function index() {
    echo 'ERRO 404';
    echo $this->request->referer();
  }
  
}
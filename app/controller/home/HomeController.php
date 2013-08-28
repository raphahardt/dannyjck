<?php

Core::uses('AppController', 'controller');

/**
 * Description of HomeController
 *
 * @author usuario
 */
class HomeController extends AppController {
  
  function index() {
    
    Core::dump();
    echo '<pre style="background:#fcc; padding:20px;">';
    foreach(glob('C:\\session\\*') as $f) {
      echo $f.'<br>';
    }
    echo '</pre>';
    
    //Core::uses('AppView', 'view');
    /*$view = new AppView('home/index.tpl');
    $view->addJSVar('teste', '123ação');
    $view->addJSVar('teste2', 123);
    $view->addJSVar('teste3', array('a', 'b', 3, null, 'ação'));
    $view->addJSVar('teste4', array('teste'=>'a', 'b', 3, null, 'ção'));
    $view->render();*/
    
    /*echo SITE_DOMAIN;
    echo $this->request->referer().'<br>';
    echo 'index';
    //echo '<img src="imagem.jpg" />';
    echo '<a href="add/">add</a>';
    $this->session['teste'] = 'aaa';
    echo '<pre style="background:#ddd">';
    //var_dump($this->session['teste']);
    
    Core::uses('SessionModel', 'model/session');
    
    $s = new SessionModel();
    //$s->setFilterValues('0-d19e796e25333eae1b58258bfa5c4ef7-2130706433-5');
    //$s->setFilterValues('gnfudignfduigd');
    $s->select();
    var_dump($s['sid']);
    
    echo '</pre>';*/
  }
  
  function favicon() {
    $favicon = DJCK.DS.'favicon.ico';
    
    $this->response->cache(filemtime($favicon), '+4 years');
    $this->response->type('ico');
    $modified = $this->response->checkNotModified($this->request);
    
    if (!$modified) {
      readfile($favicon);
    }
  }
  
  function add() {
    //$this->response->cache(mktime(0,0,0, 6, 8, 2013), time()+5);
    //$this->response->checkNotModified($this->request);
    echo $this->request->referer().'<br>';
    echo '<a href="../">home</a>';
    echo env('HTTP_REFERER').'<br>';
    echo '<img src="../imagem.jpg" />';
    echo 'add22';
  }
  
  function edit() {
    echo 'edit #'.$this->request->params[':id'];
    print_r($this->request->params);
  }
  
  
  function imagem() {
    
    if ($this->request->referer() != SITE_FULL_URL.'/') {
      //$this->response->statusCode(404);
      // Create a 100*30 image
      $im = imagecreate(500, 30);

      // White background and blue text
      $bg = imagecolorallocate($im, 255, 255, 255);
      $textcolor = imagecolorallocate($im, 0, 0, 255);

      // Write the string at the top left
      imagestring($im, 3, 0, 0, $this->request->referer(), $textcolor);
      imagestring($im, 3, 0, 10, SITE_FULL_URL.'/', $textcolor);

      // Output the image
      $this->response->type('png');

      imagepng($im);
      imagedestroy($im);
      //echo 'erro';
      return;
    }
    
    $this->response->cache(mktime(0,0,0, 6, 8, 2013), '+1 day');
    $this->response->type('jpg');
    $modified = $this->response->checkNotModified($this->request);
    
    if (!$modified) {
      readfile(DJCK.DS.'123.jpg');
    }
  }
  
}
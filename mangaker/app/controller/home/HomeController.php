<?php

Core::uses('AppController', 'controller');
Core::uses('AppView', 'view');

/**
 * Description of HomeController
 *
 * @author usuario
 */
class HomeController extends AppController {
  
  public $default_path = 'home';
  
  function index() {
    
    $partners = array();
    $partners[] = array(
        'title' => 'Conexão Nanquim',
        'description' => 'Almanaque digital de histórias em quadrinhos publicado desde março de 2012. Neste 1 ano e meio de publicação, mais de 40 títulos passaram pela revista, desde séries a histórias fechadas, abrindo a oportunidade para novos quadrinistas publicarem seus trabalhos.',
        'image' => SITE_URL. '/public/img/p-cn.png',
        'background_color' => '#e31c1d',
        'site' => 'www.conexaonanquim.com.br',
    );
    
    $partners[] = array(
        'title' => 'Mangá Pride',
        'description' => 'Uma revista sobre o universo otaku em geral. Você encontrará materias sobre games, animes, mangás e muito mais, tudo isso em linguagem simples e muito fácil de entender. Seja bem vindo ao mundo Mangá Pride!',
        'image' => SITE_URL. '/public/img/p-mp.png',
        'background_color' => '#faf4f4',
        'site' => 'www.mangapride.com.br',
    );
    $partners[] = array(
        'title' => 'Zinext',
        'description' => 'A Zinext é um movimento de quadrinho alternativo que surgiu em 2006 como o nosso e que pública revistas alternativas sempre procurando apresentar novas histórias e perspectivas sobre o mundo dos quadrinhos.',
        'image' => SITE_URL. '/public/img/p-zinext.png',
        'background_color' => '#431e74',
        'site' => 'revistazinext.wix.com/zinext',
    );
    
    $partners[] = array(
        'title' => 'Art Here',
        'description' => 'No blog você encontrará o melhor conteúdo em games, animação, quadrinhos e arte em geral. Além de notícias e lançamentos, você também encontrará materiais ensinando diversas técnicas usadas por profissionais do mundo todo para ajudar no seu aprendizado.',
        'image' => SITE_URL. '/public/img/p-arthere.png',
        'background_color' => '#bf7a0c',
        'site' => 'www.artehere.com',
    );
    
    Core::uses('AppView', 'view');
    
    $view = new AppView('home/index.tpl');
    
    // variables
    $view->assign('partners', $partners);
    
    $view->render();
    
  }
  
  function upload(AppView $view) {
    
    $view->render();
    
  }
  
  function favicon() {
    $favicon = ROOT.DS.'favicon.ico';
    
    $this->response->cache(filemtime($favicon), '+4 years');
    $this->response->type('ico');
    $modified = $this->response->checkNotModified($this->request);
    
    if (!$modified) {
      readfile($favicon);
    }
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
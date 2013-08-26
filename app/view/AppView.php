<?php

/**
 * Description of AppView
 *
 * @author Rapha e Dani
 */
class AppView extends View {
  
  public function __construct($view, $ajax = false) {
    
    // define os icones padroes
    $this->addIcon(SITE_URL.'/images/icon144.png', 'apple-touch-icon-precomposed', '144x144');
    $this->addIcon(SITE_URL.'/images/icon144.png', 'apple-touch-icon-precomposed', '144x144');
    $this->addIcon(SITE_URL.'/images/icon144.png', 'apple-touch-icon-precomposed', '144x144');
    $this->addIcon(SITE_URL.'/images/icon144.png', 'apple-touch-icon-precomposed', '144x144');
    
    parent::__construct($view, $ajax);
  }
  
}

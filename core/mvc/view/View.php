<?php

Core::import('Smarty', 'plugin/smarty');

/**
 * Description of View
 *
 * @author Rapha e Dani
 */
abstract class View extends Smarty {
  
  // pagina que será exibida
  private $view = array();
  private $breadcrumbs = array();
  private $title = '';
  private $vars = array();
  private $js_vars = array();
  
  public function __construct($view) {
    // inicia o smarty normalmente
    parent::__construct();

    // define os padrões
    $this->debugging = false;
    $this->caching = false;
    $this->cache_lifetime = 120;

    $this->setTemplateDir(APP_PATH .DS. 'view');
    $this->setCompileDir(TEMP_PATH .DS.'smarty'.DS. 'templates_c');
    $this->setCacheDir(TEMP_PATH .DS.'smarty'.DS. 'cache');
    $this->setConfigDir(PLUGIN_PATH.DS.'smarty'.DS. 'config');
    
    // definicoes principais
    $this->assign('site', array(
        'title' => SITE_TITLE,
        'copyright' => SITE_COPYRIGHT,
        'description' => SITE_DESCRIPTION,
        'keywords' => SITE_KEYWORDS,
        'owner' => SITE_OWNER,
        'URL' => SITE_URL,
        'fullURL' => SITE_FULL_URL,
        
        'token' => DJCK_TOKEN,
    ));
    
    // compile the main css
    /*$less = new lessc();
    $less->setVariables(array(
      "siteURL" => '"'.DJCK_SITE_URL.'"'
    ));
    $less->checkedCompile( DJCK_BASE . 'www'.DS.'css'.DS. 'main.less', DJCK_BASE . 'www'.DS.'css'.DS.'main.css');*/
    $this->addBreadcrumb('Pagina inicial', '');
    
    $this->view = array(
        'template.tpl'
    );

    // pasta padrao que o smarty vai buscar as paginas
    $this->view[] = $view;
  }
  
  protected function beforeRender() {
    return true;
  }
  
  protected function afterRender() {
    return true;
  }
  
  protected function render($supress_header = false) {

    try {
      
      $before = $this->beforeRender();
      
      if ($before === false) {
        return;
      }
      
      // define variaveis
      $this->assign('view', 
        array_merge(
          array( 'title' => $this->title ),
          $this->vars
        )
      ); //titulo
      
      if (!empty($this->js_vars) && is_array($this->js_vars)) { // vars javascript
        $this->assign('varsjs', $this->js_vars);
      }
      if (is_array($this->breadcrumbs) && count($this->breadcrumbs) > 1) { //breadcrumb
        $bread = array();
        foreach ($this->breadcrumbs as $t => $b) {
          $bread[] = array('title' => $t, 'url' => $b);
        }
        $this->assign('breadcrumb', $bread);
      }

      // mostra o conteudo da pagina
      if (count($this->view) > 1)
        $this->display('extends:'.implode('|', $this->view));
      else
        $this->display($this->view[0]);

    } catch (SmartyException $e) {
      throw $e;
    }
  }
  
  protected function addBreadcrumb($title, $url) {
    $this->breadcrumbs[$title] = $url;
  }
  
  protected function setVar($title, $url) {
    $this->vars[$title] = $url;
  }
  
  protected function getVar($title) {
    return $this->vars[$title];
  }
  
}
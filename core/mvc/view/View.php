<?php

Core::import('Smarty', 'plugin/smarty');

/**
 * Description of View
 *
 * @author Rapha e Dani
 */
class View extends Smarty {
  
  // pagina que será exibida
  private $view = array();
  private $breadcrumbs = array();
  private $icons = array();
  private $title = '';
  private $vars = array();
  private $js_vars = array();
  
  public function __construct($view, $ajax = false) {
    // inicia o smarty normalmente
    parent::__construct();

    // define os padrões
    $this->debugging = false;
    $this->caching = false;
    $this->cache_lifetime = 5;

    $this->setTemplateDir(array(
        APP_PATH .DS. 'view',
        DJCK . DS. 'public' .DS. 'tmpl'.DS.'components',
        ROOT . DS. 'public' .DS. 'tmpl'.DS.'components',
    ));
    $this->setCompileDir(TEMP_PATH .DS.'smarty'.DS. 'templates_c');
    $this->setCacheDir(TEMP_PATH .DS.'smarty'.DS. 'cache');
    $this->setConfigDir(PLUGIN_PATH.DS.'smarty'.DS. 'config');
    $this->setPluginsDir(array(
        PLUGIN_PATH.DS.'smarty'.DS. 'plugins'
        // TODO: colocar uma pasta só para os novos plugins pro smarty (ou não, continuar
        // deixando eles na pasta plugins dentro da pasta plugins/smarty
    ));
    
    // definicoes principais
    $this->assign('site', array(
        'title' => SITE_TITLE,
        'subtitle' => SITE_SUBTITLE,
        'copyright' => SITE_COPYRIGHT,
        'description' => SITE_DESCRIPTION,
        'keywords' => SITE_KEYWORDS,
        'owner' => SITE_OWNER,
        'URL' => SITE_URL,
        'fullURL' => SITE_FULL_URL,
        'domain' => SITE_DOMAIN,
        
        'charset' => SITE_CHARSET,
        
    ));
    
    // favicon
    $this->addIcon(SITE_URL.'/favicon.ico', 'icon');
    
    // breadcrumb inicial
    $this->addBreadcrumb('Pagina inicial', '');
    
    // variaveis relativas a cookie
    $this->addJSVar('C', array(
        'd'=>COOKIE_DOMAIN, // domain
        'p'=>COOKIE_PATH // path
    ));
    
    // token
    if (defined('TOKEN')) {
      $this->addJSVar('T', TOKEN);
      $this->setVar('token', TOKEN);
    }
    
    $this->view = array();
    $this->view[] = $ajax ? 'skin_ajax.tpl' : 'skin.tpl';
    $this->view[] = 'template.tpl';

    // pasta padrao que o smarty vai buscar as paginas
    $this->view[] = $view;
  }
  
  protected function beforeRender() {
    return true;
  }
  
  protected function afterRender() {
    return true;
  }
  
  public function render() {

    try {
      
      $before = $this->beforeRender();
      
      if ($before === false) {
        return;
      }
      
      // define variaveis
      $vars = (array)$this->vars;
      if ($this->title) $vars['title'] = $this->title;
      
      if (!empty($this->js_vars) && is_array($this->js_vars)) { // vars javascript
        $vars['js_vars'] = $this->js_vars;
      }
      if (is_array($this->breadcrumbs) && count($this->breadcrumbs) > 1) {
        $vars['breadcrumb'] = $this->breadcrumbs;
      }
      
      $vars['icons'] = $this->icons;
      
      // vars
      $this->assign('view', $vars);
      
      // mostra pagina compilada
      $this->loadFilter('output', 'trimwhitespace');

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
    $this->breadcrumbs[] = array(
        'title' => $title,
        'url' => $url,
    );
  }
  
  protected function addIcon($file, $type, $sizes = null) {
    $this->icons[] = array(
        'file' => $file,
        'sizes' => $sizes, 
        'type' => $type,
    );
  }
  
  /**
   * Define uma variavel global para ser usada no Javascript da página
   * @param string $var Nome da variável
   * @param mixed $value Valor da variável, já formatada no padrão JS
   * @param boolean $raw Se TRUE, irá imprimir o valor "como ele está", sem formatação
   */
  public function addJSVar($var, $value, $raw = false) {
    if (is_string($value) && !$raw) {
      $value = "'$value'";
    } elseif (is_array($value)) {
      $value = json($value);
    }
    $this->js_vars[] = array(
        'name' => $var,
        'value' => $value,
    );
  }

  public function setVar($title, $val) {
    $this->vars[$title] = $val;
  }
  
  public function getVar($title) {
    return $this->vars[$title];
  }
  
}
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('CORE_PATH'))
  define('CORE_PATH', DJCK.DS.'core');

if (!defined('APP_PATH'))
  define('APP_PATH', DJCK.DS.'app');

if (!defined('PLUGIN_PATH'))
  define('PLUGIN_PATH', DJCK.DS.'plugins');

/**
 * Description of Core
 *
 * @author Rapha e Dani
 */
abstract class Core {
  
  private static $imported = array();
  private static $classes = array();

  /**
   * Tipo de arquivo
   *    - class => são as classes que do core que são próŕias do sistema
   *    - file => são arquivos que estão disponíveis na pasta de download e upload
   *    - model => Carrega os modelo que será usado na aplicação já instancia a classe pai AppModel.class.php
   *    - controller => Carrega o controller que será usado na aplicação já instancia a classe pai AppController.class.php
   *    - view => Carrega a view(template) que sera usado
   *    - helper => Carrega o helpper que sera usado
   * @var type tipo do arquivo que sera carregado
   */
  public static $types = array(
      'core' => array('core' => true),
      'model' => array('core' => false, 'path' => 'model'),
      'view' => array('core' => false, 'path' => 'view'),
      'controller' => array('core' => false, 'path' => 'controller'),
      'plugin' => array('plugins' => true),
      'file' => array('root' => true),
  );
  
  static function uses() {
    
  }
  
  // retorna o caminho correto
  static function path($path) {
    
    $parts = explode('/', $path);
    $type = array_shift($parts);
    
    // pega o path do tipo e acrescenta no inicio do caminho do arquivo
    if (self::$types[$type]['path']) {
      $path_ = explode('/', (string)self::$types[$type]['path']);
      $parts = array_merge($path_, $parts);
      unset($path_);
    }
    
    // caminho absoluto para o arquivo
    if (self::$types[$type]['core'])
      $abs_path = CORE_PATH . DS;
    else {
      if (self::$types[$type]['root'])
        $abs_path = DJCK . DS;
      elseif (self::$types[$type]['plugins'])
        $abs_path = PLUGIN_PATH . DS;
      else
        $abs_path = APP_PATH . DS;
    }
    $abs_path .= implode(DS, $parts);
    
    return $abs_path;
  }
  
  static private function _parseFile($file, $path, &$alias = '') {
    
    // pega o caminho
    $abs_path = self::path($path);
    $abs_path .= DS . $file;
    
    // alias para o arquivo
    $alias = $abs_path;
    foreach (array(CORE_PATH => 'core', 
                    APP_PATH => 'app', 
                    PLUGIN_PATH => 'plugins') as $haystack => $replacement) {
      $alias = str_replace($haystack, $replacement, $alias);
    }
    $alias = str_replace(DS, '.', $alias);
    
    // coloca a extensão no arquivo
    foreach (array('.php', '.class.php') as $ext) {
      if (is_file($abs_path . $ext)) {
        $abs_path .= $ext;
        break;
      }
    }
    
    return $abs_path;
  }
  
  static function import($file, $path, $force = false) {
    $success = false;
    
    $alias = '';
    $parsed = self::_parseFile($file, $path, $alias);
    
    echo $parsed,'<br>',$alias,'<br>';
    return;
    
    // checa se o arquivo já foi carregado e se vai força-lo a sobrecarrega-lo
    if (!isset(self::$imported[$alias]) || $force) {
      
      // verifica includes
      /*if (isset(self::$types[$type]['includes'])) {
        if (is_array(self::$types[$type]['includes'])) {
          foreach (self::$types[$type]['includes'] as $include) {
            if (!in_array($arq, self::$types[$type]['includes'])) {
              self::import($path_parts.$include, $type, false, false);
            }
          }
        } elseif ($path_parts.self::$types[$type]['includes'] !== $file) {
          self::import($path_parts.self::$types[$type]['includes'], $type, false, false);
        }
      }*/

      // verifica se o arquivo existe
      if (is_file($parsed)) {
        $success = (bool) include_once $parsed;
      }

      // registra qual o status da classe.
      self::$imported[$alias] = $success;
      
    }
    return self::$imported[$alias];
  }
  
  static function depends() {
    
  }
  
  static function register() {
    
  }
  
  static function setup() {
    
  }
  
}
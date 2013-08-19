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

class CoreException extends Exception {};

/**
 * Description of Core
 *
 * @author Rapha e Dani
 */
abstract class Core {
  
  // classes importadas do core
  private static $imported = array();
  
  // classes registradas para serem carregadas pelo autoload do core
  private static $classes = array();
  
  // numero de chamadas de funcao do core
  private static $calls = 0;

  /**
   * Tipo de arquivo
   * Deve conter as seguintes configurações:
   *  - core: TRUE para buscar do core/, FALSE para buscar do app/
   *  - path: caminho onde o arquivo será encontrado, relativo ao app/ ou core/ de acordo
   *          com a opção core acima
   *  - root: TRUE para buscar da raiz do projeto, FALSE para buscar relativo ao core/ ou app/
   *  - plugins: TRUE para buscar da pasta dos plugins, FALSE para buscar relativo ao core/ ou app/
   * @var type tipo do arquivo que sera carregado
   */
  public static $types = array(
      'core' => array('core' => true),
      'model' => array('core' => false, 'path' => 'model'),
      'view' => array('core' => false, 'path' => 'view'),
      'controller' => array('core' => false, 'path' => 'controller'),
      'plugin' => array('plugins' => true),
      'file' => array('root' => true),
      'root' => array('root' => true),
  );
  
  // registra a classe que vc vai usar no contexto para ser carregada pelo autoload
  static function uses($class, $path, $force = false) {
    // pega pasta correta
    $parsed = self::_parseFile($class, $path);
    
    // sanitize class name
    $class = strtolower($class);
    
    // Only attempt to register the class if the name and file exist.
    if (!empty($class) && is_file($parsed)) {
      // Register the class with the autoloader if not already registered or the force flag is set.
      if (empty(self::$classes[$class]) || $force) {
        self::$classes[$class] = str_replace(DJCK, '#', $parsed); 
        // replace serve para economizar espaço da memoria utilizado pela variavel
        // estatica. não é necessario guardar o caminho inteiro
      }
    }
    
    ++self::$calls;
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
    ++self::$calls;
    return self::$imported[$alias];
  }
  
  // carrega uma classe
  public static function load($class) {
    // Sanitize class name.
    $class = strtolower($class);
    
    ++self::$calls;

    // If the class already exists do nothing.
    if (class_exists($class, false)) {
      return true;
    }

    // If the class is registered include the file.
    if (isset(self::$classes[$class])) {
      //echo self::$classes[$class];
      include_once str_replace('#', DJCK, self::$classes[$class]);
      return true;
    }

    return false;
  }
  
  // verifica se a classe já foi carregada, se não, lançar uma exception
  static function depends($class) {
    ++self::$calls;
    
    // If the class already exists do nothing.
    if (!class_exists(strtolower($class), false)) {
      $trace = debug_backtrace();
      throw new Exception('Arquivo '.basename($trace[0]['file']).' depende da classe '.$class);
    }
  }
  
  // alias para ::uses
  static function register($class, $path, $force = false) {
    self::uses($class, $path, $force);
  }
  
  static function setup() {
    spl_autoload_register(array('Core', 'load'));
  }
  
  static function dump() {
    global $Router;
    echo '<pre>';
    print_r(array(self::$classes, self::$imported, self::$calls, $Router, $_SESSION, ModelCommon::$dump));
    echo '</pre>';
  }
  
}
<?php

class DJKLoader {
  
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
      'core' => array('extends' => null, 'path' => 'core', 'extension_file' => 'class.php', 
          'includes' => array('Interfaces.php', 'Exceptions.php')),
      'config' => array('extends' => null, 'path' => 'config', 'extension_file' => 'php'),
      'model' => array('extends' => array('AppModel', 'AppModelCollection'), 'path' => 'app/model', 
          'extension_file' => 'class.php'),
      'controller' => array('extends' => 'AppController', 'path' => 'app/controller', 
          'suffix' => 'Controller', 'extension_file' => 'class.php'),
      'plugin' => array('extends' => null, 'path' => 'plugins', 'extension_file' => '.php'),
      'view' => array('extends' => null, 'path' => 'app/view', 'extension_file' => null),
  );

  /**
   * Importa as classes e arquivos dando include 
   * 
   * @param string $file
   * @param string $type
   * @param bool $force
   * @param bool $has_ext_file Força o arquivo pegar a extenção que foi adicionado para o tipo do arquivo
   * @return bool
   */
  public function import($file, $type = 'core', $force = false, $has_ext_file = true) {
    $success = false;

    $file = str_replace(DS, '/', $file);
    $part = explode('/', $file);
    $arq = array_pop($part);
    if ($arq == '*') {
      return $this->importAll(implode('/', $part), $type, $force);
    }
    
    $path = (!empty(self::$types[$type]['path'])?self::$types[$type]['path'].'/': '');
    $path_parts = ((!empty($part)) ? implode('/', $part).'/' : '');
    
    // nome que será registrado o arquivo
    $alias = $path.$file;
    if (strpos($alias, '.') !== false) {
      $alias = substr($alias, 0, strpos($alias, '.'));
    }
    
    /*echo $alias.'<br>';
    
    self::$a++;
    if (self::$a > 10) exit();*/
    
    // checa se o arquivo já foi carregado e se vai força-lo a sobrecarrega-lo
    if (!isset(self::$imported[$alias]) || $force) {
      $parsed = $this->_parseFile($file, $type, $has_ext_file);
      
      // verifica dependencias
      if (isset(self::$types[$type]['extends'])) {
        if (is_array(self::$types[$type]['extends'])) {
          foreach (self::$types[$type]['extends'] as $extend) {
            if (!in_array($arq, self::$types[$type]['extends'])) {
              $this->import($extend, $type);
            }
          }
        } elseif (self::$types[$type]['extends'] !== $file) {
          $this->import(self::$types[$type]['extends'], $type);
        }
      }
      
      // verifica includes
      if (isset(self::$types[$type]['includes'])) {
        if (is_array(self::$types[$type]['includes'])) {
          foreach (self::$types[$type]['includes'] as $include) {
            if (!in_array($arq, self::$types[$type]['includes'])) {
              $this->import($path_parts.$include, $type, false, false);
            }
          }
        } elseif ($path_parts.self::$types[$type]['includes'] !== $file) {
          $this->import($path_parts.self::$types[$type]['includes'], $type, false, false);
        }
      }

      // verifica se o arquivo existe
      //echo $parsed . '<br />';
      if (is_file($parsed)) {
        $success = (bool) include_once $parsed;
      }

      // registra qual o status da classe.
      self::$imported[$alias] = $success;
      
      //echo '['. $alias . '] = ' . var_export(self::$imported[$alias], true).'<br>';
    }
    return self::$imported[$alias];
  }

  public function importAll($file, $type, $force) {
    $parsed = $this->_parseFile($file, $type, false);
    
    if (is_dir($parsed)) {
      $success = $temp = true;
      $diretory = new RecursiveDirectoryIterator($parsed);
      $iterator = new RecursiveIteratorIterator($diretory);
      //$iterator = new DirectoryIterator($parsed);
      foreach ($iterator as $entry) {
        $file_normalized = str_replace($parsed.DS, '', $entry->getPathname());
        if (!preg_match('/CVS/', $file_normalized) && preg_match('/\.php$/', $file_normalized)) {
          if (is_file($parsed.DS.$file_normalized) ) {
            $temp = $this->import($file.DS.$file_normalized, $type, $force, false);
            $success && $success = $temp;
          }
        }
      }
      return $success;
    }
    return false;
  }

  /**
   * Retorna o caminho de uma classe ou arquivo
   * 
   * @param string $file
   * @param string $type
   * @param bool $has_ext_file Força o arquivo pegar a extenção que foi adicionado para o tipo do arquivo
   * @return string
   */
  public function getPath($file, $type = 'core', $has_ext_file = true) {
    return $this->_parseFile($file, $type, $has_ext_file);
  }

  /**
   * Monta o caminho fisico da classe ou do arquivo
   * 
   * @param string $file
   * @param string $type
   * @param bool $has_ext_file Força o arquivo pegar a extenção que foi adicionado para o tipo do arquivo
   * @return string
   */
  private function _parseFile($file, $type, $has_ext_file = true) {
    $file = str_replace(DS, '/', $file);
    $parts = explode('/', $file);
    $class = array_pop($parts);
    $base = DJK_BASE;
    $path = '';

    // verifica se é um arquivo do core
    /*if (self::$types[$type]['core']) {
      $path = 'core' . DS;
    }*/
    // verifica se existe um caminho pré definido para o arquivo
    if (!is_null(self::$types[$type]['path'])) {
      $path .= str_replace('/', DS, self::$types[$type]['path']) . DS;
    }
    // adiciona parte do caminho passado na chamada do metodo
    if (!empty($parts)) {
      $path .= implode(DS, $parts) . DS;
    }

    // verifica se existe prefixo no nome do arquivo
    if (isset(self::$types[$type]['prefix'])) {
      $class = self::$types[$type]['prefix'] . $class;
    }
    // verifica se existe sufixo no nome so arquivo
    if (isset(self::$types[$type]['suffix'])) {
      if (!preg_match('/' . preg_quote(self::$types[$type]['suffix'], '/') . '$/', $class))
        $class .= self::$types[$type]['suffix'];
    }
    // verifica se existe extenção do arquivo e adiciona
    if ($has_ext_file) {
      if (isset(self::$types[$type]['extension_file'])) {
        $class .= '.' . self::$types[$type]['extension_file'];
      } else {
        $class .= '.php';
      }
    }

    // retorna o endereço fisico do arquivo sem verificar a integridade do endereço.
    return $base . $path . $class;
  }

  /**
   * Load the file for a class.
   *
   * @param   string  $class  The class to be loaded.
   *
   * @return  boolean  True on success
   *
   * @since   11.1
   */
  public static function load($class) {
    // Sanitize class name.
    $class = strtolower($class);

    // If the class already exists do nothing.
    if (class_exists($class, false)) {
      return true;
    }

    // If the class is registered include the file.
    if (isset(self::$classes[$class])) {
      //echo self::$classes[$class];
      include_once self::$classes[$class];
      return true;
    }

    return false;
  }

  /**
   * Directly register a class to the autoload list.
   *
   * @param   string   $class  The class name to register.
   * @param   string   $path   Full path to the file that holds the class to register.
   * @param   boolean  $force  True to overwrite the autoload path value for the class if it already exists.
   *
   * @return  void
   *
   * @since   11.1
   */
  public function register($class, $file, $type = 'core', $force = false) {
    // Sanitize class name.
    $class = strtolower($class);

    // pega caminho físico do arquivo
    $path = $this->_parseFile($file, $type);
    
    // Only attempt to register the class if the name and file exist.
    if (!empty($class) && is_file($path)) {
      // Register the class with the autoloader if not already registered or the force flag is set.
      if (empty(self::$classes[$class]) || $force) {
        self::$classes[$class] = $path;
      }
    }
  }

    /**
   * Method to setup the autoloaders for the Joomla Platform.  Since the SPL autoloaders are
   * called in a queue we will add our explicit, class-registration based loader first, then
   * fall back on the autoloader based on conventions.  This will allow people to register a
   * class in a specific location and override platform libraries as was previously possible.
   *
   * @return  void
   *
   * @since   11.3
   */
  public function setup() {
    // Register the autoloader functions.
    spl_autoload_register(array(new self, 'load'));
  }
  
  public function dump() {
    echo '<pre>';
    print_r(self::$classes);
    print_r(self::$imported);
  }
  
}
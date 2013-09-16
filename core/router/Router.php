<?php

Core::depends('Controller');
Core::depends('View');

class RouterException extends CoreException {}

class Router {

  protected $routes = array();
  protected $namedRoutes = array();
  protected $basePath = '';

  /**
   * Set the base path.
   * Useful if you are running your application from a subdirectory.
   */
  public function __construct() {
    $this->basePath = SITE_URL;
  }

  /**
   * Map a route to a target
   *
   * @param string $method One of 4 HTTP Methods, or a pipe-separated list of multiple HTTP Methods (GET|POST|PUT|DELETE)
   * @param string $route The route regex, custom regex must start with an @. You can use multiple pre-set regex filters, like [i:id]
   * @param mixed $target The target where this route should point to. Can be anything.
   * @param string $name Optional name of this route. Supply if you want to reverse route this url in your application.
   *
   */
  public function map($route, $target, $name = null) {

    if ($route != '*') {
      $route = $this->basePath . $route;
    }

    $this->routes[] = array($route, $target, $name);

    if ($name) {
      if (isset($this->namedRoutes[$name])) {
        throw new Exception("Can not redeclare route '{$name}'");
      } else {
        $this->namedRoutes[$name] = array(
            'route' => $route,
            'controller' => is_array($target) ? key($target) : null
        );
      }
    }

    return;
  }

  /**
   * Reversed routing
   *
   * Generate the URL for a named route. Replace regexes with supplied parameters
   *
   * @param string $routeName The name of the route.
   * @param array @params Associative array of parameters to replace placeholders with.
   * @return string The URL of the route with named parameters in place.
   */
  public function generate($routeName, array $params = array()) {

    // Check if named route exists
    if (!isset($this->namedRoutes[$routeName])) {
      throw new Exception("Route '{$routeName}' does not exist.");
    }

    // Replace named parameters
    $route = $this->namedRoutes[$routeName];
    $url = $route;

    if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {

      foreach ($matches as $match) {
        list($block, $pre, $type, $param, $optional) = $match;

        if ($pre) {
          $block = substr($block, 1);
        }

        if (isset($params[$param])) {
          $url = str_replace($block, $params[$param], $url);
        } elseif ($optional) {
          $url = str_replace($pre . $block, '', $url);
        }
      }
    }

    return $url;
  }

  /**
   * Match a given Request Url against stored routes
   * @param string $requestUrl
   * @param string $requestMethod
   * @return array|boolean Array with route information on success, false on failure (no match).
   */
  public function match($requestUrl = null) {

    $params = array();
    $match = false;

    // set Request Url if it isn't passed as parameter
    if ($requestUrl === null) {
      if ($_GET['q'])
        $requestUrl = SITE_URL.'/'.$_GET['q'];
      else
        $requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    }

    // Strip query string (?a=b) from Request Url
    if (($strpos = strpos($requestUrl, '?')) !== false) {
      $requestUrl = substr($requestUrl, 0, $strpos);
    }

    // Force request_order to be GP
    // http://www.mail-archive.com/internals@lists.php.net/msg33119.html
    $_REQUEST = array_merge($_GET, $_POST);

    foreach ($this->routes as $handler) {
      list($_route, $target, $name) = $handler;
      
      //echo $requestUrl,'<br>',$_route,'<br>';
      
      // Check for a wildcard (matches all)
      if ($_route === '*') {
        $match = true;
      } elseif (isset($_route[0]) && $_route[0] === '@') {
        $match = preg_match('`' . substr($_route, 1) . '`', $requestUrl, $params);
      } else {
        $route = null;
        $regex = false;
        $j = 0;
        $n = isset($_route[0]) ? $_route[0] : null;
        $i = 0;

        // Find the longest non-regex substring and match it against the URI
        while (true) {
          if (!isset($_route[$i])) {
            break;
          } elseif (false === $regex) {
            $c = $n;
            $regex = $c === '[' || $c === '(' || $c === '.';
            if (false === $regex && false !== isset($_route[$i + 1])) {
              $n = $_route[$i + 1];
              $regex = $n === '?' || $n === '+' || $n === '*' || $n === '{';
            }
            if (false === $regex && $c !== '/' && (!isset($requestUrl[$j]) || $c !== $requestUrl[$j])) {
              continue 2;
            }
            $j++;
          }
          $route .= $_route[$i++];
        }

        $regex = $this->compileRoute($route);
        $match = preg_match($regex, $requestUrl, $params);
      }

      if (($match == true || $match > 0)) {

        if ($params) {
          foreach ($params as $key => $value) {
            if (is_numeric($key))
              unset($params[$key]);
          }
        }

        return array(
            'target' => $target,
            'params' => $params,
            'name' => $name
        );
      }
    }
    return false;
  }

  /**
   * Compile the regex for a given route (EXPENSIVE)
   */
  private function compileRoute($route) {
    if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {

      $match_types = array(
          'i' => '[0-9]++',
          'a' => '[0-9A-Za-z]++',
          'h' => '[0-9A-Fa-f]++',
          '*' => '.+?',
          '**' => '.++',
          '' => '[^/]++'
      );

      foreach ($matches as $match) {
        list($block, $pre, $type, $param, $optional) = $match;

        if (isset($match_types[$type])) {
          $type = $match_types[$type];
        }
        if ($pre === '.') {
          $pre = '\.';
        }

        //Older versions of PCRE require the 'P' in (?P<named>)
        $pattern = '(?:'
                . ($pre !== '' ? $pre : null)
                . '('
                . ($param !== '' ? "?P<$param>" : null)
                . $type
                . '))'
                . ($optional !== '' ? '?' : null);

        $route = str_replace($block, $pattern, $route);
      }
    }
    return "`^$route$`";
  }
  
  function route($target, $params=array(), $response_code = null) {
    
    static $iterations = 0;
    ++$iterations;
    
    if ($iterations > 10) {
      throw new RouterException('A rota '.$target.' entrou em loop');
    }
    
    /*$visibility = $this->connected_routes[$url_id]['visibility'];
    if ($visibility == 'hidden' && $this->url == $url_id) { //impede que acessem a rota se ela for invisivel
      return $this->route('error', null, 403);
    }*/
    $controller = $target;
    // procura o controller se $controller for um link (string)
    // o link deve ser da seguinte forma: url_id#action
    // se action for omitido, "index" é usado
    if (is_string($controller)) {
      list($url, $method) = explode('#', $controller);
      if (!$method)
        $method = null;
      
      if (isset($this->namedRoutes[$url])) {
        $class = $this->namedRoutes[$url]['controller'];
        if ($class)
          $controller = array($class => $method);
      }
      unset($url, $method, $class);
      // se ainda sim não vier um array, o controller não foi definido corretamente
      if (!is_array($controller)) {
        throw new RouterException('Rota '.$target.' não foi definida corretamente.'.
                'Verifique se ela está sendo linkada com uma rota que não tenha nome ou controller definido');
      }
    }

    // pega a ação e o controller a ser usado
    if (is_array($controller))
      list($class_name, $action) = each($controller);

    //acerto o path do meu view
    //$dir = $path;
    
    // controla o buffer
    ob_end_clean(); // limpa qualquer coisa que vier de outro redirecionamento
    ob_start();
    
    
    // verifica se a classe existe
    if (class_exists($class_name)) {
      
      // reflexão da classe do controller
      $rflc_class = new ReflectionClass($class_name);
      
      $class = new $class_name($_GET['q']); //TODO: tirar q e colocar url de um lugar melhor

      $class->request->addParams($params);
      if (isset($response_code)) $class->response->statusCode($response_code);
      
      // alguns headers padrões
      if (defined('SITE_CHARSET')) {
        $class->response->charset(SITE_CHARSET);
      }
      
      if (defined('SITE_OFFLINE') && SITE_OFFLINE === true) {
        $class->response->header('X-Robots-Tag', 'noindex, nofollow');
      }
      
      // before
      if ($class->beforeExecute() !== false) {
        
        if (empty($action))
          $action = 'index';
        
        if (!method_exists($class_name, $action)) {
          $this->route('error', null, 404);
          return;
        }
        
        
        // reflexão do metodo e dos parametros dela
        $rflc_method = $rflc_class->getMethod($action);
        $rflc_parameters = $rflc_method->getParameters();
        
        // array que vai guardar os parametros que vão ser passados pro metodo
        // como view, etc..
        $params_to_pass = array();
        
        // verifica os parametros do metodo (ação) e passa os objetos corretos conforme
        // o metodo precise
        foreach ($rflc_parameters as $rflc_param) {
          switch ($rflc_param->getName()) {
            case 'view':
              // se o metodo tiver $view, instanciar uma view automaticamente de 
              // acordo com o tipo de objeto que veio
              $class_view = $rflc_param->getClass()->getName();
              Core::depends($class_view);
              
              $view = new $class_view($class->default_path."/$action.tpl");
              
              $params_to_pass[] = $view;
              break;
          }
        }
        
        // chama o metodo       
        call_user_func_array(array(&$class, $action), $params_to_pass);
        
        // after
        $class->afterExecute();
        
      }
      
      // pega o conteudo do buffer
      $contents = ob_get_clean();
      //ob_end_flush();
      
      // manda os headers definidos automaticamente
      $class->response->body($contents);
      $class->response->send();
      
    } else {
      ob_end_flush();
      
      throw new RouterException('Controller ' . $class_name . ' ('.$target.') não foi registrado');
    }
  }
  
  function dispatch() {
    $match = $this->match();
    
    if ($match === false) {
      $this->route('error', null, 404);
    } else {
      $this->route($match['target'], $match['params']);
    }
  }

}
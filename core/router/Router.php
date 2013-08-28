<?php

Core::uses('Controller', 'core/mvc/controller');

class RouterException extends Exception {}

/**
 * Description of Router
 *
 * @author usuario
 */
class Router {
  
  private $route_tree = array();
  private $connected_routes = array();
  public $url = null;
  public $cache = false;
  
  function map($url_id, $controller, $options = array()) {
    
    $params = $options['filters'];
    $path = $options['path'];
    
    // procura o controller se $controller for um link (string)
    // o link deve ser da seguinte forma: url_id#action
    // se action for omitido, "index" é usado
    /*if (is_string($controller)) {
      list($url, $method) = explode('#', $controller);
      if (!$method)
        $method = null;
      
      if (isset($this->connected_routes[$url])) {
        $class = key($this->connected_routes[$url]['controller']);
        $controller = array($class => $method);
        
        $path = $this->connected_routes[$url]['path'];
      }
    }*/

    // guarda rota no array interno de rotas conectadas
    $this->connected_routes[$url_id] = array(
        'controller' => $controller,
        'filters' => $params,
        'path' => $path
    );

    if ($this->cache)
      return;

    $url_arr = explode('/', $url_id);
    $route_tree = &$this->route_tree;

    foreach ($url_arr as $url_item) {
      if (empty($url_item))
        continue;
      
      if ($url_item[0] == ':') {
        if (!isset($params[$url_item])) { // param on path was not specified on the param list
          throw new RouterException('Está faltando o parametro ' . $url_item . ' para a rota ' . $url_id);
        }
        $url_index = ':';
      } else {
        $url_index = $url_item;
      }
      
      if (!isset($route_tree[$url_index])) {
        $route_tree[$url_index] = array();
        if ($url_index == ':') { // we add a couple of private keys so our app knows what param it is dealing with
          $route_tree[$url_index]['__pattern'] = $params[$url_item];
          $route_tree[$url_index]['__name'] = $url_item;
        }
      } else if ($url_index == ':') { // param of that level already set
        if ($url_item != $route_tree[$url_index]['__name']) { // and its not the same we had on our tree already
          throw new RouterException('Este parametro já existe na mesma rota com um nome diferente');
        }
      }

      // move the pointer to the next level
      $route_tree = &$route_tree[$url_index];
    }
  }
  
  function parse(&$params) {
    if (!isset($this->url))
      throw new RouterException('Não foi definida nenhuma url para o router. Utilize sanitizeUrl()');

    $url = &$this->url;
    $pieces = explode('/', $url);
    $route_tree = &$this->route_tree;
    // will return false or the route found
    $route_arr = $this->_parse_aux($pieces, $route_tree, $params);

    if ($route_arr !== false) {
      $route = implode('/', $route_arr);
    }

    if ($route_arr === false || (!empty($route) && !isset($this->connected_routes[$route]))) { 
      // guessed route does not exist!!
      return false;
    }

    return $route;
  }
  
  private function _parse_aux(&$pieces, &$currentTreeLevel, &$params = array(), $index = 0, $currentRoute = array()) {

    if (empty($pieces)) { // no URL!
      return false;
    }

    if ($index >= count($pieces)) { // no more pieces to check
      return $currentRoute;
    }

    $url_piece = $pieces[$index];
    if (is_null($currentTreeLevel)) {
      return $currentRoute;
    } else if (isset($currentTreeLevel[$url_piece])) { // that static node exists in our tree		
      $currentRoute[] = $url_piece;
      return $this->_parse_aux($pieces, $currentTreeLevel[$url_piece], $params, $index + 1, $currentRoute);
    } else { // no static node with that name
      if (isset($currentTreeLevel[':']) && !empty($currentTreeLevel[':']['__pattern']) && preg_match('/^'.$currentTreeLevel[':']['__pattern'].'$/i', $url_piece)) { // it may be an explicit parameter
        $currentRoute[] = $currentTreeLevel[':']['__name'];
        $params[$currentTreeLevel[':']['__name']] = $url_piece;
        return $this->_parse_aux($pieces, $currentTreeLevel[':'], $params, $index + 1, $currentRoute);
      } else { // or not, so let's group them and let the controller decide
        for ($i = $index; $i < count($pieces); $i++) {
          $params[] = $pieces[$i];
        }

        return $currentRoute;
      }
    }
  }
  
  function route($url_id, $params=array(), $response_code = 200) {
    
    /*$visibility = $this->connected_routes[$url_id]['visibility'];
    if ($visibility == 'hidden' && $this->url == $url_id) { //impede que acessem a rota se ela for invisivel
      return $this->route('error', null, 403);
    }*/
    $controller = &$this->connected_routes[$url_id]['controller'];
    $path = &$this->connected_routes[$url_id]['path'];
    // procura o controller se $controller for um link (string)
    // o link deve ser da seguinte forma: url_id#action
    // se action for omitido, "index" é usado
    if (is_string($controller)) {
      list($url, $method) = explode('#', $controller);
      if (!$method)
        $method = null;
      
      if (isset($this->connected_routes[$url])) {
        $class = key($this->connected_routes[$url]['controller']);
        $controller = array($class => $method);
        
        $path = $this->connected_routes[$url]['path'];
      }
    }

    // pega a ação e o controller a ser usado
    if (is_array($controller))
      list($class_name, $action) = each($controller);

    //acerto o path do meu view
    $dir = $path;

    if (is_null($action) && !empty($params) && isset($params[0])) {
      //extrai a 'acao'
      $action = array_shift($params);
    }
    
    // controla o buffer
    ob_start();

    // verifica se a classe existe
    if (class_exists($class_name)) {
      $class = new $class_name($dir);

      $class->request->addParams($params);
      $class->response->statusCode($response_code);
      
      // alguns headers padrões
      if (defined('SITE_CHARSET')) {
        $class->response->charset(SITE_CHARSET);
      }
      
      if (defined('SITE_OFFLINE') && SITE_OFFLINE === true) {
        $class->response->header('X-Robots-Tag', 'noindex, nofollow');
      }

      // verifica se existe ação ou se o controller é NotFound
      if (!empty($action) && $url_id != 'error') {
        if (method_exists($class_name, $action)) {
          $class->$action();
        } else {
          //$class->response->statusCode(404);
          $this->route('error', null, 404);
          return;
        }
      } else {
        // checa e instancia o método index da classe
        if (method_exists($class_name, 'index')) {
          $class->index();
        } else {
          //$class->response->statusCode(404);
          $this->route('error', null, 404);
          return;
        }
      }
      
      // pega o conteudo do buffer
      $contents = ob_get_clean();
      //ob_end_flush();
      
      // manda os headers definidos automaticamente
      $class->response->body($contents);
      $class->response->send();
      
    } else {
      ob_end_flush();
      
      throw new RouterException('Controller ' . $class_name . ' ('.$url_id.') não foi registrado');
    }
  }
  
  function dispatch($url) {
    $this->url = &$url;
    if (empty($url)) {
      $this->route('home'); //home
    } else {
      $params = array();
      $route = $this->parse($params);

      if ($route === false) {
        $this->route('error', null, 404);
      /*} else if (empty($route)) {
        $this->route('404', $params);*/
      } else {
        $this->route($route, $params);
      }
    }
  }
  
}
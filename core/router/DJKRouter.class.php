<?php

class DJKRouterException extends RuntimeException {}

class DJKRouter {

  private $route_tree = array();
  private $connected_routes = array();
  private $url = null;
  public $cache = false;

  public function __construct() {
    if ($this->cache) {
      $this->route_tree = $cached_route_tree;
    }
  }

  public function sanitizeUrl($q) {
    $q = str_replace(array('../', './', 'javascript:'), '', $q);
    $q = strip_tags($q);
    $q = strtolower($q);
    $this->url = $q;
    return $q;
  }

  public function connect($url, $options) {

    $params = & $options['params'];

    // guarda rota no array interno de rotas conectadas
    $this->connected_routes[$url] = array_merge($options, array('url' => $url));

    if ($this->cache)
      return;

    $url_arr = explode('/', $url);
    $route_tree = & $this->route_tree;

    foreach ($url_arr as $url_item) {
      if (empty($url_item))
        continue;
      if ($url_item[0] == ':') {
        if (!isset($params[$url_item])) { // param on path was not specified on the param list
          throw new DJKRouterException('Está faltando o parametro ' . $url_item . ' para a rota ' . $url);
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
          throw new DJKRouterException('Este parametro já existe na mesma rota com um nome diferente');
        }
      }

      // move the pointer to the next level
      $route_tree = & $route_tree[$url_index];
    }
  }

  public function route($url, $params = array(), $response_code = 200) {

    $action = $this->connected_routes[$url]['action'];
    $type = $this->connected_routes[$url]['type'];
    $extras = $this->connected_routes[$url]['extras'];

    if ($type == 'link') { //se a rota eh um link para uma outra
      $url = $this->connected_routes[$url]['path'];
    }

    $visibility = $this->connected_routes[$url]['visibility'];
    if ($visibility == 'hidden' && $this->url == $url) { //impede que acessem a rota se ela for invisivel
      return $this->route('error', null, 403);
    }

    $class_name = $this->connected_routes[$url]['controller'];

    //acerto o path do meu view
    $dir = $url;

    if (is_null($action) && !empty($params) && isset($params[0])) {
      //extrai a 'acao'
      $action = $params[0];
    }

    // verifica se a classe existe
    if (class_exists($class_name)) {

      $class = new $class_name($dir);

      // verifica se existe ação ou se o controller é NotFound
      if (!empty($action) && $url != 'error') {
        if (method_exists($class_name, $action)) {
          $class->$action($params, $extras);
        } else {
          return $this->route('error', null, 404);
        }
      } else {
        // checa e instancia o método index da classe
        if (method_exists($class_name, 'index')) {
          $class->index($params, $extras);
        } else {
          return $this->route('error', null, 404);
        }
      }
    } else {
      throw new DJKRouterException('Controller ' . $class_name . ' não foi registrado');
    }
  }

  public function parseUrl(&$params) {
    if (!$this->url)
      throw new DJKRouterException('Não foi definida nenhuma url para o router. Utilize sanitizeUrl()');

    $url = & $this->url;
    $pieces = explode('/', $url);
    $route_tree = & $this->route_tree;
    $route_arr = $this->_parseUrlAux($pieces, $route_tree, $params); // will return false or the route found

    if ($route_arr !== false) {
      $route = implode('/', $route_arr);
    }

    if ($route_arr === false || (!empty($route) && !isset($this->connected_routes[$route]))) { // guessed route does not exist!!
      return false;
    }

    return $route;
  }

  private function _parseUrlAux(&$pieces, &$currentTreeLevel, &$params = array(), $index = 0, $currentRoute = array()) {

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
      return $this->_parseUrlAux($pieces, $currentTreeLevel[$url_piece], $params, $index + 1, $currentRoute);
    } else { // no static node with that name
      if (isset($currentTreeLevel[':']) && preg_match($currentTreeLevel[':']['__pattern'], $url_piece)) { // it may be an explicit parameter
        $currentRoute[] = $currentTreeLevel[':']['__name'];
        $params[$currentTreeLevel[':']['__name']] = $url_piece;
        return $this->_parseUrlAux($pieces, $currentTreeLevel[':'], $params, $index + 1, $currentRoute);
      } else { // or not, so let's group them and let the controller decide
        for ($i = $index; $i < count($pieces); $i++) {
          $params[] = $pieces[$i];
        }

        return $currentRoute;
      }
    }
  }

}
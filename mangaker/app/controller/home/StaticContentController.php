<?php

Core::uses('AppController', 'controller');

Core::uses('ImageResource', 'core/resource');

/**
 * Description of HomeController
 *
 * @author usuario
 */
class StaticContentController extends AppController {
  
  function index() {
    // processa o parametro
    $file = $this->request->params['file'];
    
    $parts_file = explode('/', $file);
    // detecta se o primeiro pedaço do nome do arquivo é um "sizes" (999x999)
    if (is_numeric($parts_file[0])) {
      $width = array_shift($parts_file);
      $height = null;
    } elseif (strpos($parts_file[0], 'x') !== false) {
      $parts_size = explode('x', $parts_file[0], 2);
      if (is_numeric($parts_size[0]) && is_numeric($parts_size[1])) {
        array_shift($parts_file);
        $width = $parts_size[0];
        $height = $parts_size[1];
      }
      unset($parts_size); //memoria
    }
    $file = implode(DS, $parts_file);
    unset($parts_file); //memoria
    
    
    // cria um objeto de criacao de imagem
    $im = new ImageResource($file);
    if ($width) {
      $im->setSizes($width, $height);
    }
    
    // renderiza o novo arquivo (ou pega do cache)
    // retorna o nome do arquivo
    $new_file = $im->render();
    
    // verifica cache e outputa o arquivo
    if ($new_file !== false) {
      $this->response->cache(filemtime($new_file), "+1 month");
      $this->response->type('png');
      $modified = $this->response->checkNotModified($this->request);

      if (!$modified) {
        readfile($new_file);
      }
    } else {
      // imagem nao criada ou nao encontrada
      $this->response->statusCode(404);
    }
  }
  
}
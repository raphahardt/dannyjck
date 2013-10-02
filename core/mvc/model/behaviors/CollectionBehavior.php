<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CollectionBehavior
 *
 * @author usuario
 */
class CollectionBehavior extends SingleBehavior {
  
  public $priority = 1;
  
  public function find(Model $Model, $pointer) {
    return $Model->Mapper->find($pointer);
  }
  
  public function setFilter(Model $Model, $cons) {
    $args = func_get_args();
    array_shift($args); // model
    
    $constraints = array();
    if (count($args) > 1) {
      $constraints = $args;
    } elseif (count($args) == 1) {
      $cons = $args[0];
      if (is_array($cons)) {
        $constraints = $cons;
      } else {
        $constraints[] = $cons;
      }
    }
    
    $Model->Mapper->setFilter($constraints);
  }
  
  public function select(Model $Model) {
    if ($Model->Mapper instanceOf DatabaseItfMapper) {
      $Model->Mapper->select();
    }
    $Model->Mapper->first();
    return true;
  }
  
  // arrayaccess
  public function offsetExists(Model $Model, $offset) {
    return !!$Model->Mapper->get((int)$offset);
  }

  public function offsetGet(Model $Model, $offset) {
    return $Model->Mapper->get((int)$offset);
  }

  public function offsetSet(Model $Model, $offset, $value) {
    throw new CoreException('Não é possivel definir dados diretamente no collection. '.
            'Para isso, use push() ou unshift()');
  }

  public function offsetUnset(Model $Model, $offset) {
    throw new CoreException('Não é possivel definir dados diretamente no collection. '.
            'Para isso, use push() ou unshift()');
  }
  
  // iterator
  // herda de single
  
}
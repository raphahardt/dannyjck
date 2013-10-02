<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SingleBehavior
 *
 * @author usuario
 */
class SingleBehavior extends Behavior {
  
  public $priority = 0;
  
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
    // se o model também é um collection, chamar o metodo dele sempre primeiro 
    // em vez do single. isso permite que não importa a ordem dos behaviors, o collection
    // sempre terá precedencia no select
    if ($Model->is('Collection')) {
      return $Model->uses('Collection')->select();
    }
    // se for database, faz select no banco primeiro
    if ($Model->Mapper instanceOf DatabaseItfMapper) {
      $Model->Mapper->select();
    }
    // só atualiza o registro atual se encontrar apenas 1 registro
    if ($Model->Mapper->count() != 1) {
      $Model->Mapper->nullset();
      $Model->Mapper->clearResult();
    } else {
      $Model->Mapper->first();
    }
    return true;
  }
  
  public function insert(Model $Model) {
    if ($Model->Mapper instanceOf DatabaseItfMapper) {
      $return = $Model->Mapper->insert();
    } elseif ($Model->Mapper instanceof DefaultItfMapper) {
      $Model->Mapper->push();
      $return = $Model->Mapper->commit();
    }
    return $return;
  }
  
  public function update(Model $Model) {
    if ($Model->Mapper instanceOf DatabaseItfMapper) {
      $return = $Model->Mapper->update();
    } elseif ($Model->Mapper instanceof DefaultItfMapper) {
      $Model->Mapper->refresh();
      $return = $Model->Mapper->commit();
    }
    return $return;
  }
  
  public function delete(Model $Model) {
    // todos mappers tem essa funcao, e nos de database, ele apaga sem alterar o result,
    // diferente do delete() do dbcmapper
    $return = $Model->Mapper->remove();
    return true;
  }
  
  // arrayaccess
  public function offsetExists(Model $Model, $offset) {
    return isset($Model->Mapper[$offset]);
  }

  public function offsetGet(Model $Model, $offset) {
    return $Model->Mapper[$offset];
  }

  public function offsetSet(Model $Model, $offset, $value) {
    $Model->Mapper[$offset] = $value;
  }

  public function offsetUnset(Model $Model, $offset) {
    $Model->Mapper[$offset] = null;
  }
  
  // iterator
  public function current(Model $Model) {
    return $Model->Mapper->get();
  }

  public function key(Model $Model) {
    return $Model->Mapper->getPointerValue();
  }

  public function next(Model $Model) {
    return $Model->Mapper->next();
  }
  
  public function rewind(Model $Model) {
    return $Model->Mapper->first();
  }

  public function valid(Model $Model) {
    return !!$Model->Mapper->get();
  }
  
  public function count(Model $Model) {
    return $Model->Mapper->count();
  }
  
}
<?php

class SQLIDelete extends SQLInstructionBase {
  
  private $entity;
  
  protected function hash() {
    return SQLBase::key(__CLASS__.self::$_obj_id++);
  }
  
  public function __construct(SQLTable $table, SQLBase $where = null, $defs = null) { 
    parent::__construct();
    $this->build($table, $where, $defs);
  }
  
  public function build(SQLTable $table, SQLBase $where = null, $defs = null) { 
    $this->entity = $table;
    $this->filter = $where;
  }
  
  public function destroy() {
    $this->entity = null;
  }
  
  public function get() {
    return $this->getTable();
  }
  
  public function getTable() {
    return $this->entity;
  }
    
  function __get($name) {
    switch ($name) {
      case 'Entity':
      case 'Table':
        return $this->entity;
      default:
        throw new SQLException('Propriedade '.$name.' não existe');
    }
  }
  
  public function __toString() {
    
    if (empty($this->filter)) {
      return '';
    }
    
    parent::__toString();
    
    $s = 'DELETE FROM ';
    $s .= $this->entity . ' '. $this->entity->getAlias();
    
    if ($this->filter) {
      $s .= ' WHERE '. $this->filter;
    }
    
    return $s;
  }  
}
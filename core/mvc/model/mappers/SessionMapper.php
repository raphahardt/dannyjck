<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of FileMapper
 *
 * @author usuario
 */
// persistencia do mapper em session
class SessionMapper extends Mapper implements DefaultItfMapper {
  
  static private $counter = 1;
  
  public function init() {
    if (!isset($this->entity))
      $this->entity = 'data'.self::$counter++;
    
    
    // select registros logo de inicio
    if (isset($_SESSION[$this->entity])) {
      $this->_formatInput($_SESSION[$this->entity]);
      // seta o autoincremente para o ultimo encontrado no arquivo
      $this->autoIncrement($this->result[$this->count-1]['data'][key($this->pointer)]+1);
    }
    
    // cria referencia com a session para modificação de dados imediata
    //$this->result = &$_SESSION[$this->entity];
    
  }
  
  protected function _formatInput($input) {
    
    $this->clearResult();
    foreach ($input as $data) {
      $this->push($data);
    }
    return true;
  }
  
  protected function _formatOutput() {
    $output = array();
    foreach ($this->result as $r) {
      $output[] = $r['data'];
    }
    return $output;
  }
  
  /**
   * Salva as alterações dos registros na session
   * @return boolean
   */
  public function commit() {
    
    $output = $this->_formatOutput();
    $_SESSION[$this->entity] = $output;
    
    return parent::commit();
  }
  
  /**
   * Deleta a session (entidade)
   * @return boolen
   */
  public function destroy() {
    $this->clearResult();
    return true;
  }
  
}
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UnitTest
 *
 * @author usuario
 */
class UnitTest {
  private $test;
  private $result;
  private $expected = null;
  private $expected_sign = '';
  private $final = null;
  
  function __construct($test) {
    $this->test = $test;
    return $this;
  }
  
  function expect($result) {
    $this->result = $result;
    return $this;
  }
  
  function toBe($expected, $strict = true) {
    $this->expected = $expected;
    $this->expected_sign = $strict ? '=' : 'lazy=';
    $this->final = $strict ? $this->result === $expected : $this->result == $expected;
    return $this;
  }
  
  function notToBe($expected, $strict = true) {
    $this->expected = $expected;
    $this->expected_sign = $strict ? '!=' : 'lazy!=';
    $this->final = $strict ? $this->result !== $expected : $this->result != $expected;
    return $this;
  }
  
  function toInstanceOf($instance) {
    if (is_object($instance))
      $this->result = get_class($instance);
    
    $this->expected = $instance;
    $this->expected_sign = 'instanceof';
    $this->final = $this->result instanceof $instance;
    return $this;
  }
  
  function __toString() {
    if (is_object($this->result))
      $this->result = get_class ($this->result);
    
    return '<p>Teste: ' . $this->test . 
            '<br>'.(!$this->final ? '<span style="color:red">FALHA</span>' : '<span style="color:green">OK</span>').
            '<br>Esperado: '.$this->expected_sign.' '.var_export($this->expected, true).' ('.gettype($this->expected).')'.
            '<br>Resultado: '.var_export($this->result, true) . ' ('. gettype($this->result). ')'.
          '</p>';
  }
  
  static function dump($object) {
    echo '<pre>';
    print_r($object);
    echo '</pre>';
  }
  
}

function it($test) {
  $unit = new UnitTest($test);
  return $unit;
}


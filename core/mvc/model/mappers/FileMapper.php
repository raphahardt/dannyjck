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
// persistencia do mapper em arquivo
// collection: ~16.000 (20 campos)
class FileMapper extends Mapper implements FileItfMapper {
  
  const DEFAULT_ID_NAME = 'id';
  const DEFAULT_DELETE_NAME = 'exc';
  const DEFAULT_DELETE_DATE_NAME = 'exc_em';
  
  // caracteres de separação de dados de arquivo
  protected $delimiter = ',';
  protected $closure = '"';
  protected $escape = '\\';
  protected $newline = "\r\n";
  
  protected $fields;
  
  protected $permanent_delete = true;
  
  // serve como base para dados que vierem para serem alterados ou inseridos
  private $_fields_array = array();
  
  public function init() {
    
    if (!isset($this->entity))
      throw new CoreException('Obrigatorio definir um arquivo');
    
    if (!isset($this->fields))
      throw new CoreException('Obrigatorio definir os campos');
    
    $this->_fields_array = array();
    foreach ($this->fields as $f) {
      $this->_fields_array[$f] = null;
    }
    // inicia os dados já com os campos definidos
    $this->nullset();
    
    // select registros logo de inicio
    if (is_file($this->entity)) {
      $input = file_get_contents($this->entity);
      $this->_formatInput($input);
      // seta o autoincremente para o ultimo encontrado no arquivo
      $this->autoIncrement($this->result[$this->count-1]['data'][key($this->pointer)]+1);
    }
    
  }
  
  public function nullset() {
    parent::nullset();
    $this->data = $this->_fields_array; // limpa com os campos da tabela
  }
  
  public function set($data) {
    parent::set($data);
    $this->data = $this->_diff($this->_fields_array, $this->data); // preenche os campos que faltaram
  }
  
  public function push($data = null, $flag = self::FRESH) {
    if (is_array($data) && !empty($data)) {
      $data = $this->_diff($this->_fields_array, $data); // preenche os campos que faltaram
    }
    return parent::push($data, $flag);
  }
  
  public function unshift($data = null, $flag = self::FRESH) {
    if (is_array($data) && !empty($data)) {
      $data = $this->_diff($this->_fields_array, $data); // preenche os campos que faltaram
    }
    return parent::unshift($data, $flag);
  }
  
  /**
   * Processa os dados do objeto para o arquivo.
   * Cada tipo de mapper deve alterar essa função para fazer a conversão correta dos
   * dados.
   * @return boolean
   */
  protected function _formatOutput() {
    $output = '';
    // header
    $header = $this->fields;
    $output .= str_putcsv($header, $this->delimiter, $this->closure).$this->newline;
    // contents
    foreach ($this->result as $r) {
      $output .= str_putcsv($r['data'], $this->delimiter, $this->closure).$this->newline;
    }
    return $output;
  }
  
  /**
   * Processa os dados do arquivo para o objeto.
   * Cada tipo de mapper deve alterar essa função para fazer a conversão correta dos
   * dados.
   * @param string $input String com todos os dados, formatados da forma que salvou
   * @return boolean
   */
  protected function _formatInput($input) {
    
    dump(strlen($input));
    
    $lines = explode($this->newline, trim($input));
    // cabeçalho
    $line = array_shift($lines); 
    $header = str_getcsv($line, $this->delimiter, $this->closure, $this->escape);
    
    $this->clearResult();
    foreach ($lines as $line) {
      if (!empty($line)) {
        $values = str_getcsv($line, $this->delimiter, $this->closure, $this->escape);
        $data=array();
        for($i=0;$i<count($header);$i++) {
          $data[ $header[$i] ] = $values[$i];
        }
        
        $this->push($data);
      }
    }
    return true;
  }
  
  /**
   * Salva as alterações dos registros no arquivo
   * @return boolean
   */
  public function commit() {
    
    $success = true;
    
    $output = $this->_formatOutput();
    
    $success && $success = ($file = fopen($this->entity, 'w+'));
    $success && $success = (fwrite($file, $output) !== false);
    $success && $success = fclose($file);
    
    dump(strlen($output));
    
    return $success && parent::commit();
  }
  
  /**
   * Deleta o arquivo (entidade)
   * @return boolen
   */
  public function destroy() {
    $success = is_file($this->entity) ? unlink($this->entity) : false;
    if ($success)
      $this->clearResult();
    
    return $success;
  }
  
  /**
   * Define os campos dos registros. Nos arquivos servirão de cabeçalho. Nos outros formatos
   * como json ou xml, serão propriedades
   * (É protected pois não é possivel alterar os campos em tempo de execução, só ao criar a
   * instancia __construct)
   * @param mixed $entity
   * @access protected
   */
  public function setFields($fields) {
    $this->fields = $fields;
  }
  
  /**
   * Retorna os campos definidos
   * @return mixed
   */
  public function getFields() {
    return $this->fields;
  }
  
}
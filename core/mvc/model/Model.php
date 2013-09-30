<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include CORE_PATH.'/unit/UnitTest.php';

// guarda dados de um objeto: representa uma tabela no banco
abstract class Mapper implements ArrayAccess {
  
  // onde dados do registro ficam guardados
  protected $data;
  
  // entidade que guarda a persistencia do mapper
  // pode ser uma tabela, um nome de arquivo, ou até nada (dados temporarios)
  protected $entity;
  
  // identificador do registro
  // pode ser uma SQLExpression (Dbc), o numero da linha (file), um id, um index de array, etc..
  protected $pointer = array('id'=>null);
  
  // guarda os registros retornados pelo find() ou filter(), e o ponteiro quem vai lidar
  // com o registro unico. o mapper funcionará como um recordset
  protected $result = array();
  protected $internal_pointer = 0;
  protected $count = 0;
  
  private $autoincrement = 1;
  
  private function autoIncrement() {
    return $this->autoincrement++;
  }
  
  protected function _find($search) {
    for($i=0;$i<$this->count;$i++) {
      $found = false;
      if (is_array($search)) {
        $found = reset($search) == $this->result[$i]['data'][ key($search) ];
      } else {
        // TODO
      }
      if ($found) {
        return $i;
      }
    }
    return false;
  }
  
  // procura um registro ou pelo id (pointer) ou por uma expressao
  public function find($pointer) {
    // limpa os dados internos
    $this->nullset();
    if (($offset = $this->_find(array(key($this->pointer)=>$pointer))) !== false) {
      $this->set($this->result[$offset]);
      return $offset;
    }
    return false;
  }
  
  public function nullset() {
    $this->data = null;
    $this->pointer = array(key($this->pointer) => null);
  }
  
  public function set($data) {
    if (!$data) {
      $this->nullset();
      return;
    }
    $values = array_change_key_case($data['data'], CASE_LOWER);
    $this->data = $values;
    $this->pointer = array(key($this->pointer) => $data['data'][key($this->pointer)]);
  }
  
  public function get() {
    $data = $this->result[$this->internal_pointer];
    $this->set($data);
    return $data ? $data['data'] : false;
  }
  
  public function clearResult() {
    $this->result = array();
    $this->internal_pointer = 0;
    $this->count = 0;
  }
  
  // adiciona um registro no result interno no final dos registros
  public function push($data = null) {
    if (!isset($data)) {
      $data = $this->data;
    }
    if ($data === null) return;
    
    if (!isset($data[ key($this->pointer) ]))
      $data[ key($this->pointer) ] = $this->autoIncrement();
    
    $this->result[ $this->count++ ] = array(
        'data' => $data,
        //'pointer' => $data[ key($this->pointer) ], // valor do ponteiro
        'flag' => 0, // flag é usado nos mappers de banco de dados para saber se o registro foi salvo ou não no bd
    );
    $this->internal_pointer = $this->count-1;
  }
  
  // remove o registro do final do result interno
  public function pop() {
    $result = array_pop($this->result);
    --$this->count;
    return $result['data'];
  }
  
  public function splice($offset, $len=1) {
    if ($len === null) $len = $this->count;
    array_splice($this->result, $offset, $len);
    $this->count-=$len;
    return true;
  }
  
  public function remove($pointer = null) {
    if (!isset($pointer)) {
      $pointer = $this->data[ key($this->pointer) ];
    }
    if (($offset = $this->find($pointer)) !== false) {
      array_splice($this->result, $offset, 1);
      --$this->count;
      $this->nullset();
      return true;
    }
    return false;
  }
  
  // adiciona um registro no result interno no final dos registros
  public function unshift($data = null) {
    if (!isset($data)) {
      $data = $this->data;
    }
    if ($data === null) return;
    
    if (!isset($data[ key($this->pointer) ]))
      $data[ key($this->pointer) ] = $this->autoIncrement();
    
    array_unshift($this->result, array(
        'data' => $data,
        //'pointer' => $data[ key($this->pointer) ], // valor do ponteiro
        'flag' => 0, // flag é usado nos mappers de banco de dados para saber se o registro foi salvo ou não no bd
    ));
    ++$this->count;
    $this->internal_pointer = 0;
  }
  
  // remove o registro do inicio do result interno
  public function shift() {
    $result = array_shift($this->result);
    --$this->count;
    return $result['data'];
  }
  
  public function exists() {
    return $this->data !== null || current($this->pointer) !== null;
  }
  
  public function first() {
    $this->internal_pointer = 0;
    $data = $this->result[$this->internal_pointer];
    $this->set($data);
    return $data ? $data['data'] : false;
  }
  
  public function next() {
    ++$this->internal_pointer;
    $data = $this->result[$this->internal_pointer];
    $this->set($data);
    return $data ? $data['data'] : false;
  }
  
  public function prev() {
    --$this->internal_pointer;
    $data = $this->result[$this->internal_pointer];
    $this->set($data);
    return $data ? $data['data'] : false;
  }
  
  public function last() {
    $this->internal_pointer = $this->count-1;
    $data = $this->result[$this->internal_pointer];
    $this->set($data);
    return $data ? $data['data'] : false;
  }
  
  // salva os dados do registro
  public function save() {
    // nao faz nada: o mapper temporario fica com seus dados todos no result
    return true;
  }
  
  // ordena os dados internos por uma coluna
  // se não for definida coluna, usa pointer
  public function sort($column = null, $desc = false) {
    if (!isset($column))
      $column = key($this->pointer);
    
    $this->_quicksort($column, 0, $this->count-1, $desc === true || strtolower($desc) === 'desc');
    return true;
  }
  
  protected function _compare($val1, $val2) {
    if (is_string($val1) && is_string($val2)) {
      return strnatcasecmp($val1, $val2);
    }
    return $val1 < $val2 ? -1 : ($val1 > $val2 ? 1 : 0);
  }
  
  protected function _quicksort($col, $left, $right, $inverse = false) {
    $i = $left;
    $j = $right;
    $pivot = (int)(($i + $j) / 2);
    $val_pivot = $this->result[$pivot]['data'][$col];
    while ($i < $j) {
      if ($inverse) {
        while ($this->_compare($this->result[$i]['data'][$col], $val_pivot) > 0) { // menor
          ++$i;
        }
        while ($this->_compare($this->result[$j]['data'][$col], $val_pivot) < 0) { // maior
          --$j;
        }
      } else {
        while ($this->_compare($this->result[$i]['data'][$col], $val_pivot) < 0) { // menor
          ++$i;
        }
        while ($this->_compare($this->result[$j]['data'][$col], $val_pivot) > 0) { // maior
          --$j;
        }
      }
      if ($i <= $j) {
        $aux = $this->result[$i];
        $this->result[$i] = $this->result[$j];
        $this->result[$j] = $aux;
        ++$i;
        --$j;
      }
    }
    if ($j > $left) $this->_quicksort($col, $left, $j, $inverse);
    if ($i < $right) $this->_quicksort($col, $i, $right, $inverse);
  }
  
  public function setEntity($entity) {
    $this->entity = $entity;
  }
  
  public function getEntity() {
    return $this->entity;
  }
  
  public function setPointer($pointer, $initval = null) {
    $this->pointer = array($pointer => $initval);
  }
  
  public function getPointer() {
    return key($this->pointer);
  }
  
  public function count() {
    return $this->count;
  }
  
  public function __get($name) {
    // sanitize
    $field = strtolower($name);
    if (isset($this->data[$field])) {
      return $field;
    }
  }
  
  // --------------------- INICIO DOS METODOS DE ACESSO POR ARRAY ----
  
  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para adicionar um valor ao objeto (ex: $obj[] = 'valor').
   */
  public function offsetSet($offset, $value) {
    if (is_null($offset)) {
      //$this->data[] = $value;
      $this->data[] = $value;
      //throw new CoreException('Não é possível definir valores para um campo sem nome');
      // TODO: deixar ele acrescentar valores, desde que os data tenham sido definidos
      // e que o valor a ser adicionado não ultrapasse o numero de campos definidos
    } else {
      // sanitize
      $offset = strtolower($offset);
      
      $this->data[ $offset ] = $value;
    }
  }
  
  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para verificar se o elemento existe (ex: isset($obj[1]) ).
   */
  public function offsetExists($offset) {
    return isset($this->data[strtolower($offset)]);
  }

  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para deletar um elemento do objeto (ex: unset($obj[1]) ).
   */
  public function offsetUnset($offset) {
    $this->offsetSet($offset, null);
  }

  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para retornar o valor de um elemento existente (ex: $var = $obj[1] ).
   */
  public function offsetGet($offset) {
    if (is_numeric($offset)) {
      $data = array_values($this->data);
      
      /*if (!isset($data[ $offset ]))
        throw new ModelException('Campo '.$offset.' não existe');*/
      
      return $data[ $offset ];
      
    } else {
      // sanitize
      $offset = strtolower($offset);
      
      /*if (!isset($this->data[ $offset ]))
        throw new ModelException('Campo '.$offset.' não existe');*/
      
      return $this->data[ $offset ];
    }
  }
  
  
  // --------------------- FIM DOS METODOS DE ACESSO POR ARRAY ----
  
}

// faz com que a persistencia do mapper seja no banco de dados
// depends: SQLBase, Dbc
// aguenta mais ou menos 4000~ instancias criadas
class DbcMapper extends Mapper {
  
  const DEFAULT_ID_NAME = 'id';
  const DEFAULT_DELETE_NAME = 'excluido';
  const DEFAULT_DELETE_DATE_NAME = 'excluido_em';
  const MAX_LIMIT_COLLECTION = 5001; // sempre deixe um número redondo + 1
  
  protected $dbc;
  
  protected $fields;
  protected $filters;
  
  protected $permanent_delete = true;
  
  public function __construct() {
    if (!isset($this->dbc))
      $this->dbc = Dbc::getInstance();
    
    if (!isset($this->entity))
      throw new CoreException('Obrigatorio definir uma tabela');
    
    $this->fields = $this->entity->Fields;
  }
  
  public function setFilter($cons) {
    $args = func_get_args();
    
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
    
    $this->filters = array();
    foreach ($constraints as $c) {
      
      if (!($c instanceof SQLExpressionBase)) {
        throw new ModelException('O filtro de um ModelCollection deve sempre ser uma ou mais expressões');
        /*if ($c instanceof SQLFieldBase) {
          $c = new SQLCriteria($c, '=', $c->getValue());
        } else {
          $first_table = $this->_getFirstTable();
          if (!$first_table[(string)$c])
            throw new ModelException('Campo '.$c.' não existe na tabela do Model.');
          $c = new SQLCriteria($first_table[(string)$c], '=', null);
        }*/
      }
      
      $this->filters[$c->getHash()] = $c;
    }
    
  }
  
  public function select() {
    $success = true;

    // instancia de conexao com o banco de dados
    $bd = & $this->dbc;
    
    $where = $this->filters;
    // só pega registros não deletados, se a tabela foi configurada para tal
    if ($this->permanent_delete !== true) {
      $where[] = new SQLCriteria(new SQLField(self::DEFAULT_DELETE_NAME), '=', '0');
    }
    if ($where)
      $where = new SQLExpression('AND', $where);

    $instruction = new SQLISelect($this->fields, $this->entity, $where);
    $sql = (string) $instruction;
    $bind_v = $instruction->getBinds();
    
    //$this->_to_dump($sql, $bind_v);
    //print_r(array( $sql, $bind_v));

    // prepara o sql
    if ($success = $success && $bd->prepare($sql)) {
      foreach ($bind_v as $k => $value) {
        // binda o valor
        $bd->bind_param($bind_v[$k]);
      }

      // executa a query
      if ($success = $success && $bd->execute()) {

        if ($success = $success && ($bd->num_rows() >= 1)) {
          while ($row = $bd->fetch_assoc()) {
            // adiciona cada registro no collection interno
            $this->push($row);
          }

          // retira os campos internos
          //unset($row['TOTAL'], $row['R_N']);
          
          // salva os dados nos campos
          // define que o registro selecionado para edicao é o primeiro 
          $this->first();
          /*foreach ($row as $col => $val) {
            $this->fields[SQLBase::key($col)]->setValue($val);
          }*/
          
        }

        //$this->total = $success ? 1 : 0;
      }
    }
    // sempre limpar o prepare, não importa se retornou true ou false
    $bd->free();
    
    // guarda os valores atuais para log de alteracao
    //if ($success)
      //$this->saveState();

    return $success; // retorna o registro fetchado
  }
  
  public function delete() {
    $success = true;

    // instancia de conexao com o banco de dados
    $bd = & $this->dbc;
    
    $where = $this->filters;
    // só pega registros não deletados, se a tabela foi configurada para tal
    if ($this->permanent_delete !== true) {
      $where[] = _c(new SQLField(self::DEFAULT_DELETE_NAME), '=', '0');
    }
    $where = new SQLExpression('AND', $where);

    // cria o SQL
    if ($this->permanent_delete === true) {
      // se for permanente, deleta
      //$this->buildSQL('DELETE');
      $instr = new SQLIDelete($this->entity, $where);
    } else {
      // se não for permanente, só fazer update
      $exc_field = new SQLField(self::DEFAULT_DELETE_NAME);
      $exc_field->setValue('1');
      $excem_field = new SQLField(self::DEFAULT_DELETE_DATE_NAME);
      $excem_field->setValue(new SQLTDateTime());

      //$this->buildSQL('UPDATE');
      $instr = new SQLIUpdate($this->entity, array($exc_field, $excem_field), $where);
    }

    // pega as variaveis criadas do buildSQL
    $sql = (string)$instr;
    //$bind_v = $instr->getBinds();
    
    //print_r(array( $sql, $bind_v));

    // evita deletar toda a tabela (questoes de seguranca 12/03/2013)
    /*if (empty($bind_v)) {
      throw new CoreException('Alerta: tentativa de excluir toda a tabela!');
    }*/

    // prepara o sql
    if ($success = $success && $bd->prepare($sql)) {
      foreach ($bind_v as $k => $value) {
        // binda o valor
        $bd->bind_param($bind_v[$k]);
      }

      // executa a query
      if ($success = $success && $bd->execute()) {

        $affected = $bd->affected_rows();
        
        if ($affected > 0) {
          // limpa o objeto, setando todos os valores e criterias-igual para null
          //$this->recordset_data = array();
          $this->nullset();
          $this->clearResult();
          
        }
        
        //$this->total = count($this->recordset_data);
        
        //if ($this->log)
          //Logger::delete($this->_getFirstTable(), $success);
        
      }
    }
    // sempre limpar o prepare, não importa se retornou true ou false
    $bd->free();

    return $affected;
  }
  
}

// persistencia do mapper em arquivo
// collection: ~16.000 (20 campos)
class FileMapper extends Mapper {
  
}

// persistencia do mapper em xml
class XmlMapper extends Mapper {
  
}

// persistencia do mapper em arquivo json
class JsonMapper extends FileMapper {
  
}

/**
 * Description of Model
 *
 * @author usuario
 */
class Model implements ArrayAccess {
  
  protected $dbc; // DB
    
  protected $behaviors = array();
  
  protected $table;
  
  protected $fields = array();
  protected $pristine_fields = array(); // copia dos fields, mas sempre com os valores anteriores
  protected $pristine = true; // flag que define se um model foi alterado ou não
  
  protected $filter = array();
  
  protected $total = 0;
  
  public function addBehavior($behavior) {
    if (is_string($behavior)) {
      $behavior = new $behavior();
    }
    $this->behaviors[] = $behavior;
  }
  
  // para chamada de metodos dos behaviors, que serao injetados no model
  public function __call($name, $arguments) {
    if (empty($this->behaviors))
      throw new CoreException('Método '.$name.'() não existe no Model '.get_class());
    
    foreach ($this->behaviors as $behavior) {
      if (!method_exists($behavior, $name)) {
        throw new CoreException('Método '.$name.'() não existe no Model '.get_class());
      }
      // injeta o model na funcao do behavior
      call_user_func_array(array($behavior, $name), array_merge(array($this), $arguments));
    }
  }
  
  // para chamada de 
  public function __get($name) {
    
    if (isset($this->fields[$name])) {
      return $this->fields[$name];
    }
  }
  
  // --------------------- INICIO DOS METODOS DE ACESSO POR ARRAY ----
  
  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para adicionar um valor ao objeto (ex: $obj[] = 'valor').
   */
  public function offsetSet($offset, $value) {
    if (is_null($offset)) {
      //$this->data[] = $value;
      throw new ModelException('Não é possível definir valores para um campo sem nome');
      // TODO: deixar ele acrescentar valores, desde que os fields tenham sido definidos
      // e que o valor a ser adicionado não ultrapasse o numero de campos definidos
    } else {
      // sanitize
      $offset = strtolower($offset);
      // procura o campo
      $field = $this->fields[ $offset ];
      if (!($field instanceof SQLField)) {
        throw new ModelException('Campo '.$offset.' não existe');
      }
      
      $field->setValue($value);
    }
  }
  
  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para verificar se o elemento existe (ex: isset($obj[1]) ).
   */
  public function offsetExists($offset) {
    return isset($this->fields[strtolower($offset)]);
  }

  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para deletar um elemento do objeto (ex: unset($obj[1]) ).
   */
  public function offsetUnset($offset) {
    $this->offsetSet($offset, null);
  }

  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para retornar o valor de um elemento existente (ex: $var = $obj[1] ).
   */
  public function offsetGet($offset) {
    if (is_numeric($offset)) {
      $fields = array_values($this->fields);
      
      if (!isset($fields[ $offset ]))
        throw new ModelException('Campo '.$offset.' não existe');
      
      return $fields[ $offset ]->getValue();
      
    } else {
      // sanitize
      $offset = strtolower($offset);
      
      if (!isset($this->fields[ $offset ]))
        throw new ModelException('Campo '.$offset.' não existe');
      
      return $this->fields[ $offset ]->getValue();
    }
  }
  
  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para contar os elementos do array (ex: count($var) )
   */
  public function count() {
    return count($this->data);
  }
  // --------------------- FIM DOS METODOS DE ACESSO POR ARRAY ----
  
}

class Behavior {
  
  
  
}

class CollectionBehavior extends Behavior {
  
  
  
  
}

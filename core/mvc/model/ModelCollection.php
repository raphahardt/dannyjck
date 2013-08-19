<?php

Core::uses('ModelCommon', 'core/mvc/model');
Core::uses('Logger', 'core/logger');

class ModelCollection extends ModelCommon implements Iterator {
  
  protected $recordset_data = array();
  protected $pointer = 0;
  
  protected $add_data = array(); // recordset dos registros que vao ser inseridos na tabela com INSERT
  
  protected $offset = 0;
  protected $limit = self::MAX_LIMIT_COLLECTION;
  protected $max_limit = self::MAX_LIMIT_COLLECTION;
  
  protected $order = array();
  
  /**
   * Cria um objeto Model. Podem ser passados pré-filtros chamados "constraints" que
   * irão selecionar o registro certo da tabela que o Model representa. Pode ser passado
   * tanto um integer quanto um array de campos.
   * @param array|int $constraint Pré-filtro para selecionar o registro desejado da tabela.
   *                              Pode ser um inteiro (ex: 12, 56, 3214), um array
   *                              associativo de nome/valor (ex: array('campo1' => 'valor')),
   *                              ou um array de SQLField (ex: array($campo1, $campo2))
   * @throws ModelException
   */
  public function __construct() {
    $this->pointer = 0;
    $this->add_data = array();
    
    parent::__construct();
  }
  
  protected function initializeModel() {
    
    $this->pointer = 0;
    $this->add_data = array();
    
    if (empty($this->tables))
      throw new ModelException('Defina uma tabela para o ModelCollection '.get_class($this));
    
    if (empty($this->fields))
      throw new ModelException('Defina os campos para o ModelCollection '.get_class($this));
    
    parent::__construct();
  }
  
  // --------------------- INICIO DOS METODOS ITERATIVOS ----
  /**
   * NÃO MUDAR!<br>
   * Método iterativo. Serve para reiniciar o ponteiro interno.
   */
  public function rewind() {
    $this->pointer = 0;
  }

  /**
   * NÃO MUDAR!<br>
   * Método iterativo. Serve para retornar o model atual baseado no ponteiro interno.
   * @return mixed Model
   */
  public function current() {
    //return $this->offsetGet($this->pointer);
    return $this->recordset_data[$this->pointer];
  }

  /**
   * NÃO MUDAR!<br>
   * Método iterativo. Serve para retornar o ponteiro interno atual.
   * @return int Ponteiro
   */
  public function key() {
    // retorna o numero da linha
    return $this->recordset_data[$this->pointer]['N'];
    //return $this->pointer;
  }

  /**
   * NÃO MUDAR!<br>
   * Método iterativo. Serve para andar para o próximo model.
   */
  public function next() {
    ++$this->pointer;
  }

  /**
   * NÃO MUDAR!<br>
   * Método iterativo. Serve para verificar quando o iterador deve parar.
   * @return bool <b>TRUE</b> continua a iteração, <b>FALSE</b> para.
   */
  public function valid() {
    return isset($this->recordset_data[$this->pointer]);
  }
  
  /**
   * NÃO MUDAR!<br>
   * Método iterativo. Serve para retornar o model baseado num ponteiro. Pode também ser
   * utilizado acesso por array (ex: $obj[0] ao inves de $obj->item(0))
   * @return mixed Model
   */
  public function item($pointer) {
    return $this->offsetGet($pointer);
    //return $this->recordset_data[$pointer];
  }
  // --------------------- FIM DOS METODOS ITERATIVOS ----
  
  // --------------------- INICIO DOS METODOS DE ACESSO POR ARRAY ----
  
  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para adicionar um valor ao objeto (ex: $obj[] = 'valor').
   */
  public function offsetSet($offset, $value) {
    if (is_null($offset)) {
      $this->recordset_data[] = $value;
    } else {
      $this->recordset_data[$offset] = $value;
    }
  }
  
  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para verificar se o elemento existe (ex: isset($obj[1]) ).
   */
  public function offsetExists($offset) {
    return isset($this->recordset_data[$offset]);
  }

  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para deletar um elemento do objeto (ex: unset($obj[1]) ).
   */
  public function offsetUnset($offset) {
    unset($this->recordset_data[$offset]);
  }

  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para retornar o valor de um elemento existente (ex: $var = $obj[1] ).
   */
  public function offsetGet($offset) {
    if (is_numeric($offset)) {
      if ($row = $this->recordset_data[$offset]) {
        $modelname = str_replace('Collection', '', get_class($this));
        $model = new $modelname();
        foreach ($row as $col => $val) {
          if ($col == 'N')
            continue;
          //if (!$model->fields[SQLBase::key($col)])
            //$model->addField($col); //TODO
          $model->fields[SQLBase::key($col)]->setValue($val);
        }
        return $model;
      }
      return null;
      //return isset($this->recordset_data[$offset]) ? $this->recordset_data[$offset] : null;
    } else {
      return $this->fields[ SQLBase::key($offset) ];
    }
  }
  
  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para contar os elementos do array (ex: count($var) )
   * @return int
   */
  public function count() {
    return $this->total;
    //return count($this->recordset_data);
  }
  // --------------------- FIM DOS METODOS DE ACESSO POR ARRAY ----
  
  public function setValue($field, $value) {
    $field = $this->getField($field);
    $field->setValue($value);
  }
  
  public function getValue($field) {
    $field = $this->getField($field);
    return $field->getValue();
  }
  
  public function setOffset($offset) {
    if ($offset < 0) $offset = 0;
    //if ($start > $this->limit) $start = $this->limit;
    $this->offset = $offset;
  }
  
  public function setStart($offset) {
    $this->setOffset($offset);
  }
  
  public function setLimit($limit) {
    if ($limit > PHP_INT_MAX) $limit = PHP_INT_MAX;
    //if ($limit < $this->start) $limit = $this->start;
    $this->limit = $limit;
  }
  
  public function setMaxLimit($limit) {
    if ($limit > PHP_INT_MAX) $limit = PHP_INT_MAX;
    //if ($limit < $this->start) $limit = $this->start;
    $this->max_limit = $limit;
  }
  
  public function setOrderBy($order) {
    $args = func_get_args();
    
    $orders = array();
    if (count($args) > 1) {
      if (count($args) == 2 && is_string($args[1])) {
        $orders[] = array($args[0], $args[1]);
      } else {
        $orders = $args;
      }
    } elseif (count($args) == 1) {
      $order = $args[0];
      if (is_array($order)) {
        if (count($order) == 2 && is_string($order[1])) {
          $orders[] = array($order[0], $order[1]);
        } else {
          $orders = array_values($order);
        }
      } else {
        $orders[] = $order;
      }
    }
    
    $this->order = array();
    foreach ($orders as $o) {
      if (is_array($o)) {
        $direction = $o[1];
        $o = $o[0];
        $o->setOrder($direction);
      }
      $this->order[] = $o;
    }
    
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
    
    $this->filter = array();
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
      
      $this->filter[$c->getHash()] = $c;
    }
    
  }
  
  public function add($data) {
    $this->add_data = $data;
  }
  
  public function insert() {
    $success = true;
    $this->total = 0;

    // instancia de conexao com o banco de dados
    $bd = & $this->database;
    
    // separa os registros em 'chunks' de 100 registros
    $all_data = array_chunk($this->add_data, 100, true);
    
    foreach ($all_data as $data) {

      $instr = new SQLIInsertAll($this->_getFirstTable(), $data);

      // pega as variaveis criadas do buildSQL
      $sql = (string) $instr;
      $bind_v = SQLBase::getBinds();
      
      //print_r(array($sql,$bind_v));
      //continue;

      // prepara o sql
      if ($success = $success && $bd->prepare($sql)) {
        foreach ($bind_v as $k => $value) {
          // binda o valor
          $bd->bind_param($bind_v[$k]);
        }

        // executa a query
        if ($success = $success && $bd->execute()) {
          // seta o id
          //$id = $bd->insert_id();
          $affected = $bd->affected_rows();

          // volta os campos retornados para os criterias
          if ($affected > 0) {
            while (list($key) = each($data)) {
              //$this->recordset_data[] = $this->add_data[$key];
              unset($this->add_data[$key]);
            }
            /*foreach ($criteria_fields as $key => $field) {
              $field->setValue( $criteria_values[$key] ); // seta o campo também para o novo valor
              foreach ($this->_criterias_from_constraints as $criteria) {
                if ($field === $criteria->getField() && $criteria->getOperator() === '=') {
                  $criteria->setValue( $criteria_values[$key] );
                }
              }
            }*/
          }

          $success = $success && ($affected > 0);

          $this->total += $affected;

          if ($this->log)
            Logger::insert($this->_getFirstTable(), array('cnpj' => $this->fields['cnpj']->getValue(), 'times' => $this->fields['times']->getValue()), $success);

        }
      }
      // sempre limpar o prepare, não importa se retornou true ou false
      $bd->free();
      
    }

    return $success;
    
  }
  
  public function select() {
    $success = true;

    // instancia de conexao com o banco de dados
    $bd = & $this->database;
    
    $where = $this->filter;
    // só pega registros não deletados, se a tabela foi configurada para tal
    if ($this->permanentDelete !== true) {
      $where[] = _c(new SQLField(self::DEFAULT_DELETE_NAME), '=', '0');
    }
    //$where[] = _c(new SQLField('ROWNUM'), '<=', max($this->offset+$this->limit, $this->max_limit));
    if ($where)
      $where = new SQLExpression('AND', $where);

    // cria o SQL
    
    $instruction = new SQLISelect($this->fields, $this->tables, $where, $this->order);
    //$sup_instruction = new SQLISelect(array('*'), $instruction, _c($frnw, 'BETWEEN', array($this->offset+1, $this->offset+$this->limit)));
    $sql = (string) $instruction;
    $bind_v = $instruction->getBinds();
    
    $this->_to_dump($sql, $bind_v);
    //print_r(array( $sql, $bind_v));
    
    $this->recordset_data = array();

    // prepara o sql
    if ($success = $success && $bd->prepare($sql)) {
      foreach ($bind_v as $k => $value) {
        // binda o valor
        $bd->bind_param($bind_v[$k]);
      }

      // executa a query
      if ($success = $success && $bd->execute()) {

        if ($success = $success && (($this->total = $bd->num_rows()) > 0)) {
          
          while ($row = $bd->fetch_assoc()) {
            // retira os campos internos
            $num = $row['R_N'];
            unset($row['TOTAL'], $row['R_N']);
            
            // salva os dados nos campos
            foreach ($row as $col => $val) {
              //if (!$model->fields[SQLBase::key($col)])
                //$model->addField($col); //TODO
              //$model->fields[SQLBase::key($col)]->setValue($val);
            }
            
            $this->recordset_data[] = array_change_key_case($row, CASE_LOWER) + array('N' => $num);
          }
          
        }

      }
    }
    // sempre limpar o prepare, não importa se retornou true ou false
    $bd->free();
    
    return $success; // retorna o registro fetchado
  }
  
  public function delete() {
    $success = true;

    // instancia de conexao com o banco de dados
    $bd = & $this->database;
    
    $where = $this->filter;
    // só pega registros não deletados, se a tabela foi configurada para tal
    if ($this->permanentDelete !== true) {
      $where[] = _c(new SQLField(self::DEFAULT_DELETE_NAME), '=', '0');
    }
    $where = new SQLExpression('AND', $where);

    // cria o SQL
    if ($this->permanentDelete === true) {
      // se for permanente, deleta
      //$this->buildSQL('DELETE');
      $instr = new SQLIDelete($this->_getFirstTable(), $where);
    } else {
      // se não for permanente, só fazer update
      $exc_field = new SQLField(self::DEFAULT_DELETE_NAME);
      $exc_field->setValue('1');
      $excem_field = new SQLField(self::DEFAULT_DELETE_DATE_NAME);
      $excem_field->setValue(new SQLTDateTime());

      //$this->buildSQL('UPDATE');
      $instr = new SQLIUpdate($this->_getFirstTable(), array($exc_field, $excem_field), $where);
    }

    // pega as variaveis criadas do buildSQL
    $sql = (string)$instr;
    $bind_v = $instr->getBinds();
    
    //print_r(array( $sql, $bind_v));

    // evita deletar toda a tabela (questoes de seguranca 12/03/2013)
    if (empty($bind_v)) {
      throw new ModelException('Alerta: tentativa de excluir toda a tabela!');
    }

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
          $this->recordset_data = array();
          
        }
        
        $this->total = count($this->recordset_data);
        
        if ($this->log)
          Logger::delete($this->_getFirstTable(), $success);
        
      }
    }
    // sempre limpar o prepare, não importa se retornou true ou false
    $bd->free();

    return $affected;
  }
  
}
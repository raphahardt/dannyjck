<?php

Core::uses('ModelCommon', 'core/mvc/model');
Core::uses('Logger', 'core/logger');

class Model extends ModelCommon {
  
  /**
   * São os campos criterias que são usados como 'filtro' para instruções como 
   * UPDATE e DELETE de um Model. O padrão é o campo 'id' da tabela[0] do Model.
   * @var array|SQLExpressionBase
   */
  protected $constraints = array();
  protected $_criterias_from_constraints = array();
  
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
    $args = func_get_args();
    call_user_func_array(array($this,'initializeModel'), $args);
    parent::__construct();
  }
  
  protected function initializeModel($constraint = array()) {
    
    if (empty($this->tables))
      throw new ModelException('Defina uma tabela para o Model '.get_class($this));
    
    if (empty($this->fields))
      throw new ModelException('Defina os campos para o Model '.get_class($this));
    
    $args = func_get_args();
    
    if (is_array($constraint) && !empty($constraint)) {
      $fields = array_keys($constraint);
      
      $this->setFilter($fields);
      $this->setFilterValues($constraint);
    } else {
      if (empty($this->constraints)) {
        $this->setFilter(self::DEFAULT_ID_NAME);
      }
      $this->setFilterValues($args);
    }
    
    parent::__construct();
    
  }
  
  /**
   * Define por qual constraint o Model irá fazer as operações de UPDATE e DELETE.
   * No caso do Model, de SELECT também.
   * @param string|SQLFieldBase|SQLExpressionBase $cons Constraint da tabela. Pode ser um campo
   *                                      comum (será transformado em SQLCriteria), um
   *                                      SQLCriteria ou uma expressão (SQLExpression), ou
   *                                      uma string. No caso de string, será o campo da
   *                                      primeira tabela definida
   */
  protected function defineConstraint($cons) {
    $args = func_get_args();
    call_user_func_array(array($this, 'setFilter'), $args);
  }
  
  private function _extractCriterias(SQLBase $expr, &$array) {
    if ($expr instanceof SQLExpression) {
      if (!isset($array)) $array = array();
      
      $elements = $expr->get();
      
      foreach ($elements as $hash => $element) {
        if ($element instanceof SQLCriteria) {
          $array[$hash] = $element;
        } elseif ($element instanceof SQLExpression) {
          $subexprs = $expr->getExpressions();
          if (!empty($subexprs)) {
            foreach ($subexprs as $subexpr) {
              $this->_extractCriterias($subexpr, $array);
            }
          }
        }
      }
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
        $constraints = array_values($cons);
      } else {
        $constraints[] = $cons;
      }
    }
    
    $this->constraints = $this->_criterias_from_constraints = array();
    foreach ($constraints as $c) {
      
      if (!($c instanceof SQLExpressionBase)) {
        if ($c instanceof SQLFieldBase) {
          $c = new SQLCriteria($c, '=', $c->getValue());
        } else {
          $first_table = $this->_getFirstTable();
          if (!$first_table[(string)$c])
            throw new ModelException('Campo '.$c.' não existe na tabela do Model.');
          $c = new SQLCriteria($first_table[(string)$c], '=', null);
        }
      }
      
      $this->constraints[$c->getHash()] = $c;
    }
    
    foreach ($this->constraints as $c) {
      if ($c instanceof SQLExpression) {
        $this->_extractCriterias($c, $this->_criterias_from_constraints);
      } elseif ($c instanceof SQLCriteria) {
        $this->_criterias_from_constraints[$c->getHash()] = $c;
      }
    }
    
  }
  
  /**
   * Define um valor para uma constraint. Se quiser mudar o ID do Model, use:
   * $obj->setConstraintValue('id', 123)
   * @param string $index
   * @param mixed $constraint
   */
  public function setConstraintValue($index, $constraint) {
    if (is_numeric($index)) {
      // buscar por numero
      $counter = 0;
      foreach ($this->constraints as $key => $val) {
        if ($counter == $index) {
          $index = $key;
          break;
        }
        ++$counter;
      }
    } else {
      // por valor
      $index = SQLBase::key($index);
    }
    
    $c = $this->constraints[$index];
    if ($c instanceof SQLBase)
      $c->setValue($constraint);
  }
  
  /**
   * 
   * @param array $constraint
   */
  public function setFilterValues($constraint) {
    $args = func_get_args();
    
    $constrs = array();
    if (count($args) > 1) {
      $constrs = $args;
    } elseif (count($args) == 1) {
      $constraint = $args[0];
      if (is_array($constraint)) {
        $constrs = array_values($constraint);
      } else {
        $constrs[] = $constraint;
      }
    }
    
    reset($constrs);
    foreach ($this->_criterias_from_constraints as $key => $v) {
      list(,$val) = each($constrs);
      $this->_criterias_from_constraints[SQLBase::key($key)]->setValue($val);
    }
  }
  
  public function getConstraint($index) {
    if (is_numeric($index)) {
      // buscar por numero
      $counter = 0;
      foreach ($this->constraints as $key => $val) {
        if ($counter == $index) {
          $index = $key;
          break;
        }
        ++$counter;
      }
    } else {
      // por valor
      $index = SQLBase::key($index);
    }
    return $this->constraints[$index];
  }
  
  public function getConstraints() {
    return $this->constraints;
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
      // procura o campo
      $field = $this->fields[ SQLBase::key($offset) ];
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
    return isset($this->fields[SQLBase::key($offset)]);
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
      if (!isset($this->fields[ SQLBase::key($offset) ]))
        throw new ModelException('Campo '.$offset.' não existe');
      
      return $this->fields[ SQLBase::key($offset) ]->getValue();
    }
  }
  
  /**
   * NÃO MUDAR!<br>
   * Método de acesso como array. Serve para contar os elementos do array (ex: count($var) )
   */
  public function count() {
    return count($this->fields);
  }
  // --------------------- FIM DOS METODOS DE ACESSO POR ARRAY ----
  
  /*function __get($name) {
    switch ($name) {
      case 'Fields':
        return $this->fields;
      case 'Tables':
        return $this->tables;
      case 'Table':
        return $this->_getFirstTable();
      default:
        throw new SQLException('Propriedade '.$name.' não existe');
    }
  }*/
  
  public function select() {
    $success = true;

    // instancia de conexao com o banco de dados
    $bd = & $this->database;
    
    $where = $this->constraints;
    // só pega registros não deletados, se a tabela foi configurada para tal
    if ($this->permanentDelete !== true) {
      $where[] = _c(new SQLField(self::DEFAULT_DELETE_NAME), '=', '0');
    }
    $where = new SQLExpression('AND', $where);

    // cria o SQL
    $ftotal = new SQLField('*');
    $ftotal->setFunction('count');
    $ftotal->setOver('order by 1');
    $ftotal->setAlias('total');
    
    $instruction = new SQLISelect(array_merge($this->fields, array($ftotal)), $this->tables, $where);
    $sql = (string) $instruction;
    $bind_v = $instruction->getBinds();
    
    //print_r(array( $sql, $bind_v));

    // prepara o sql
    if ($success = $success && $bd->prepare($sql)) {
      foreach ($bind_v as $k => $value) {
        // binda o valor
        $bd->bind_param($k, $bind_v[$k]);
      }

      // executa a query
      if ($success = $success && $bd->execute()) {

        if ($success = $success && ($bd->num_rows() == 1)) {
          $row = $bd->fetch_assoc();

          // retira os campos internos
          unset($row['TOTAL'], $row['R_N']);
          
          // salva os dados nos campos
          foreach ($row as $col => $val) {
            $this->fields[SQLBase::key($col)]->setValue($val);
          }
          
        }

        $this->total = $success ? 1 : 0;
      }
    }
    // sempre limpar o prepare, não importa se retornou true ou false
    $bd->free();
    
    // guarda os valores atuais para log de alteracao
    if ($success)
      $this->saveState();

    return $success; // retorna o registro fetchado
  }
  
  public function update() {
    $success = true;
    
    // instancia de conexao com o banco de dados
    $bd = & $this->database;

    $where = $this->constraints;
    // só pega registros não deletados, se a tabela foi configurada para tal
    if ($this->permanentDelete !== true) {
      $where[] = _c(new SQLField(self::DEFAULT_DELETE_NAME), '=', '0');
    }
    $where = new SQLExpression('AND', $where);
    
    $instr = new SQLIUpdate($this->_getFirstTable(), $this->_getNotNullFields(), $where);
    
    $sql = (string) $instr;
    $bind_v = $instr->getBinds();

    // prepara o sql
    if ($success = $success && $bd->prepare($sql)) {
      foreach ($bind_v as $k => $value) {
        // binda o valor
        $bd->bind_param($k, $bind_v[$k]);
      }

      // executa a query
      if ($success = $success && $bd->execute()) {

        $affected = $bd->affected_rows();
        
        // muda os criterias para os novos valores
        foreach ($this->fields as $field) {
          foreach ($this->_criterias_from_constraints as $criteria) {
            if ($field === $criteria->getField() && $criteria->getOperator() === '=') {
              $criteria->setValue( $field->getValue() );
            }
          }
        }
        
        if ($this->log)
          Logger::update($this->_getFirstTable(), $this->_getUpdatedValues(), $success);
      }
    }
    // sempre limpar o prepare, não importa se retornou true ou false
    $bd->free();
    
    if ($success)
      $this->saveState();

    return $affected;
  }
  
  public function insert() {
    $success = true;

    // instancia de conexao com o banco de dados
    $bd = & $this->database;
    
    $criteria_fields = array();
    $criteria_values = array();
    foreach ($this->_criterias_from_constraints as $criteria) {
      $criteria_fields[$criteria->getField()->getHash()] = $criteria->getField();
      $criteria_values[$criteria->getField()->getHash()] = null;
    }

    // cria o SQL
    $instr_returning = new SQLIReturning($criteria_fields, $criteria_fields);
    $instr = new SQLIInsert($this->_getFirstTable(), $this->_getNotNullFields(), $instr_returning);

    // pega as variaveis criadas do buildSQL
    $sql = (string) $instr;
    $bind_v = SQLBase::getBinds();
    
    //print_r(array( $sql, $bind_v));

    // prepara o sql
    if ($success = $success && $bd->prepare($sql)) {
      foreach ($bind_v as $k => $value) {
        if (substr($k, 0, 6) === ':into_') {
          // se for into, jogar na var que pediu
          $k_field = substr($k, 6);
          $bd->bind_param($k, $criteria_values[$k_field], 4000); // para valores out, deve-se garantir o espaço
        } else {
          // binda o valor
          $bd->bind_param($k, $bind_v[$k]);
        }
      }

      // executa a query
      if ($success = $success && $bd->execute()) {
        // seta o id
        //$id = $bd->insert_id();
        $affected = $bd->affected_rows();
        
        // volta os campos retornados para os criterias
        if ($affected > 0) {
          foreach ($criteria_fields as $key => $field) {
            $field->setValue( $criteria_values[$key] ); // seta o campo também para o novo valor
            foreach ($this->_criterias_from_constraints as $criteria) {
              if ($field === $criteria->getField() && $criteria->getOperator() === '=') {
                $criteria->setValue( $criteria_values[$key] );
              }
            }
          }
        }
        
        $success = $success && ($affected > 0);
        
        $this->total = $success ? 1 : 0;
        
        if ($this->log)
          Logger::insert($this->_getFirstTable(), array('cnpj' => $this->fields['cnpj']->getValue(), 'times' => $this->fields['times']->getValue()), $success);
        
      }
    }
    // sempre limpar o prepare, não importa se retornou true ou false
    $bd->free();
    
    if ($success)
      $this->saveState();

    return $success;
    
  }
  
  public function delete() {
    $success = true;

    // instancia de conexao com o banco de dados
    $bd = & $this->database;
    
    $where = $this->constraints;
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
        $bd->bind_param($k, $bind_v[$k]);
      }

      // executa a query
      if ($success = $success && $bd->execute()) {

        $affected = $bd->affected_rows();
        
        if ($affected > 0) {
          // limpa o objeto, setando todos os valores e criterias-igual para null
          foreach ($this->fields as $field) {
            $field->setValue( null );
          }
          foreach ($this->_criterias_from_constraints as $criteria) {
            if ($criteria->getOperator() === '=') {
              $criteria->setValue( null );
            }
          }
        }
        
        $this->total = $affected ? 0 : 1;
        
        if ($this->log)
          Logger::delete($this->_getFirstTable(), $success);
        
      }
    }
    // sempre limpar o prepare, não importa se retornou true ou false
    $bd->free();
    
    if ($success)
      $this->saveState();

    return $affected;
  }
  
}
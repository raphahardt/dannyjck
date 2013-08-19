<?php

Core::depends('SQLBase');

class ModelException extends CoreException {}

abstract class ModelCommon implements ArrayAccess, Countable {
  
  /**
   * Nome padrão para o PRIMARY KEY de todas as tabelas do banco.
   */
  const DEFAULT_ID_NAME = 'id';
  const DEFAULT_DELETE_NAME = 'excluido';
  const DEFAULT_DELETE_DATE_NAME = 'excluido_em';
  const MAX_LIMIT_COLLECTION = 5001; // sempre deixe um número redondo + 1
  
  /**
   * Entidades (tabelas, joins, procedures) que definem o Model/ModelCollection.
   * No caso de um Model, a primeira tabela sempre será mandatória nos updates e deletes.
   * @var array|SQLEntityBase|SQLProcedure
   */
  protected $tables = array();
  
  /**
   * Campos que definem o Model/ModelCollection. Esta propriedade é apenas uma "cópia"
   * dos campos da tabela definidos pelo $tables. Servem para serem modificados.
   * @var array|SQLFieldBase|SQLParam
   */
  protected $fields = array();
  
  protected $fields_old_values = array();
  
  /**
   * Filtro que servirá para buscar os dados do Model/ModelCollection. No caso de Model,
   * a consulta deve retornar apenas 1 registro, senão sempre retornará FALSE. Este filtro
   * serve apenas para consultas SELECT, os filtros de UPDATE e DELETE usarão $constraints
   * (somente no caso de Model)
   * @var ISQLExpression
   */
  protected $filter = array();
  
  protected $total = 0;
  
  protected $permanentDelete = false;
  
  protected $database;
  
  protected $log = true;
  
  /**
   * Status da transação e se o model está em transação
   * @var boolean
   */
  protected $transaction = false;
  protected $transaction_status = null; // guarda o status do autocommit anterior
  
  static $dump = array();
  
  protected function _to_dump($sql, $bind) {
    self::$dump[] = array(
      'sql' => $sql,
      'bind' => $bind
    );
  }

  public function __construct() {
    $this->database = Dbc::getInstance();
  }
  
  /**
   * Define as tabelas que vão ser usadas pelo Model/ModelCollection
   * @param type $tables
   * @throws ModelException
   */
  protected function defineTables($tables) {
    if (!is_array($tables))
      $tables = array($tables);
    
    $this->tables = array();
    foreach ($tables as $o) {
      if (!($o instanceof SQLEntityBase || $o instanceof SQLProcedure))
        throw new ModelException('Tipo inválido. Verifique os tipos de entidades você '.
                'definiu para o Model '.get_class($this));
      
      $this->tables[ $o->getHash() ] = $o;
    }
  }
  
  /**
   * Define os campos que vão ser usadas pelo Model/ModelCollection
   * @param type $fields
   */
  protected function defineFields($fields) {
    if (!is_array($fields))
      $fields = array($fields);
    
    $this->fields = array();
    foreach ($fields as $o) {
      $this->setField($o);
    }
  }
  
  /**
   * Adiciona um campo que vai ser usada pela tabela
   * @param type $field
   * @param type $alias
   * @throws ModelException
   */
  public function setField($field, $alias = null) {
    
    if ($field instanceof SQLBase) {
      if (!($field instanceof SQLFieldBase || $field instanceof SQLParam))
        throw new ModelException('Tipo inválido. Verifique os tipos de campos '.
                'você definiu para o Model '.get_class($this));
    } else {
      $table = $this->_getFirstTable();
      $table->addField((string)$field, $alias);
      $field = $table[$alias ? $alias : (string)$field];
    }

    $this->fields[ $field->getHash() ] = $field;
  }
  
  /**
   * Retorna o campo da tabela do Model
   * @param type $field
   * @return type
   * @throws ModelException
   */
  public function getField($field) {
    
    if (!isset($this->fields[ SQLBase::key($field) ]))
      throw new ModelException('Campo '.$field.' não existe');

    return $this->fields[ SQLBase::key($field) ];
  }
  
  /**
   * Define o valor para o campo da tabela do Model
   * @param type $field
   * @param type $value
   */
  public function setValue($field, $value) {
    $field = $this->getField($field);
    $field->setValue($value);
  }
  
  /**
   * Retorna o valor para o campo da tabela do Model
   * @param type $field
   * @return type
   */
  public function getValue($field) {
    $field = $this->getField($field);
    return $field->getValue();
  }
  
  /**
   * Retorna a primeira tabela definida pelo Model/ModelCollection.
   * Se foi definido um join como tabela, retorna sempre a primeira tabela.
   * @return type
   */
  protected function _getFirstTable() {
    $firstTable = reset($this->tables);
    //if (!$firstTable) return null;
    
    if ($firstTable instanceof SQLJoin) {
      $firstTable = $firstTable->getTable1();
    }
    return $firstTable;
  }
  
  /**
   * Função auxiliar que retorna apenas os campos que foram definidos valores.
   * É usado para as instruções de UPDATE e INSERT só alterarem os campos alterados
   * @return type
   */
  protected function _getNotNullFields() {
    $fields = array();
    foreach ($this->fields as $k => $f) {
      if ($f->getValue() !== null) {
        $fields[$k] = $f;
      }
    }
    return $fields;
  }
  
  /**
   * Grava os valores atuais numa variável interna. Serve para criar o log na alteração
   * de valores.
   */
  public function saveState() {
    foreach ($this->fields as $k => $f) {
      $this->fields_old_values[$k] = $f->getValue();
    }
  }
  
  /**
   * Função auxiliar que retorna apenas os campos que foram definidos valores.
   * É usado para o log retornar só os campos que foram alterados.
   * @return type
   */
  protected function _getUpdatedValues() {
    $values = array();
    foreach ($this->fields as $k => $f) {
      if ($f->getValue() != $this->fields_old_values[$k]) {
        $values[$k] = array($this->fields_old_values[$k], $f->getValue());
      }
    }
    return $values;
  }
  
  /**
   * Retorna o total de linhas do SQL do Model/ModelCollection
   * @return int
   */
  public function getTotal() {
    return (int)$this->total;
  }
  
  /**
   * Função para retorno dos campos do Model como propriedades do objeto.
   * Exemplo:
   * $model->nome;  // retorna o campo 'nome' da tabela do model
   * $model->nome->getValue(); // retorna o valor do campo 'nome'; ou
   * $model['nome'];
   * 
   * Também é possível retornar outras propriedades do Model
   * $model->Table // retorna a primeira tabela do model
   * $model->Tables // retorna as tabelas definidas do model
   * $model->Fields // retorna os campos definidos do model
   * 
   * Uso:
   * $model->Table->Fields['nome'] // retorna o campo 'nome' da primeira tabela do model
   * 
   * @param type $name
   * @return type
   * @throws ModelException
   */
  function __get($name) {
    
    if (isset($this->fields[$name])) {
      return $this->fields[$name];
    }
    
    switch ($name) {
      case 'Fields':
        return $this->fields;
      case 'Tables':
        return $this->tables;
      case 'Table':
        return $this->_getFirstTable();
      default:
        throw new ModelException('Propriedade '.$name.' não existe');
    }
  }
  
  /**
   * Inicia uma transação com o banco (autocommit = false)
   * A transação é iniciada apenas uma vez, não importa quantas vezes este método é
   * chamado.
   */
  function startTransaction() {
    if (!$this->transaction) {
      $this->transaction = true;
      $this->transaction_status = (bool)$this->database->autocommit();
      $this->database->autocommit(false);
    }
  }
  
  /**
   * Termina uma transação com o banco (commit ou rollback, dependendo se a operação
   * foi bem sucedida ou não). A transação só é finalizada uma vez, não importa quantas
   * vezes este método é chamado. Este método só efetiva uma transação (commit) APENAS se 
   * o autocommit da conexão atual estivesse em true.
   * @param bool $success
   */
  function endTransaction($success = false) {
    if ($this->transaction) {
      $this->transaction = false;
      
      if ($this->transaction_status == true) {
        if ($success) {
          $this->database->commit();
        } else {
          $this->database->rollback();
        }
      }
      
      $this->database->autocommit((bool)$this->transaction_status);
      $this->transaction_status = null;
    }
  }
  
}